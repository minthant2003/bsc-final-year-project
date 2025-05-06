<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$request = json_decode(file_get_contents("php://input"), true);

if (isset($request['userName'], $request['password'], $request['email'], 
    $request['phoneNo'], $request['remark'], $request['roleId'])) {

    $response = ['status' => 300]; // default error status
    $mandatoryFields = [
        'userName' => 'Username is mandatory!',
        'password' => 'Password is mandatory!',
        'email' => 'Email is mandatory!',
        'phoneNo' => 'Phone No is mandatory!',
        'roleId' => 'Role is mandatory!'
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
    $userName = $request['userName'];
    $password = password_hash($request['password'], PASSWORD_BCRYPT);
    $email = $request['email'];
    $phoneNo = $request['phoneNo'];
    $remark = empty($request['remark']) ? NULL : $request['remark'];
    $roleId = $request['roleId'];
    $createdAt = date("Y-m-d H:i:s"); // current datetime

    // check if same username exists
    $query = "SELECT * FROM user WHERE userName = '$userName'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $response['message'] = 'Same Username already exists!';
        $conn->close();
        exit(json_encode($response)); // exit code
    }

    // insert into DB
    $sql = "INSERT INTO user (userName, password, email, phoneNo, remark, createdAt, roleId) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $userName, $password, $email, $phoneNo, $remark, $createdAt, $roleId);
    if ($stmt->execute()) {
        $response = [
            'status' => 200,
            'message' => 'New user added successfully!',
        ];
    }
    $stmt->close();
    $conn->close();
    exit(json_encode($response));
}