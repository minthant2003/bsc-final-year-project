<?php
require './../DbConfig.php';

$rolesObj = [];
$roleQuery = "SELECT roleName, id FROM role WHERE roleName IN ('admin', 'manager', 'staff')";
$roleResult = mysqli_query($conn, $roleQuery);
if (mysqli_num_rows($roleResult) > 0) {
    while ($row = mysqli_fetch_assoc($roleResult)) {
        $rolesObj[$row['roleName']] = $row['id'];
    }

    if (isset($rolesObj['admin'], $rolesObj['manager'], $rolesObj['staff'])) {
        $result = mysqli_query($conn, "SELECT * FROM user");
        if (mysqli_num_rows($result) == 0) {
            $sql = "INSERT INTO user (userName, password, email, phoneNo, createdAt, roleId) VALUES 
                ('admin', '". password_hash("admin", PASSWORD_DEFAULT) ."', 'admin@gmail.com', '12345', NOW(), ". $rolesObj['admin'] ."),
                ('manager', '". password_hash("manager", PASSWORD_DEFAULT) ."', 'manager@gmail.com', '54321', NOW(), ". $rolesObj['manager'] ."),
                ('staff', '". password_hash("staff", PASSWORD_DEFAULT) ."', 'staff@gmail.com', '67890', NOW(), ". $rolesObj['staff'] .")";
            if (mysqli_query($conn, $sql)) {
                $response = [
                    "message" => "Users seeded successfully.",
                ];
            } else {
                $response = [
                    "message" => "Error while seeding users: ". mysqli_error($conn),
                ];
            }
        } else {
            $response = [
                "message" => "Users already seeded.",
            ];
        }
    }
}

echo json_encode($response);
mysqli_close($conn);