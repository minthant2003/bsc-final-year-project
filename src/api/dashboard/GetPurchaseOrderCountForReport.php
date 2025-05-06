<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$sql = "
    SELECT po.status AS status, COUNT(po.id) AS qty
    FROM purchase_order po
    GROUP BY po.status
    ORDER BY po.status
";

$result = $conn->query($sql);
$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'status' => $row['status'],
            'qty' => (int)$row['qty']
        ];
    }
}

$response = [
    'status' => 200,
    'message' => "Purchase Order No retrieved successfully.",
    'data' => $data,
];
echo json_encode($response);

mysqli_close($conn);