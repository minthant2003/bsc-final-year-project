<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$userList = [];

$query = "SELECT u.*, r.roleName FROM user u
        INNER JOIN role r ON u.roleId = r.id
        ORDER BY u.id DESC";
$result = mysqli_query($conn, $query);
while ($obj = mysqli_fetch_assoc($result)) {
    $userList[] = $obj;
}
$response = [
    'status' => 200,
    'message' => "User List has been successfully retrieved.",
    'data' => $userList
];
echo json_encode($response);

mysqli_close($conn);