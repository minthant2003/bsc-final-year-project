<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require "./../../../vendor/autoload.php"; // relative path to autoload.php for PHPMailer
require './../../database/DbConfig.php';

$supplierId = isset($_GET['supplierId']) ? (int) $_GET['supplierId'] : 0;
$purchaseOrderCode = $_GET['purchaseOrderCode'];
$createdAt = date("Y-m-d H:i:s"); // current datetime

if ($supplierId <= 0) {
    echo json_encode(["status" => 400, "message" => "Invalid supplier ID!"]);
    exit;
}

// step 1: Get supplier by ID
$url = "http://localhost/finalYearProjectServer/src/api/supplier/GetSupplierById.php?id=" . $supplierId;
$resData = json_decode(file_get_contents($url), true);

if (!$resData || $resData['status'] !== 200) {
    echo json_encode(["status" => 404, "message" => "Supplier not found"]);
    exit;
}

$supplier = $resData['data'];
$supplierName = $supplier['supplierName'];
$supplierEmail = $supplier['email'];

try {
    $mail = new PHPMailer(true);

    // SMTP server config
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->SMTPAuth = true;
    $mail->Username = "";
    $mail->Password = "";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = "465";

    // Parameter
    $mail->setFrom("", "");
    $mail->addAddress($supplierEmail, $supplierName);

    // Content
    $mail->isHTML(true);
    $mail->Subject = "Email Notification from 24/7 IMS with POS system";
    $mail->Body = "<h3>New Purchase Order has been made. [Purchase Order Code: $purchaseOrderCode]</h3>";
    $mail->Body .= "<p>Datetime: {$createdAt}</p>";

    // Fail or Success
    if ($mail->send()) {
        $response['status'] = 200;
        $response['message'] = "Email Noti sent successfully to the supplier!";
    } else {
        $response['status'] = 300;
        $response['message'] = "Email Noti to the supplier failed!";
    }
} catch (Exception $e) {
    $response['status'] = 300;
    $response['message'] = $e->getMessage();
}
echo json_encode($response);

mysqli_close($conn);