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

$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';

logMessage("User Input - Name: $name , Phone: $phone");

$email = "ictsat@teku.ac.tz";
$amount = 1000;
$transaction_id = time().rand(100,999);

$address = "Mbeya";
$postcode = "53000";

$data = [
"name"=>$name,
"email"=>$email,
"phone"=>$phone,
"amount"=>$amount,
"transaction_id"=>$transaction_id,
"address"=>$address,
"postcode"=>$postcode,
"callback_url"=>"https://tyisha-innovatory-ossie.ngrok-free.dev/ictsat/callback.php"
];

logMessage("Sending API Request: ".json_encode($data));

$ch = curl_init();

curl_setopt_array($ch, [
CURLOPT_URL => "https://palmpesa.drmlelwa.co.tz/api/palmpesa/initiate",
CURLOPT_RETURNTRANSFER => true,
CURLOPT_POST => true,
CURLOPT_SSL_VERIFYPEER => false,
CURLOPT_SSL_VERIFYHOST => false,
CURLOPT_HTTPHEADER => [
"Authorization: Bearer UgGnf1bYJb1vC8MoZXa7LDXcWS6sA7mxWR12MaPgr05kDowvakyzP6jBLsbs",
"Content-Type: application/json"
],
CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($ch);

if(curl_errno($ch)){
    logMessage("cURL Error: ".curl_error($ch));
}

curl_close($ch);

logMessage("API Response: ".$response);

$result = json_decode($response,true);

$order_id = $result['order_id'] ?? null;

logMessage("Order ID received: ".$order_id);

$stmt = $conn->prepare("INSERT INTO contributions(contributor_name,phone,email,amount,transaction_id,order_id) VALUES(?,?,?,?,?,?)");

if(!$stmt){
    logMessage("Prepare failed: ".$conn->error);
}

$stmt->bind_param("sssiss",$name,$phone,$email,$amount,$transaction_id,$order_id);

if($stmt->execute()){
    logMessage("Data inserted successfully");
}else{
    logMessage("DB Insert Error: ".$stmt->error);
}

echo "<h3>Payment request sent. Please complete payment on your phone.</h3>";

logMessage("----- Request Finished -----");

?>