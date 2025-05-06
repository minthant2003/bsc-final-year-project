<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$roleList = [];

$query = "SELECT * FROM role
        ORDER BY id DESC";
$result = mysqli_query($conn, $query);
while ($obj = mysqli_fetch_assoc($result)) {
    $roleList[] = $obj;
}
$response = [
    'status' => 200,
    'message' => "Role List has been successfully retrieved.",
    'data' => $roleList
];
echo json_encode($response);

mysqli_close($conn);