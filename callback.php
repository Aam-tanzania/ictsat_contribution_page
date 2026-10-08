<?php

function callbackLog($message) {
    $timestamp = gmdate('Y-m-d H:i:s');
    file_put_contents(__DIR__ . '/payment_log.txt', "[$timestamp UTC] CALLBACK $message" . PHP_EOL, FILE_APPEND | LOCK_EX);
}

$receivedAt = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s.u');
$rawPayload = file_get_contents('php://input');
$data = json_decode($rawPayload, true);
$callbackData = is_array($data) ? ($data['data'] ?? $data) : [];
$orderId = is_array($callbackData) ? (string)($callbackData['order_id'] ?? '') : '';
$status = is_array($callbackData)
    ? (string)($callbackData['payment_status'] ?? $callbackData['status'] ?? '')
    : '';
callbackLog('received order_id=' . ($orderId !== '' ? $orderId : '(missing)') .
    ' status=' . ($status !== '' ? $status : '(missing)') .
    ' payload_bytes=' . strlen($rawPayload));

$conn = new mysqli('localhost', 'root', '', 'ictsat');
if ($conn->connect_error) {
    callbackLog('database connection failed: ' . $conn->connect_error);
    http_response_code(500);
    exit('Database unavailable');
}

$contributionId = null;
$latencyMs = null;
if ($orderId !== '') {
    $lookup = $conn->prepare(
        'SELECT id, GREATEST(TIMESTAMPDIFF(MICROSECOND, initiation_started_at, ?), 0) DIV 1000 AS latency_ms
         FROM contributions WHERE order_id = ? LIMIT 1'
    );
    if ($lookup) {
        $lookup->bind_param('ss', $receivedAt, $orderId);
        $lookup->execute();
        $match = $lookup->get_result()->fetch_assoc();
        if ($match) {
            $contributionId = (int)$match['id'];
            $latencyMs = $match['latency_ms'] !== null ? (int)$match['latency_ms'] : null;
        }
        $lookup->close();
    }
}

$log = $conn->prepare(
    'INSERT INTO payment_callback_logs (order_id, callback_status, payload, received_at, contribution_id, initiation_to_callback_ms)
     VALUES (?, ?, ?, ?, ?, ?)'
);
if (!$log) {
    callbackLog('could not prepare callback log insert: ' . $conn->error);
    http_response_code(500);
    exit('Could not record callback');
}
$log->bind_param('ssssii', $orderId, $status, $rawPayload, $receivedAt, $contributionId, $latencyMs);
if (!$log->execute()) {
    callbackLog('callback log insert failed: ' . $log->error);
    http_response_code(500);
    exit('Could not record callback');
}
$callbackLogId = $log->insert_id;
$log->close();

if ($orderId === '' || $status === '') {
    callbackLog("saved invalid payload as log #$callbackLogId");
    http_response_code(400);
    exit('Invalid callback payload');
}

$update = $conn->prepare(
    'UPDATE contributions SET payment_status = ?, callback_received_at = ?, callback_latency_ms = ? WHERE order_id = ?'
);
if (!$update) {
    callbackLog('could not prepare payment update: ' . $conn->error);
    http_response_code(500);
    exit('Could not update payment');
}
$update->bind_param('ssis', $status, $receivedAt, $latencyMs, $orderId);
if (!$update->execute()) {
    callbackLog('payment update failed for ' . $orderId . ': ' . $update->error);
    http_response_code(500);
    exit('Could not update payment');
}

if ($update->affected_rows === 0 && $contributionId === null) {
    callbackLog("saved callback log #$callbackLogId but no contribution matched order_id=$orderId");
} else {
    callbackLog("saved callback log #$callbackLogId contribution_id=" . ($contributionId ?? 'unknown') .
        ' initiation_to_callback_ms=' . ($latencyMs !== null ? $latencyMs : 'unavailable'));
}

$update->close();
$conn->close();
echo 'Callback received';

?>
