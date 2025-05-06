<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$request = json_decode(file_get_contents("php://input"), true);

if (isset($request['categoryName'], $request['categoryDesc'], $request['remark'])) {

    $response = ['status' => 300]; // default error status
    $mandatoryFields = [
        'categoryName' => 'Name is mandatory!',
        'categoryDesc' => 'Description is mandatory!',
    ];
    // step 1. validation
    foreach ($mandatoryFields as $field => $errMsg) {
        if (empty($request[$field])) {
            $response['message'] = $errMsg;
            $conn->close();
            exit(json_encode($response)); // exit code
        }
    }

    // Interact with DB
    $categoryName = $request['categoryName'];
    $categoryDesc = $request['categoryDesc'];
    $remark = empty($request['remark']) ? NULL : $request['remark'];
    $createdAt = date("Y-m-d H:i:s"); // current datetime

    // step 2. check if same category name exists
    $query = "SELECT * FROM category WHERE categoryName = '$categoryName'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $response['message'] = 'Same Name already exists!';
        $conn->close();
        exit(json_encode($response)); // exit code
    }

    // step 3. insert into DB
    $sql = "INSERT INTO category (categoryName, categoryDesc, remark, createdAt) 
        VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $categoryName, $categoryDesc, $remark, $createdAt);
    if ($stmt->execute()) {
        $response = [
            'status' => 200,
            'message' => 'New category added successfully!',
        ];
    }
    $stmt->close();
    $conn->close();
    exit(json_encode($response));
}