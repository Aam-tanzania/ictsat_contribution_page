<?php

function logMessage($message){
    $logFile = "payment_log.txt";
    $time = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$time] ".$message.PHP_EOL , FILE_APPEND);
}

logMessage("----- New Payment Request -----");

$conn = new mysqli("localhost","root","","ictsat");

if($conn->connect_error){
    logMessage("Database connection failed: ".$conn->connect_error);
    die("DB Error");
}

$name = trim($_POST['name'] ?? '');
$phone = preg_replace('/\D/', '', $_POST['phone'] ?? '');
$rawAmount = $_POST['amount'] ?? '';
$amount = filter_var($rawAmount, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 2147483647]
]);

if (strlen($phone) !== 10) {
    http_response_code(400);
    exit('Please enter a valid 10-digit phone number.');
}

if ($amount === false) {
    http_response_code(400);
    exit('Please enter a valid amount of at least 1 TZS.');
}

if (count(preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY)) < 2) {
    http_response_code(400);
    exit('Please enter your first and last name.');
}

logMessage("User Input - Name: $name , Phone: $phone");

$email = "ictsat@teku.ac.tz";
$transaction_id = 'ICTSAT' . date('YmdHis') . random_int(100, 999);

$data = [
    "transaction_id" => $transaction_id,
    "amount" => $amount,
    "currency" => "TZS",
    "phone" => $phone,
    "name" => $name,
    "email" => $email,
    "callback_url" => "https://tyisha-innovatory-ossie.ngrok-free.dev/ictsat/callback.php",
    "buyer_remarks" => "ICTSAT contribution",
    "merchant_remarks" => "ICTSAT TEKU contribution"
];

logMessage("Sending API Request: ".json_encode($data));

$ch = curl_init();

curl_setopt_array($ch, [
CURLOPT_URL => "https://palmpesa.co.tz/api/v2/async/payments",
CURLOPT_RETURNTRANSFER => true,
CURLOPT_POST => true,
CURLOPT_SSL_VERIFYPEER => false,
CURLOPT_SSL_VERIFYHOST => 2,
CURLOPT_HTTPHEADER => [
"Accept: application/json",
"Authorization: Bearer A3VJc7EJZOcJCIKhwUi7uBmU9Q8b2TuOsDIxpKfA4E5DRzFTHD3AyYYU7WNU",
"Content-Type: application/json"
],
CURLOPT_POSTFIELDS => json_encode($data)
]);

$initiationStartedAt = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s.u');
$initiationClock = hrtime(true);
$response = curl_exec($ch);
$initiationResponseAt = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s.u');
$initiationResponseMs = (int) round((hrtime(true) - $initiationClock) / 1000000);
$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if(curl_errno($ch)){
    logMessage("cURL Error: ".curl_error($ch));
    curl_close($ch);
    http_response_code(502);
    exit('Could not contact the payment service. Please try again.');
}

curl_close($ch);

logMessage("API Response: ".$response);
logMessage("PalmPesa initiation response received after {$initiationResponseMs} ms (HTTP {$httpStatus})");

$result = json_decode($response,true);
$payment = $result['data'] ?? [];

$order_id = $payment['order_id'] ?? null;
$provider_transaction_id = $payment['transaction_id'] ?? $transaction_id;
$payment_status = $payment['status'] ?? 'INITIATING';

if ($httpStatus < 200 || $httpStatus >= 300 || empty($result['success']) || !$order_id) {
    logMessage("Payment initiation rejected. HTTP status: $httpStatus");
    http_response_code(502);
    exit('Payment could not be started. Please check your details and try again.');
}

logMessage("Order ID received: ".$order_id);

$stmt = $conn->prepare("INSERT INTO contributions(contributor_name,phone,email,amount,transaction_id,order_id,payment_status,initiation_started_at,initiation_response_at,initiation_response_ms) VALUES(?,?,?,?,?,?,?,?,?,?)");

if(!$stmt){
    logMessage("Prepare failed: ".$conn->error);
}

$stmt->bind_param("sssisssssi",$name,$phone,$email,$amount,$provider_transaction_id,$order_id,$payment_status,$initiationStartedAt,$initiationResponseAt,$initiationResponseMs);

if($stmt->execute()){
    logMessage("Data inserted successfully");
}else{
    logMessage("DB Insert Error: ".$stmt->error);
}

echo "<main style='font-family:Arial,sans-serif;max-width:520px;margin:10vh auto;padding:32px;border:1px solid #dce6e9;border-radius:16px;color:#14263d'>";
echo "<h2 style='color:#087e8b'>Payment request accepted</h2>";
echo "<p>Follow the payment prompt on your phone to complete your TZS ".number_format($amount)." contribution.</p>";
echo "<p>Order reference: <strong>".htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8')."</strong></p>";
echo "</main>";

logMessage("----- Request Finished -----");

?>
