<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$categoryList = [];

$query = "SELECT * FROM category
        ORDER BY id DESC";
$result = mysqli_query($conn, $query);
while ($obj = mysqli_fetch_assoc($result)) {
    $categoryList[] = $obj;
}
$response = [
    'status' => 200,
    'message' => "Category List has been successfully retrieved.",
    'data' => $categoryList
];
echo json_encode($response);

mysqli_close($conn);