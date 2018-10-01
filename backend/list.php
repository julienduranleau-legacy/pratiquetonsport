<?php

$dbh = require('../includes/db.php');
$sportList = require('../includes/sports.php');

$currentPage = 'list';

$filter = getCurrentFilter($sportList);

if ($filter) {
    $stmt = $dbh->prepare("SELECT * FROM paypal_transactions WHERE website_sport = :website_sport");
    $stmt->bindParam(':website_sport', $filter);
} else {
    $stmt = $dbh->prepare("SELECT * FROM paypal_transactions");
}

$stmt->execute();

$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

function sportIdToString($id, $list) {
    return $list[$id];
}

function formatDateFromTimestamp($timestamp) {
    $months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');

    $currentMonthId = ((int) date('n', $timestamp)) - 1;

    $str = date('j', $timestamp);
    $str .= ' '.$months[$currentMonthId];
    $str .= date(' Y - G\hi', $timestamp);

    return $str;
}

function getCurrentFilter($list) {
    if (isset($_GET['filter'])) {
        if (in_array($_GET['filter'], array_keys($list))) {
            return $_GET['filter'];
        }
    } else {
        return null;
    }
}

?>
<!DOCTYPE html>
<html lang="en" class="backend list">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <title>Pratique ton sport - Admin</title>

        <link rel="stylesheet" href="../styles/compiled/styles.css" />
    </head>
    <body>
        <?php include('../includes/backend/menu.php'); ?>
        
        <div class="filters">
            <a href="?">Tous</a>
            <?php foreach($sportList as $key => $sport) { ?>
                <a href="?filter=<?php echo $key ?>"><?php echo $sport ?></a>
            <?php } ?>
        </div>
        <div class="n-results">
            <?php 
                switch(count($sales)) {
                    case 0: echo 'Aucun résultat'; break;
                    case 1: echo '1 résultat'; break;
                    default: echo count($sales).' résultats';
                }
            ?>
        </div>
        <div class="sales">
            <?php foreach ($sales as $i => $sale) { ?>
                <div class="sale">
                    <div class="top">
                        <div class="id"># <?php echo $sale['id'] ?></div>
                        <div class="payment_create_time"><?php echo formatDateFromTimestamp(strtotime($sale['payment_create_time'])) ?></div>
                    </div>
                    <div class="center">
                        <div class="website_firstname">
                            <strong>Nom: </strong><?php echo $sale['website_firstname'] . ' ' . $sale['website_lastname'] ?>
                        </div>
                        <div class="website_email">
                            <strong>Courriel: </strong><?php echo $sale['website_email'] ?>
                        </div>
                        <div class="website_sport">
                            <strong>Sport: </strong><?php echo sportIdToString($sale['website_sport'], $sportList) ?>
                        </div>
                        <div class="show-more">
                            <span class="show">Afficher le paiement</span>
                            <span class="hide">Cacher le paiement</span>
                        </div>
                        <div class="paiement-infos">
                            <div class="payer_first_name">
                                <strong>Prénom: </strong><?php echo $sale['payer_first_name'] ?>
                            </div>
                            <div class="payer_last_name">
                                <strong>Nom: </strong><?php echo $sale['payer_last_name'] ?>
                            </div>
                            <div class="payer_email">
                                <strong>Email: </strong><?php echo $sale['payer_email'] ?>
                            </div>
                            <div class="payer_phone">
                                <strong>Téléphone: </strong><?php echo $sale['payer_phone'] ?>
                            </div>
                            <div class="payment_state">
                                <strong>Transaction: </strong><?php echo $sale['payment_state'] ?>
                            </div>
                            <div class="payer_country_code">
                                <strong>Pays: </strong><?php echo $sale['payer_country_code'] ?>
                            </div>
                            <div class="payment_id">
                                <strong>ID de paiement: </strong><?php echo $sale['payment_id'] ?>
                            </div>
                            <div class="payment_cart">
                                <strong>ID de carte: </strong><?php echo $sale['payment_cart'] ?>
                            </div>
                            <div class="payer_id">
                                <strong>ID du payer: </strong><?php echo $sale['payer_id'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="../js/backend/list.js"></script>
    </body>
</html>