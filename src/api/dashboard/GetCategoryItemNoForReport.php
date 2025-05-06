<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$sql = "
    SELECT c.categoryName, COUNT(i.id) AS qty
    FROM category c
    LEFT JOIN item i ON c.id = i.categoryId
    GROUP BY c.id, c.categoryName
    ORDER BY c.categoryName
";

$result = $conn->query($sql);
$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'categoryName' => $row['categoryName'],
            'qty' => (int)$row['qty']
        ];
    }
}

$response = [
    'status' => 200,
    'message' => "Category Item No retrieved successfully.",
    'data' => $data,
];
echo json_encode($response);

mysqli_close($conn);