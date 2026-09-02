<?php

declare(strict_types=1);

header('Content-Type: application/json');

function callbackResponse(int $statusCode, array $body): never
{
    http_response_code($statusCode);
    echo json_encode($body, JSON_UNESCAPED_SLASHES);
    exit;
}

function logCallback(string $message): void
{
    $time = date('Y-m-d H:i:s');
    @file_put_contents(
        __DIR__ . '/payment_log.txt',
        "[$time] Callback: $message" . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    callbackResponse(405, ['received' => false, 'message' => 'Method not allowed.']);
}

$rawBody = file_get_contents('php://input') ?: '';
$timestamp = (string) ($_SERVER['HTTP_X_PALMPESA_TIMESTAMP'] ?? '');
$signature = (string) ($_SERVER['HTTP_X_PALMPESA_SIGNATURE'] ?? '');
$headerEventId = (string) ($_SERVER['HTTP_X_PALMPESA_EVENT_ID'] ?? '');
$signingSecret = (string) (getenv('PALMPESA_WEBHOOK_SIGNING_SECRET') ?: '');

// Log receipt before validation so delivery attempts remain visible while debugging.
logCallback(
    'received request; event_id=' . ($headerEventId !== '' ? $headerEventId : 'missing')
    . '; bytes=' . strlen($rawBody)
);

// Require and verify PalmPesa's HMAC whenever the shared signing secret is set.
// An unset secret permits unsigned callbacks only for the current test environment.
if ($signingSecret !== '') {
    if ($timestamp === '' || !ctype_digit($timestamp) || !str_starts_with($signature, 'sha256=')) {
        logCallback('rejected: missing or malformed signature headers');
        callbackResponse(401, ['received' => false, 'message' => 'Missing callback signature.']);
    }

    if (abs(time() - (int) $timestamp) > 300) {
        logCallback('rejected: callback timestamp outside replay window');
        callbackResponse(401, ['received' => false, 'message' => 'Expired callback timestamp.']);
    }

    $expectedSignature = 'sha256=' . hash_hmac(
        'sha256',
        $timestamp . '.' . $rawBody,
        $signingSecret
    );

    if (!hash_equals($expectedSignature, $signature)) {
        logCallback('rejected: invalid signature');
        callbackResponse(401, ['received' => false, 'message' => 'Invalid callback signature.']);
    }
}

try {
    $data = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $exception) {
    logCallback('rejected: invalid JSON');
    callbackResponse(400, ['received' => false, 'message' => 'Invalid JSON payload.']);
}

$event = (string) ($data['event'] ?? '');
$eventId = (string) ($data['event_id'] ?? '');
$transactionId = (string) ($data['transaction_id'] ?? '');
$orderId = (string) ($data['order_id'] ?? '');
$status = strtoupper((string) ($data['status'] ?? ''));
$amount = filter_var($data['amount'] ?? null, FILTER_VALIDATE_INT);
$currency = strtoupper((string) ($data['currency'] ?? ''));

if (
    $eventId === '' || $orderId === '' || $transactionId === '' || $status === ''
    || $amount === false || $currency === ''
) {
    logCallback('rejected: required callback fields missing');
    callbackResponse(422, ['received' => false, 'message' => 'Required callback fields are missing.']);
}

if ($headerEventId !== '' && !hash_equals($eventId, $headerEventId)) {
    logCallback("rejected: header/body event_id mismatch for $eventId");
    callbackResponse(422, ['received' => false, 'message' => 'Callback event ID mismatch.']);
}

$eventStatuses = [
    'payment.pending' => 'PENDING',
    'payment.completed' => 'COMPLETED',
    'payment.cancelled' => 'CANCELLED',
    'payment.rejected' => 'REJECTED',
];

if (!isset($eventStatuses[$event]) || $eventStatuses[$event] !== $status) {
    logCallback("rejected: inconsistent event=$event status=$status");
    callbackResponse(422, ['received' => false, 'message' => 'Unsupported or inconsistent payment event.']);
}

if ($currency !== 'TZS') {
    logCallback("rejected: unsupported currency=$currency");
    callbackResponse(409, ['received' => false, 'message' => 'Payment currency does not match.']);
}

$conn = new mysqli('localhost', 'root', '', 'ictsat');
if ($conn->connect_error) {
    logCallback('database connection failed: ' . $conn->connect_error);
    callbackResponse(500, ['received' => false, 'message' => 'Database connection failed.']);
}

$stmt = $conn->prepare(
    'SELECT id, amount, payment_status FROM contributions '
    . 'WHERE order_id = ? AND transaction_id = ? LIMIT 1'
);

if (!$stmt) {
    logCallback('lookup prepare failed: ' . $conn->error);
    callbackResponse(500, ['received' => false, 'message' => 'Could not process callback.']);
}

$stmt->bind_param('ss', $orderId, $transactionId);
$stmt->execute();
$stmt->bind_result($contributionId, $storedAmount, $storedStatus);
$paymentExists = $stmt->fetch();
$stmt->close();

if (!$paymentExists) {
    $conn->close();
    logCallback("rejected: transaction not found; transaction_id=$transactionId; order_id=$orderId");
    callbackResponse(404, ['received' => false, 'message' => 'Payment transaction not found.']);
}

if ((int) $storedAmount !== $amount) {
    $conn->close();
    logCallback("rejected: amount mismatch; expected=$storedAmount; received=$amount");
    callbackResponse(409, ['received' => false, 'message' => 'Payment amount does not match.']);
}

// A completed payment is final and cannot be downgraded by a later callback.
if (strtoupper((string) $storedStatus) !== 'COMPLETED' || $status === 'COMPLETED') {
    $stmt = $conn->prepare('UPDATE contributions SET payment_status = ? WHERE id = ?');

    if (!$stmt) {
        logCallback('update prepare failed: ' . $conn->error);
        callbackResponse(500, ['received' => false, 'message' => 'Could not process callback.']);
    }

    $stmt->bind_param('si', $status, $contributionId);
    $updated = $stmt->execute();
    $stmt->close();

    if (!$updated) {
        $conn->close();
        logCallback("update failed; event_id=$eventId");
        callbackResponse(500, ['received' => false, 'message' => 'Could not update payment status.']);
    }
}

$conn->close();

logCallback(
    "accepted; event=$event; event_id=$eventId; transaction_id=$transactionId; "
    . "order_id=$orderId; status=$status"
);

// PalmPesa requires this acknowledgement body with an HTTP 200 response.
callbackResponse(200, ['received' => true]);
