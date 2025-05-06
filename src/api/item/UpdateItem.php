<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$request = json_decode(file_get_contents("php://input"), true);

if (isset($request['id'], $request['itemCode'], $request['itemImgStr'], $request['barcodeImgStr'], 
    $request['itemName'], $request['price'], $request['rop'],
    $request['ropQuantity'], $request['discountPercentage'], $request['taxPercentage'],
    $request['isPerishable'], $request['expireGapInDay'], $request['remark'],
    $request['categoryId'])) {

    $response = ['status' => 300]; // default error status
    $mandatoryFields = [
        'itemName' => 'Item Name is mandatory!',
        'price' => 'Price is mandatory!',
        'rop' => 'ROP is mandatory!',
        'ropQuantity' => 'ROP Quantity is mandatory!',
        'discountPercentage' => 'Discount % is mandatory!',
        'taxPercentage' => 'Tax % is mandatory!',
        'categoryId' => 'Category is mandatory!',
    ];
    // step 1.1. validation (traditional)
    foreach ($mandatoryFields as $field => $errMsg) {
        if (!isset($request[$field]) || $request[$field] === "") { // allows 0 or false
            $response['message'] = $errMsg;
            $conn->close();
            exit(json_encode($response)); // exit code
        }
    }
    // step 1.2. validation (for expireGapInDay based on isPerishable)
    if ($request['isPerishable'] && empty($request['expireGapInDay'])) {
        $response['message'] = 'Expire Gap in Days is mandatory for perishable items!';
        $conn->close();
        exit(json_encode($response));
    }

    // Interact with DB
    $id = intval($request['id']);
    $itemName = $request['itemName'];
    $price = $request['price'];
    $rop = $request['rop'];
    $ropQuantity = $request['ropQuantity'];
    $discountPercentage = $request['discountPercentage'];
    $taxPercentage = $request['taxPercentage'];
    $expireGapInDay = empty($request['expireGapInDay']) ? NULL : $request['expireGapInDay'];
    $remark = empty($request['remark']) ? NULL : $request['remark'];
    $categoryId = $request['categoryId'];

    // step 2. check if same item name exists
    $query = "SELECT * FROM item WHERE itemName = '$itemName'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $existItem = mysqli_fetch_assoc($result);
        if (intval($existItem['id']) !== $id) { // convert to int and compare
            $response['message'] = 'Same Item Name already exists!';
            $conn->close();
            exit(json_encode($response)); // exit code
        }
    }

    // step 3. update DB
    $sql = "UPDATE item 
            SET itemName = ?, price = ?, rop = ?, ropQuantity = ?, discountPercentage = ?
                , taxPercentage = ?, expireGapInDay = ?, remark = ?, categoryId = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdiiddisii", $itemName, $price, $rop, $ropQuantity
        , $discountPercentage, $taxPercentage, $expireGapInDay, $remark, $categoryId, $id);
    if ($stmt->execute()) {
        $response = [
            'status' => 200,
            'message' => 'Item updated successfully!',
        ];
    }
    $stmt->close();
    $conn->close();
    exit(json_encode($response));
}