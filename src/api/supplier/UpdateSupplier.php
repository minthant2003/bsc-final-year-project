<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require './../../database/DbConfig.php';

$request = json_decode(file_get_contents("php://input"), true);

if (isset($request['id'], $request['supplierName'], $request['email'], $request['phoneNo'], 
    $request['address'], $request['remark'])) {

    $response = ['status' => 300]; // default error status
    $mandatoryFields = [
        'id' => 'Supplier ID is mandatory!',
        'supplierName' => 'Name is mandatory!',
        'email' => 'Email is mandatory!',
        'phoneNo' => 'Phone No is mandatory!',
        'address' => 'Address is mandatory!',
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
    $supplierName = $request['supplierName'];
    $email = $request['email'];
    $phoneNo = $request['phoneNo'];
    $address = $request['address'];
    $remark = empty($request['remark']) ? NULL : $request['remark'];

    // step 2. check if same supplier name exists
    $query = "SELECT * FROM supplier WHERE supplierName = '$supplierName'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $existSupplier = mysqli_fetch_assoc($result);
        if (intval($existSupplier['id']) !== $id) { // convert to int and compare
            $response['message'] = 'Same Name already exists!';
            $conn->close();
            exit(json_encode($response)); // exit code
        }
    }

    // step 3. update DB
    $sql = "UPDATE supplier 
        SET supplierName = ?, email = ?, phoneNo = ?, address = ?, remark = ?
        WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $supplierName,  $email, $phoneNo, $address, $remark, $id);
    if ($stmt->execute()) {
        $response = [
            'status' => 200,
            'message' => 'Supplier updated successfully!',
        ];
    }
    $stmt->close();
    $conn->close();
    exit(json_encode($response));
}