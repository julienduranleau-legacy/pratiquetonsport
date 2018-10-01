<?php

$dbh = require('includes/db.php');

$sql = "
    UPDATE paypal_transactions 
    SET payer_phone = :payer_phone
    WHERE id = :id
";

$stmt = $dbh->prepare($sql);

$id = $_POST['id'];
$payer_phone = $_POST['payer_phone'];

$stmt->bindParam(':id', $id);
$stmt->bindParam(':payer_phone', $payer_phone);

$sqlResult = $stmt->execute();
$errorInfo = $dbh->errorInfo();

$result = array(
    "success" => (bool) $sqlResult,
    "errorInfos" => $errorInfo
);

echo json_encode($result);
