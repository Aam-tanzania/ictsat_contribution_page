<?php

$conn = new mysqli("localhost","root","","ictsat");

if($conn->connect_error){
    die("Database connection failed");
}

$result = $conn->query("SELECT * FROM contributions ORDER BY id DESC");
$callbackLogs = $conn->query("SELECT * FROM payment_callback_logs ORDER BY id DESC LIMIT 100");

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>ICTSAT Contributions Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f5f7fb;
}

.header{
font-weight:700;
color:#0d6efd;
}

.status-paid{
color:green;
font-weight:bold;
}

.status-pending{
color:orange;
font-weight:bold;
}

.status-failed{
color:red;
font-weight:bold;
}

</style>

</head>

<body>

<div class="container mt-5">

<h3 class="text-center header mb-4">
ICTSAT Club Contributions (TEKU)
</h3>

<div class="card shadow">

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>
<th>ID</th>
<th>Name</th>
<th>Phone</th>
<th>Amount</th>
<th>Transaction ID</th>
<th>Order ID</th>
<th>Status</th>
<th>Date</th>
<th>Initiated (UTC)</th>
<th>PalmPesa response (ms)</th>
<th>Callback received (UTC)</th>
<th>Initiation to callback (ms)</th>
</tr>

</thead>

<tbody>

<?php while($row = $result->fetch_assoc()){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo htmlspecialchars($row['contributor_name']); ?></td>

<td><?php echo htmlspecialchars($row['phone']); ?></td>

<td><?php echo $row['amount']; ?> TSH</td>

<td><?php echo htmlspecialchars($row['transaction_id'] ?? ''); ?></td>

<td><?php echo htmlspecialchars($row['order_id'] ?? ''); ?></td>

<td>

<?php

$status = $row['payment_status'];

if($status == "PAID"){
echo "<span class='status-paid'>PAID</span>";
}
elseif($status == "PENDING"){
echo "<span class='status-pending'>PENDING</span>";
}
else{
echo "<span class='status-failed'>" . htmlspecialchars($status) . "</span>";
}

?>

</td>

<td><?php echo $row['created_at']; ?></td>
<td><?php echo htmlspecialchars($row['initiation_started_at'] ?? '—'); ?></td>
<td><?php echo $row['initiation_response_ms'] !== null ? number_format((int)$row['initiation_response_ms']) : '—'; ?></td>
<td><?php echo htmlspecialchars($row['callback_received_at'] ?? '—'); ?></td>
<td><?php echo $row['callback_latency_ms'] !== null ? number_format((int)$row['callback_latency_ms']) : '—'; ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

<div class="card shadow mt-4">
<div class="card-body">
<h5 class="mb-3">Callback delivery log <small class="text-muted">(latest 100, times in UTC)</small></h5>
<div class="table-responsive">
<table class="table table-sm table-striped align-middle">
<thead class="table-dark"><tr><th>Received at (UTC)</th><th>Order ID</th><th>Status</th><th>Initiation to callback (ms)</th><th>Payload</th></tr></thead>
<tbody>
<?php while($log = $callbackLogs->fetch_assoc()){ ?>
<tr>
<td><?php echo htmlspecialchars($log['received_at']); ?></td>
<td><?php echo htmlspecialchars($log['order_id'] ?? ''); ?></td>
<td><?php echo htmlspecialchars($log['callback_status'] ?? ''); ?></td>
<td><?php echo $log['initiation_to_callback_ms'] !== null ? number_format((int)$log['initiation_to_callback_ms']) : '—'; ?></td>
<td><details><summary>View payload</summary><pre class="small mb-0" style="max-width:480px;white-space:pre-wrap"><?php echo htmlspecialchars($log['payload']); ?></pre></details></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</div>
</div>

</div>
</body>
</html>
