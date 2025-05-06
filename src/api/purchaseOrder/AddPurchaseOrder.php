<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$request = json_decode(file_get_contents("php://input"), true);

if (isset($request['itemObjList'], $request['purchaseOrderCode'], 
    $request['remark'], $request['supplierId'])) {

    $response = ['status' => 300]; // default error status
    $mandatoryFields = [
        'purchaseOrderCode' => 'Purchase Order Code is mandatory!',
        'supplierId' => 'Supplier is mandatory!',
    ];
    // step 1.1. validation (traditional)
    foreach ($mandatoryFields as $field => $errMsg) {
        if (!isset($request[$field]) || $request[$field] === "") { // allows 0 or false
            $response['message'] = $errMsg;
            $conn->close();
            exit(json_encode($response)); // exit code
        }
    }
    // step 1.2. validation (for itemObjList length)
    if (count($request['itemObjList']) === 0) {
        $response['message'] = 'At least one item is mandatory!';
        $conn->close();
        exit(json_encode($response));
    }

    // Interact with DB
    $itemObjList = $request['itemObjList'];
    $purchaseOrderCode = $request['purchaseOrderCode'];
    $status = "Pending"; // pending status for purchase order
    $remark = empty($request['remark']) ? NULL : $request['remark'];
    $supplierId = $request['supplierId'];
    $createdAt = date("Y-m-d H:i:s"); // current datetime

    // step 2. check if same purchase order code exists
    $query = "SELECT * FROM purchase_order WHERE purchaseOrderCode = '$purchaseOrderCode'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $response['message'] = 'Same Purchase Order Code already exists!';
        $conn->close();
        exit(json_encode($response)); // exit code
    }

    // step 3.1. insert into DB (purchase_order)
    $sql = "INSERT INTO purchase_order (purchaseOrderCode, status, remark, createdAt)
        VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $purchaseOrderCode, $status, $remark, $createdAt);
    if ($stmt->execute()) {
        $url = "http://localhost/finalYearProjectServer/src/api/purchaseOrder/GetPurchaseOrderByCode.php?purchaseOrderCode=" . $purchaseOrderCode;
        $resObj = json_decode(file_get_contents($url), true);
        if ($resObj && $resObj['status'] === 200) {
            $purchaseOrder = $resObj['data'];
            $purchaseOrderId = $purchaseOrder['id'];
    
            // step 3.2. insert into DB (purchase_order_item)
            $sql = "INSERT INTO purchase_order_item 
                    (purchaseOrderId, itemId, quantity, supplierId, remark, createdAt)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            foreach ($itemObjList as $itemObj) {
                $itemId = $itemObj['itemId'];
                $quantity = $itemObj['quantity'];
                $stmt->bind_param("iiiiss", $purchaseOrderId, $itemId, $quantity, $supplierId, $remark, $createdAt);
                $stmt->execute();
            }

            // send email noti to the supplier
            $url = "http://localhost/finalYearProjectServer/src/api/emailNoti/SendPurchaseOrderNotiToSupplier.php?supplierId=$supplierId&purchaseOrderCode=$purchaseOrderCode";
            $resData = json_decode(file_get_contents($url), true);
            if (!$resData || $resData['status'] !== 200) {
                $response['status'] = 301;
                $response['message'] = "Failed to send email noti to the supplier!";
                $conn->close();
                exit(json_encode($response));
            }

            $response = [
                'status' => 200,
                'message' => 'Purchase Order has been made!',
            ];
            $stmt->close();
            $conn->close();
            exit(json_encode($response)); // exit code
        } else {
            $response['message'] = "Error while fetching Purchase Order!";
            $conn->close();
            exit(json_encode($response)); // exit code
        }
    }
}