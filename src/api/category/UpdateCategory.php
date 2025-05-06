<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$request = json_decode(file_get_contents("php://input"), true);

if (isset($request['id'], $request['categoryName'], $request['categoryDesc'], $request['remark'])) {

    $response = ['status' => 300]; // default error status
    $mandatoryFields = [
        'id' => 'Category ID is mandatory!',
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
    $id = intval($request['id']);
    $categoryName = $request['categoryName'];
    $categoryDesc = $request['categoryDesc'];
    $remark = empty($request['remark']) ? NULL : $request['remark'];

    // step 2. check if same category name exists
    $query = "SELECT * FROM category WHERE categoryName = '$categoryName'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $existCategory = mysqli_fetch_assoc($result);
        if (intval($existCategory['id']) !== $id) { // convert to int and compare
            $response['message'] = 'Same Name already exists!';
            $conn->close();
            exit(json_encode($response)); // exit code
        }
    }

    // step 3. update DB
    $sql = "UPDATE category 
        SET categoryName = ?, categoryDesc = ?, remark = ?
        WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $categoryName,  $categoryDesc, $remark, $id);
    if ($stmt->execute()) {
        $response = [
            'status' => 200,
            'message' => 'Category updated successfully!',
        ];
    }
    $stmt->close();
    $conn->close();
    exit(json_encode($response));
}