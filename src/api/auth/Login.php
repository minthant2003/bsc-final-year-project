<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
require './../../database/DbConfig.php';

$request = json_decode(file_get_contents("php://input"), true);
if (isset($request['userName'], $request['password'])) {
    // Validation
    if (empty($request['userName']) || empty($request['password'])) {
        $response = [
            'status' => 300,
            'message' => "All input fields are required."
        ];
        echo json_encode($response);
    } else {
        // Interact with DB
        $userName = $request['userName'];
        $password = $request['password'];
    
        $query = "SELECT u.*, r.roleName FROM user u
                INNER JOIN role r ON u.roleId = r.id
                WHERE u.userName = '$userName'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) == 0) {
            $response = [
                'status' => 300,
                'message' => "Username is not correct."
            ];
            echo json_encode($response);
        } else {
            $data = mysqli_fetch_assoc($result);
            if (!password_verify($password, $data["password"])) {
                $response = [
                    'status' => 300,
                    'message' => "Password is not correct."
                ];
                echo json_encode($response);
            } else {
                $response = [
                    'status' => 200,
                    'message' => "Login successful.",
                    'data' => $data
                ];
                echo json_encode($response);
            }
        }
    }
}
mysqli_close($conn);