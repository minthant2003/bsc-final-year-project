<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

// $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

$inStockList = [];

$query = "SELECT ins.*, i.itemName, i.isPerishable FROM instock ins 
        LEFT JOIN item i 
        ON ins.itemId = i.id";

// if (!empty($search)) {
//     $query .= " WHERE i.itemCode LIKE '%$search%' 
//         OR i.itemName LIKE '%$search%' 
//         OR c.categoryName LIKE '%$search%'";
// }

$query .= " ORDER BY ins.id DESC";
$query .= " LIMIT $offset, $limit"; // pagination

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