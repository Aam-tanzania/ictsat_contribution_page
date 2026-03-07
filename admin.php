<?php

$conn = new mysqli("localhost","root","","ictsat");

if($conn->connect_error){
    die("Database connection failed");
}

$result = $conn->query("SELECT * FROM contributions ORDER BY id DESC");

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
</tr>

</thead>

<tbody>

<?php while($row = $result->fetch_assoc()){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo htmlspecialchars($row['contributor_name']); ?></td>

<td><?php echo $row['phone']; ?></td>

<td><?php echo $row['amount']; ?> TSH</td>

<td><?php echo $row['transaction_id']; ?></td>

<td><?php echo $row['order_id']; ?></td>

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
echo "<span class='status-failed'>$status</span>";
}

?>

</td>

<td><?php echo $row['created_at']; ?></td>

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