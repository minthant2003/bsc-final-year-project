<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$request = json_decode(file_get_contents("php://input"), true);

if (isset($request['roleName'], $request['remark'])) {

    $response = ['status' => 300]; // default error status
    $mandatoryFields = [
        'roleName' => 'Name is mandatory!',
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
    $roleName = $request['roleName'];
    $remark = empty($request['remark']) ? NULL : $request['remark'];
    $createdAt = date("Y-m-d H:i:s"); // current datetime

    // step 2. check if same role name exists
    $query = "SELECT * FROM role WHERE roleName = '$roleName'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $response['message'] = 'Same Name already exists!';
        $conn->close();
        exit(json_encode($response)); // exit code
    }

    // step 3. insert into DB
    $sql = "INSERT INTO role (roleName, remark, createdAt) 
        VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $roleName, $remark, $createdAt);
    if ($stmt->execute()) {
        $response = [
            'status' => 200,
            'message' => 'New Role added successfully!',
        ];
    }
    $stmt->close();
    $conn->close();
    exit(json_encode($response));
}