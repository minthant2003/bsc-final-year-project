<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$saleList = [];

$query = "SELECT s.*, u.userName FROM sale s
        LEFT JOIN user u 
        ON s.userId = u.id
        ORDER BY s.id DESC";
$result = mysqli_query($conn, $query);
while ($obj = mysqli_fetch_assoc($result)) {
    $saleList[] = $obj;
}
$response = [
    'status' => 200,
    'message' => "Sale List for export has been successfully retrieved.",
    'data' => $saleList
];
echo json_encode($response);

mysqli_close($conn);