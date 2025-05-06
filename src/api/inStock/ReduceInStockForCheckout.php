<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$itemId = isset($_GET['itemId']) ? (int) $_GET['itemId'] : 0;
$reduceQuantity = isset($_GET['quantity']) ? (int) $_GET['quantity'] : 0;

if ($itemId <= 0 || $reduceQuantity <= 0) {
    echo json_encode(["status" => 400, "message" => "Invalid itemId or quantity"]);
    exit;
}

// step 1: Get item to check if perishable
$url = "http://localhost/finalYearProjectServer/src/api/item/GetItemById.php?id=" . $itemId;
$resData = json_decode(file_get_contents($url), true);

if (!$resData || $resData['status'] !== 200) {
    echo json_encode(["status" => 404, "message" => "Item not found"]);
    exit;
}

$item = $resData['data'];
$isPerishable = (int) $item['isPerishable'];

// step 2: get in stock rows
$sql = "SELECT * FROM instock WHERE itemId = ? AND quantity > 0";
if ($isPerishable === 1) {
    $sql .= " AND expireDate >= NOW() ORDER BY expireDate ASC";
} else {
    $sql .= " ORDER BY id ASC";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $itemId);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);

// step 3: loop and reduce the in stock
foreach ($rows as $row) {
    $rowId = $row['id'];
    $rowQty = (int) $row['quantity'];

    if ($rowQty <= 0)
        continue; // skip current loop if 0

    if ($rowQty < $reduceQuantity) {
        $sql = "UPDATE instock SET quantity = 0 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $rowId);
        $stmt->execute();
        $reduceQuantity -= $rowQty; // reduce quantity for next loop
    } elseif ($rowQty === $reduceQuantity) {
        $sql = "UPDATE instock SET quantity = 0 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $rowId);
        $stmt->execute();
        $reduceQuantity = 0;
        break; // exit loop
    } elseif ($rowQty > $reduceQuantity) {
        $remainingQty = $rowQty - $reduceQuantity;
        $sql = "UPDATE instock SET quantity = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $remainingQty, $rowId);
        $stmt->execute();
        $reduceQuantity = 0;
        break; // exit loop
    }
}

$response = [
    "status" => 200,
    "message" => "Stock reduced successfully",
];

echo json_encode($response);
$conn->close();
