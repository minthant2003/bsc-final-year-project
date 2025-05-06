<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$inStockList = [];

$query = "SELECT ins.*, i.itemName, i.isPerishable FROM instock ins 
        LEFT JOIN item i 
        ON ins.itemId = i.id 
        ORDER BY ins.id DESC";
$result = mysqli_query($conn, $query);
while ($obj = mysqli_fetch_assoc($result)) {
    $inStockList[] = $obj;
}
$response = [
    'status' => 200,
    'message' => "In Stock List has been successfully retrieved.",
    'data' => $inStockList
];
echo json_encode($response);

mysqli_close($conn);