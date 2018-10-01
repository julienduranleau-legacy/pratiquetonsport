<?php

$dbh = require('includes/db.php');


$website_firstname = $_POST['website_firstname'];
$website_lastname = $_POST['website_lastname'];
$website_email = $_POST['website_email'];
$website_sport = $_POST['website_sport'];
$payment_cart = $_POST['payment_cart'];
$payment_create_time = $_POST['payment_create_time'];
$payment_id = $_POST['payment_id'];
$payment_state = $_POST['payment_state'];
$payer_country_code = $_POST['payer_country_code'];
$payer_email = $_POST['payer_email'];
$payer_first_name = $_POST['payer_first_name'];
$payer_last_name = $_POST['payer_last_name'];
$payer_id = $_POST['payer_id'];
$payer_phone = ""; //$_POST['payer_phone'];

// ========== precheck

$sql = "
    SELECT 
        total,
        (
            SELECT count(*) 
            FROM paypal_transactions
            WHERE website_sport = name_id 
        ) as current 
    FROM sport_limits 
    INNER JOIN sports ON fk_sport_id = sports.id
    WHERE name_id = :sportId
";

$stmt = $dbh->prepare($sql);

$stmt->bindParam(':sportId', $website_sport);

$stmt->execute();

$limits = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (! $limits || (int) $limits[0]['total'] <= (int) $limits[0]['current']) {
    $result = array(
        "success" => false,
        "errorInfos" => "limit for sport reached"
    );

    echo json_encode($result);
    exit();
}

// =======================



$sql = "
    INSERT INTO paypal_transactions 
        (
            website_firstname,
            website_lastname,
            website_email,
            website_sport,
            payment_cart,
            payment_create_time,
            payment_id,
            payment_state,
            payer_country_code,
            payer_email,
            payer_first_name,
            payer_last_name,
            payer_id,
            payer_phone
        ) 
    VALUES 
        (
            :website_firstname,
            :website_lastname,
            :website_email,
            :website_sport,
            :payment_cart,
            :payment_create_time,
            :payment_id,
            :payment_state,
            :payer_country_code,
            :payer_email,
            :payer_first_name,
            :payer_last_name,
            :payer_id,
            :payer_phone
        )
";

$stmt = $dbh->prepare($sql);

$stmt->bindParam(':website_firstname', $website_firstname);
$stmt->bindParam(':website_lastname', $website_lastname);
$stmt->bindParam(':website_email', $website_email);
$stmt->bindParam(':website_sport', $website_sport);
$stmt->bindParam(':payment_cart', $payment_cart);
$stmt->bindParam(':payment_create_time', $payment_create_time);
$stmt->bindParam(':payment_id', $payment_id);
$stmt->bindParam(':payment_state', $payment_state);
$stmt->bindParam(':payer_country_code', $payer_country_code);
$stmt->bindParam(':payer_email', $payer_email);
$stmt->bindParam(':payer_first_name', $payer_first_name);
$stmt->bindParam(':payer_last_name', $payer_last_name);
$stmt->bindParam(':payer_id', $payer_id);
$stmt->bindParam(':payer_phone', $payer_phone);

$sqlResult = $stmt->execute();
$errorInfo = $dbh->errorInfo();

$id = $dbh->lastInsertId();

$result = array(
    "success" => (bool) $sqlResult,
    "errorInfos" => $errorInfo,
    "id" => $id
);

echo json_encode($result);
