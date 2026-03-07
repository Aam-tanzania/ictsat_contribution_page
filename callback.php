<?php

$conn = new mysqli("localhost","root","","ictsat");

$data = json_decode(file_get_contents("php://input"), true);

$order_id = $data['order_id'];
$status = $data['payment_status'];

$stmt = $conn->prepare("UPDATE contributions SET payment_status=? WHERE order_id=?");
$stmt->bind_param("ss",$status,$order_id);
$stmt->execute();

echo "Callback received";

?>