<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$supplierList = [];

$query = "SELECT * FROM supplier
        ORDER BY id DESC";
$result = mysqli_query($conn, $query);
while ($obj = mysqli_fetch_assoc($result)) {
    $supplierList[] = $obj;
}
$response = [
    'status' => 200,
    'message' => "Supplier List has been successfully retrieved.",
    'data' => $supplierList
];
echo json_encode($response);

mysqli_close($conn);