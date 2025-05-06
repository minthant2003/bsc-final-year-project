<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

if (isset($_GET['itemCode'], $_GET['quantity'])) {
    $itemCode = $_GET['itemCode'];
    $quantity = (int)$_GET['quantity'];
    $response = [];

    // fetch item by itemCode
    $url = "http://localhost/finalYearProjectServer/src/api/item/GetItemByCode.php?itemCode=" . $itemCode;
    $resObj = json_decode(file_get_contents($url), true);
    if ($resObj && $resObj['status'] === 200) {
        $item = $resObj['data'];
        $itemId = $item['id'];
        if ((int)$item['isPerishable'] === 1) {
            // consider quantity and expire date for perishable items
            $curDatetime = (new DateTime())->format('Y-m-d H:i:s');
            $stmt = $conn->prepare("
                SELECT SUM(quantity) AS inStockQuantity 
                FROM instock 
                WHERE itemId = ? AND expireDate >= ?
            ");
            $stmt->bind_param("is", $itemId, $curDatetime);
        } else {
            // consider only quantity for non-perishable items
            $stmt = $conn->prepare("
                SELECT SUM(quantity) AS inStockQuantity 
                FROM instock 
                WHERE itemId = ?
            ");
            $stmt->bind_param("i", $itemId);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $inStockQuantity = (int)$row['inStockQuantity'];

        // check in-stock
        if ($inStockQuantity >= $quantity) {
            $response['status'] = 200;
            $response['message'] = "Available in stock!";
            $conn->close();
            exit(json_encode($response)); // exit code
        } else {
            $response['status'] = 300;
            $response['message'] = "Not Available in stock!";
            $conn->close();
            exit(json_encode($response)); // exit code
        }
    } else {
        $response['status'] = 300;
        $response['message'] = "Error while fetching Item!";
        $conn->close();
        exit(json_encode($response)); // exit code
    }
}
$conn->close();