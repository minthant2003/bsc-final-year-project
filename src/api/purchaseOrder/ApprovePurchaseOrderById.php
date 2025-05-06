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
        $status = "Approved";

        // interact with DB (purchase_order)
        $sql = "UPDATE purchase_order 
                SET status = ?
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $purchaseOrderId);
        if ($stmt->execute()) {
            // interact with DB (purchase_order_item)
            $query = "SELECT * FROM purchase_order_item WHERE purchaseOrderId = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $purchaseOrderId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
                while ($obj = mysqli_fetch_assoc($result)) {
                    // send request to AddInStock.php using cURL
                    $request = [];
                    $request['itemId'] = $obj['itemId'];
                    $request['quantity'] = $obj['quantity'];

                    $url = "http://localhost/finalYearProjectServer/src/api/inStock/AddInStock.php";
                    $payload = json_encode($request);
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json'
                    ]);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
                    $res = curl_exec($curl);
                    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);
                }

                $response['status'] = 200;
                $response['message'] = "Purchase Order approved successfully.";
                $stmt->close();
                $conn->close();
                exit(json_encode($response)); // exit code
            }
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