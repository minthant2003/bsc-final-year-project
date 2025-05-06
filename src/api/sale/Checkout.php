<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$request = json_decode(file_get_contents("php://input"), true);

if (isset($request['items'], $request['paymentMethod'], $request['remark'], $request['userId'])) {
    $total = 0; // update total later*
    $items = $request['items'];
    $paymentMethod = $request['paymentMethod'];
    $remark = empty($request['remark']) ? NULL : $request['remark'];
    $createdAt = date("Y-m-d H:i:s"); // current datetime
    $userId = (int)$request['userId'];

    // step 1.1. insert into DB (sale) -> but update total later*
    $sql = "INSERT INTO sale (total, paymentMethod, remark, createdAt, userId)
        VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dsssi", $total, $paymentMethod, $remark, $createdAt, $userId);
    if ($stmt->execute()) {
        $url = "http://localhost/finalYearProjectServer/src/api/sale/GetSaleByCreatedAt.php?createdAt=" . urlencode($createdAt);
        $resObj = json_decode(file_get_contents($url), true);
        if ($resObj && $resObj['status'] === 200) {
            $sale = $resObj['data'];
            $saleId = $sale['id'];

            // step 1.2. insert into DB (sale_item)
            $sql = "INSERT INTO sale_item 
                    (saleId, itemId, quantity, itemPriceAtTheTime, itemDiscountAtTheTime, remark, createdAt)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            foreach ($items as $item) {
                $itemCode = $item['itemCode'];
                $quantity = $item['quantity'];

                $url = "http://localhost/finalYearProjectServer/src/api/item/GetItemByCode.php?itemCode=" . $itemCode;
                $resObj = json_decode(file_get_contents($url), true);
                if ($resObj && $resObj['status'] === 200) {
                    $item = $resObj['data'];
                    $itemId = $item['id'];
                    $itemPriceAtTheTime = $item['price'];
                    $itemDiscountAtTheTime = $item['discountPercentage'];
                    
                    $stmt->bind_param("iiiddss", $saleId, $itemId, $quantity, $itemPriceAtTheTime, $itemDiscountAtTheTime, $remark, $createdAt);
                    $stmt->execute();

                    // step 1.3. call reduce in stock api
                    $url = "http://localhost/finalYearProjectServer/src/api/inStock/ReduceInStockForCheckout.php?itemId=$itemId&quantity=$quantity";
                    $resData = json_decode(file_get_contents($url), true);
                    if (!$resData || $resData['status'] !== 200) {
                        $response['status'] = 301;
                        $response['message'] = "Failed to reduce stock for item ID: $itemId";
                        $conn->close();
                        exit(json_encode($response));
                    }

                    // calculate total for sale item update
                    $total = $total + ($itemPriceAtTheTime * $quantity);
                } else {
                    $response['status'] = 300;
                    $response['message'] = "Error while fetching Item!";
                    $conn->close();
                    exit(json_encode($response)); // exit code
                }
            }

            // step 2. update total at sale after sale_item insert
            $sql = "UPDATE sale SET total = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("di", $total, $saleId);
            $stmt->execute();

            $response = [
                'status' => 200,
                'message' => 'Sale has been made!',
            ];
            $stmt->close();
            $conn->close();
            exit(json_encode($response)); // exit code
        } else {
            $response['status'] = 300;
            $response['message'] = "Error while fetching Sale!";
            $conn->close();
            exit(json_encode($response)); // exit code
        }
    }
}

// logic I: insert DB tables [sale, sale_item]
// logic II: reduce in stock