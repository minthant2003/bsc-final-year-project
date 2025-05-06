<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$request = json_decode(file_get_contents("php://input"), true);

if (isset($request['itemCode'], $request['itemImgStr'], $request['barcodeImgStr'], 
    $request['itemName'], $request['price'], $request['rop'],
    $request['ropQuantity'], $request['discountPercentage'], $request['taxPercentage'],
    $request['isPerishable'], $request['expireGapInDay'], $request['remark'],
    $request['categoryId'])) {

    $response = ['status' => 300]; // default error status
    $mandatoryFields = [
        'itemCode' => 'Item Code is mandatory!',
        'itemImgStr' => 'Item Image is mandatory!',
        'barcodeImgStr' => 'Barcode Image is mandatory!',
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
    $itemCode = $request['itemCode'];
    $itemImgStr = $request['itemImgStr'];
    $barcodeImgStr = $request['barcodeImgStr'];
    $itemName = $request['itemName'];
    $price = $request['price'];
    $rop = $request['rop'];
    $ropQuantity = $request['ropQuantity'];
    $discountPercentage = $request['discountPercentage'];
    $taxPercentage = $request['taxPercentage'];
    $isPerishable = $request['isPerishable'];
    $expireGapInDay = empty($request['expireGapInDay']) ? NULL : $request['expireGapInDay'];
    $remark = empty($request['remark']) ? NULL : $request['remark'];
    $createdAt = date("Y-m-d H:i:s"); // current datetime
    $categoryId = $request['categoryId'];

    // step 2.1. check if same item code exists
    $query = "SELECT * FROM item WHERE itemCode = '$itemCode'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $response['message'] = 'Same Item Code already exists!';
        $conn->close();
        exit(json_encode($response)); // exit code
    }
    // step 2.2. check if same item name exists
    $query = "SELECT * FROM item WHERE itemName = '$itemName'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $response['message'] = 'Same Item Name already exists!';
        $conn->close();
        exit(json_encode($response)); // exit code
    }

    // step 3. insert into DB
    $sql = "INSERT INTO item (itemCode, itemImgStr, barcodeImgStr, itemName, 
        price, rop, ropQuantity, discountPercentage, taxPercentage, 
        isPerishable, expireGapInDay, remark, createdAt, categoryId)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssdiiddiissi", $itemCode, $itemImgStr, $barcodeImgStr, $itemName, 
        $price, $rop, $ropQuantity, $discountPercentage, $taxPercentage, 
        $isPerishable, $expireGapInDay, $remark, $createdAt, $categoryId);
    if ($stmt->execute()) {
        $response = [
            'status' => 200,
            'message' => 'New Item added successfully!',
        ];
    }
    $stmt->close();
    $conn->close();
    exit(json_encode($response));
}