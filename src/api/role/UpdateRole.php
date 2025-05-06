<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$request = json_decode(file_get_contents("php://input"), true);

if (isset($request['id'], $request['roleName'], $request['remark'])) {

    $response = ['status' => 300]; // default error status
    $mandatoryFields = [
        'id' => 'Role ID is mandatory!',
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
    $id = intval($request['id']);
    $roleName = $request['roleName'];
    $remark = empty($request['remark']) ? NULL : $request['remark'];

    // step 2. check if same role name exists
    $query = "SELECT * FROM role WHERE roleName = '$roleName'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $existRole = mysqli_fetch_assoc($result);
        if (intval($existRole['id']) !== $id) { // convert to int and compare
            $response['message'] = 'Same Name already exists!';
            $conn->close();
            exit(json_encode($response)); // exit code
        }
    }

    // step 3. update DB
    $sql = "UPDATE role 
        SET roleName = ?, remark = ?
        WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $roleName, $remark, $id);
    if ($stmt->execute()) {
        $response = [
            'status' => 200,
            'message' => 'Role updated successfully!',
        ];
    }
    $stmt->close();
    $conn->close();
    exit(json_encode($response));
}