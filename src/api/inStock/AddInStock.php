<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$request = json_decode(file_get_contents("php://input"), true);

if (isset($request['itemId'], $request['quantity'])) {
    $response = ['status' => 300]; // default error status
    $mandatoryFields = [
        'itemId' => 'Item is mandatory!',
        'quantity' => 'Item Quantity is mandatory!',
    ];
    // step 1 validation (traditional)
    foreach ($mandatoryFields as $field => $errMsg) {
        if (!isset($request[$field]) || $request[$field] === "") { // allows 0 or false
            $response['message'] = $errMsg;
            $conn->close();
            exit(json_encode($response)); // exit code
        }
    }

    // Interact with DB
    $itemId = intval($request['itemId']);
    $quantity = $request['quantity'];
    $expireDate = null;
    $createdAt = date("Y-m-d H:i:s"); // current datetime

    // step 2 fetch item by itemId, and consider expire date
    $url = "http://localhost/finalYearProjectServer/src/api/item/GetItemById.php?id=" . $itemId;
    $resObj = json_decode(file_get_contents($url), true);
    if ($resObj && $resObj['status'] === 200) {
        $item = $resObj['data'];
        if ((int)$item['isPerishable'] === 1) {
            $expireGapInDay = isset($item['expireGapInDay']) ? (int)$item['expireGapInDay'] : 0;
            $curDatetime = new DateTime();
            $curDatetime->modify("+{$expireGapInDay} days"); // append expire gap to currrent datetime
            $expireDate = $curDatetime->format('Y-m-d H:i:s');
        }

        // step 3. insert into DB
        $sql = "INSERT INTO instock (itemId, quantity, expireDate, createdAt)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $itemId, $quantity, $expireDate, $createdAt);
        if ($stmt->execute()) {
            $response = [
                'status' => 200,
                'message' => 'Item Stock has been filled!',
            ];
        }
        $stmt->close();
        $conn->close();
        exit(json_encode($response));
    } else {
        $response['message'] = "Error while fetching Item!";
        $conn->close();
        exit(json_encode($response)); // exit code
    }
}