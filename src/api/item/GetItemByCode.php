<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

if (isset($_GET['itemCode'])) {
    $itemCode = $_GET['itemCode'];
    $response = [];

    $query = "SELECT * FROM item WHERE itemCode = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $itemCode);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) == 1) {
        $item = mysqli_fetch_assoc($result);
        $response['status'] = 200;
        $response['message'] = "Item has been successfully retrieved.";
        $response['data'] = $item;
        $stmt->close();
        $conn->close();
        exit(json_encode($response)); // exit code
    } else {
        $response['status'] = 400;
        $response['message'] = "Item not found.";
        $response['data'] = null;
        $stmt->close();
        $conn->close();
        exit(json_encode($response)); // exit code
    }
}
$conn->close();