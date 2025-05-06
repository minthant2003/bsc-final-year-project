<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

if (isset($_GET['id'])) {
    $purchaseOrderId = $_GET['id'];
    $response = [];

    $query = "SELECT * FROM purchase_order WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $purchaseOrderId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) == 1) {
        $status = "Rejected";

        // interact with DB (purchase_order)
        $sql = "UPDATE purchase_order 
                SET status = ?
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $purchaseOrderId);
        if ($stmt->execute()) {
            $response['status'] = 200;
            $response['message'] = "Purchase Order rejected successfully.";
            $stmt->close();
            $conn->close();
            exit(json_encode($response)); // exit code
        }
    } else {
        $response['status'] = 400;
        $response['message'] = "Purchase Order not found.";
        $response['data'] = null;
        $stmt->close();
        $conn->close();
        exit(json_encode($response)); // exit code
    }
}
$conn->close();