<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

$saleList = [];

$query = "SELECT s.*, u.userName FROM sale s
        LEFT JOIN user u 
        ON s.userId = u.id";

// if (!empty($search)) {
//     $query .= " WHERE i.itemCode LIKE '%$search%' 
//         OR i.itemName LIKE '%$search%' 
//         OR c.categoryName LIKE '%$search%'";
// }

$query .= " ORDER BY s.id DESC";
$query .= " LIMIT $offset, $limit"; // pagination

$result = mysqli_query($conn, $query);
while ($obj = mysqli_fetch_assoc($result)) {
    $saleList[] = $obj;
}
$response = [
    'status' => 200,
    'message' => "Sale List has been successfully retrieved.",
    'data' => $saleList
];
echo json_encode($response);

mysqli_close($conn);