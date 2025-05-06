<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$sql = "
    SELECT 
        (SELECT COUNT(*) FROM item) AS total,
        (SELECT COUNT(*) FROM item WHERE isPerishable = 1) AS perishable,
        (SELECT COUNT(*) FROM item WHERE isPerishable = 0) AS nonPerishable
";

$result = $conn->query($sql);
$data = $result->fetch_assoc();

$response = [
    'status' => 200,
    'message' => "Item Perishability Per retrieved successfully.",
    'data' => $data,
];

echo json_encode($response);
$conn->close();
