<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

// $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";

$query = "SELECT COUNT(s.id) AS total FROM sale s
        LEFT JOIN user u 
        ON s.userId = u.id";

// if (!empty($search)) {
//     $query .= " WHERE i.itemCode LIKE '%$search%' 
//         OR i.itemName LIKE '%$search%' 
//         OR c.categoryName LIKE '%$search%'";
// }

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$response = [
    'status' => 200,
    'message' => "Sale count retrieved successfully.",
    'data' => $row['total']
];

echo json_encode($response);
