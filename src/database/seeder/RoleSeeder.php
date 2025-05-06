<?php
require './../DbConfig.php';

$result = mysqli_query($conn, "SELECT * FROM role");
if (mysqli_num_rows($result) == 0) {
    $sql = "INSERT INTO role (roleName, remark, createdAt) VALUES 
        ('admin', NULL, NOW()), 
        ('manager', NULL, NOW()), 
        ('staff', NULL, NOW())";
    if (mysqli_query($conn, $sql)) {
        $response = [
            "message" => "Roles seeded successfully.",
        ];
    } else {
        $response = [
            "message" => "Error while seeding roles: ". mysqli_error($conn),
        ];
    }
} else {
    $response = [
        "message" => "Roles already seeded.",
    ];
}
echo json_encode($response);
mysqli_close($conn);