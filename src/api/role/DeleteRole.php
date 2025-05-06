<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $response = [];

    // check foreign key constraints before delete
    $query = "SELECT * FROM user WHERE roleId = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) > 0) {
        $response["status"] = 300;
        $response["message"] = "Could not delete. Role already assigned on users!";
        $stmt->close();
        $conn->close();
        exit(json_encode($response)); // exit code
    }

    // delete from the DB
    $query = "DELETE FROM role WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $response["status"] = 200;
        $response["message"] = "Role has been successfully deleted.";
        $stmt->close();
        $conn->close();
        exit(json_encode($response)); // exit code
    }
}
$conn->close();