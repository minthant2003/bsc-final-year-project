<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$itemList = [];

$query = "SELECT i.*, c.categoryName FROM item i
        LEFT JOIN category c 
        ON i.categoryId = c.id
        ORDER BY i.id DESC";
$result = mysqli_query($conn, $query);
while ($obj = mysqli_fetch_assoc($result)) {
    $itemList[] = $obj;
}
$response = [
    'status' => 200,
    'message' => "Item List for export has been successfully retrieved.",
    'data' => $itemList
];
echo json_encode($response);

mysqli_close($conn);