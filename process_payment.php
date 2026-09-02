<?php

declare(strict_types=1);

function logMessage(string $message): void
{
    $logFile = __DIR__ . '/payment_log.txt';
    $time = date('Y-m-d H:i:s');
    // Logging must never corrupt the HTTP response if the log is not writable.
    @file_put_contents($logFile, "[$time] $message" . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function failPayment(string $message, int $statusCode = 400): never
{
    http_response_code($statusCode);
    logMessage($message);
    echo '<h3>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</h3>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    failPayment('Method not allowed.', 405);
}

logMessage('----- New Payment Request -----');

$name = trim((string) ($_POST['name'] ?? ''));
$phone = preg_replace('/\D+/', '', (string) ($_POST['phone'] ?? ''));

if (strlen($phone) === 9 && !str_starts_with($phone, '0')) {
    $phone = '0' . $phone;
} elseif (strlen($phone) === 12 && str_starts_with($phone, '255')) {
    $phone = '0' . substr($phone, 3);
}

if ($name === '' || !preg_match('/^0\d{9}$/', $phone)) {
    failPayment('A name and valid Tanzanian phone number are required.');
}

$email = 'ictsat@teku.ac.tz';
$amount = 200;
$currency = 'TZS';
$transactionId = date('YmdHis') . random_int(100000, 999999);

// Set these environment variables in production to change credentials/URLs
// without editing application code.
$apiToken = getenv('PALMPESA_API_TOKEN') ?: 'A3VJc7EJZOcJCIKhwUi7uBmU9Q8b2TuOsDIxpKfA4E5DRzFTHD3AyYYU7WNU';
$callbackUrl = getenv('PALMPESA_CALLBACK_URL') ?: 'https://tyisha-innovatory-ossie.ngrok-free.dev/ictsat/callback.php';

$payload = [
    'transaction_id' => $transactionId,
    'amount' => $amount,
    'currency' => $currency,
    'phone' => $phone,
    'name' => $name,
    'email' => $email,
    'callback_url' => $callbackUrl,
    'buyer_remarks' => 'ICTSAT contribution',
    'merchant_remarks' => 'ICTSAT contribution ' . $transactionId,
];

logMessage('Sending PalmPesa v2 request: ' . json_encode($payload, JSON_UNESCAPED_SLASHES));

$ch = curl_init('https://palmpesa.drmlelwa.co.tz/api/v2/palmpesa/payments');
$curlOptions = [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_TIMEOUT => 45,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiToken,
        'Accept: application/json',
        'Content-Type: application/json',
    ],
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
];

curl_setopt_array($ch, $curlOptions);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpStatus = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
    logMessage('PalmPesa cURL error: ' . $curlError);
    failPayment('Unable to contact the payment service. Please try again.', 502);
}

logMessage("PalmPesa response (HTTP $httpStatus): $response");

try {
    $result = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $exception) {
    failPayment('The payment service returned an invalid response.', 502);
}

if ($httpStatus < 200 || $httpStatus >= 300 || empty($result['success'])) {
    $apiMessage = is_string($result['message'] ?? null)
        ? $result['message']
        : 'The payment request was rejected.';
    failPayment($apiMessage, 502);
}

$orderId = (string) ($result['data']['order_id'] ?? '');
$providerTransactionId = (string) ($result['data']['transaction_id'] ?? $transactionId);
$paymentStatus = (string) ($result['data']['status'] ?? 'PENDING');

if ($orderId === '' || $providerTransactionId === '') {
    failPayment('The payment service did not return the required payment identifiers.', 502);
}

$conn = new mysqli('localhost', 'root', '', 'ictsat');
if ($conn->connect_error) {
    failPayment('Database connection failed.', 500);
}

$stmt = $conn->prepare(
    'INSERT INTO contributions '
    . '(contributor_name, phone, email, amount, transaction_id, order_id, payment_status) '
    . 'VALUES (?, ?, ?, ?, ?, ?, ?)'
);

if (!$stmt) {
    failPayment('Could not save the payment request.', 500);
}

$stmt->bind_param('sssisss', $name, $phone, $email, $amount, $providerTransactionId, $orderId, $paymentStatus);

if (!$stmt->execute()) {
    failPayment('Could not save the payment request.', 500);
}

$stmt->close();
$conn->close();

logMessage("Payment saved: transaction_id=$providerTransactionId, order_id=$orderId, status=$paymentStatus");
logMessage('----- Request Finished -----');

echo '<h3>Payment request sent. Please complete payment on your phone.</h3>';
