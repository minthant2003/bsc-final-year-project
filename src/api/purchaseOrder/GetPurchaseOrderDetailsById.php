<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $response = [];
    $response['data'] = null;

    // get data from purchase_order
    $query = "SELECT * FROM purchase_order WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) == 1) {
        $purchaseOrder = mysqli_fetch_assoc($result); // main purchase order obj
        $purchaseOrder['purchaseOrderItems'] = []; // custom purchase order items list

        // get data from purchase_order_item
        $query = "SELECT * FROM purchase_order_item WHERE purchaseOrderId = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $supplierId = null; // common supplier id

            while ($obj = mysqli_fetch_assoc($result)) {
                $purchaseOrderItem = null;
                $itemId = $obj['itemId'];
                $quantity = $obj['quantity'];
                $supplierId = $obj['supplierId'];

                // fetch item by id
                $url = "http://localhost/finalYearProjectServer/src/api/item/GetItemById.php?id=" . $itemId;
                $resObj = json_decode(file_get_contents($url), true);
                if ($resObj && $resObj['status'] === 200) {
                    $item = $resObj['data'];
                    $purchaseOrderItem['itemId'] = $itemId;
                    $purchaseOrderItem['itemCode'] = $item['itemCode'];
                    $purchaseOrderItem['itemName'] = $item['itemName'];
                    $purchaseOrderItem['quantity'] = $quantity;
                    $purchaseOrder['purchaseOrderItems'][] = $purchaseOrderItem; // append order item to the list
                } else {
                    $response['message'] = "Error while fetching Item!";
                    $conn->close();
                    exit(json_encode($response)); // exit code
                }
            }

            // fetch supplier by id
            $url = "http://localhost/finalYearProjectServer/src/api/supplier/GetSupplierById.php?id=" . $supplierId;
            $resObj = json_decode(file_get_contents($url), true);
            if ($resObj && $resObj['status'] === 200) {
                $supplier = $resObj['data'];
                $purchaseOrder['supplierId'] = $supplier['id'];
                $purchaseOrder['supplierName'] = $supplier['supplierName']; // append supplier info to the main obj
            } else {
                $response['message'] = "Error while fetching Supplier!";
                $conn->close();
                exit(json_encode($response)); // exit code
            }

            $response['status'] = 200;
            $response['message'] = "Purchase Order Details has been successfully retrieved.";
            $response['data'] = $purchaseOrder;
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