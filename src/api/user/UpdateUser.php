<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$request = json_decode(file_get_contents("php://input"), true);

if (isset($request['id'], $request['userName'], $request['email'], 
    $request['phoneNo'], $request['remark'])) {

    $response = ['status' => 300]; // default error status
    $mandatoryFields = [
        'id' => 'User ID is mandatory!',
        'userName' => 'Username is mandatory!',
        'email' => 'Email is mandatory!',
        'phoneNo' => 'Phone No is mandatory!',
    ];
    // validation
    foreach ($mandatoryFields as $field => $errMsg) {
        if (empty($request[$field])) {
            $response['message'] = $errMsg;
            $conn->close();
            exit(json_encode($response)); // exit code
        }
    }

    // Interact with DB
    $id = intval($request['id']);
    $userName = $request['userName'];
    $email = $request['email'];
    $phoneNo = $request['phoneNo'];
    $remark = empty($request['remark']) ? NULL : $request['remark'];

    // check if same username exists
    $query = "SELECT * FROM user WHERE userName = '$userName'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $existUser = mysqli_fetch_assoc($result);
        if (intval($existUser['id']) !== $id) { // convert to int and compare
            $response['message'] = 'Same Username already exists!';
            $conn->close();
            exit(json_encode($response)); // exit code
        }
    }

    // update DB
    $sql = "UPDATE user 
        SET userName = ?, email = ?, phoneNo = ?, remark = ?
        WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $userName,  $email, $phoneNo, $remark, $id);
    if ($stmt->execute()) {
        $response = [
            'status' => 200,
            'message' => 'User updated successfully!',
        ];
    }
    $stmt->close();
    $conn->close();
    exit(json_encode($response));
}