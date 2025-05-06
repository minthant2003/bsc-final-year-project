<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

if (isset($_GET['categoryId'])) {
    $categoryId = intval($_GET['categoryId']);
    $itemList = [];
    
    $query = "SELECT i.*, c.categoryName 
            FROM item i
            LEFT JOIN category c 
            ON i.categoryId = c.id
            WHERE i.categoryId = ?
            ORDER BY i.id DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $categoryId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($obj = mysqli_fetch_assoc($result)) {
        $itemList[] = $obj;
    }
    $response = [
        'status' => 200,
        'message' => "Item List has been successfully retrieved.",
        'data' => $itemList
    ];
    echo json_encode($response);
}

mysqli_close($conn);