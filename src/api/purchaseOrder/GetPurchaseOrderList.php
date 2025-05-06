<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$purchaseOrderList = [];

$query = "SELECT * FROM purchase_order
        ORDER BY id DESC";
$result = mysqli_query($conn, $query);
while ($obj = mysqli_fetch_assoc($result)) {
    $purchaseOrderList[] = $obj;
}
$response = [
    'status' => 200,
    'message' => "Purchase Order List has been successfully retrieved.",
    'data' => $purchaseOrderList
];
echo json_encode($response);

mysqli_close($conn);