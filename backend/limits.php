<?php

$dbh = require('../includes/db.php');
$sportList = require('../includes/sports.php');

$currentPage = 'limits';

if (isset($_POST['edit'])) {
    if ((int) $_POST['hidden'] === 42) {
        $limitId = (int) $_POST['limit_id'];
        $limitTotal = (int) $_POST['total'];

        $sql = "
            UPDATE sport_limits
            SET total = :total
            WHERE id = :id
        ";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':total', $limitTotal);
        $stmt->bindParam(':id', $limitId);

        $stmt->execute();
    }

    header('location:?');
}

$sql = "
    SELECT 
        sport_limits.id, 
        name_id, 
        name, 
        total,
        (
            SELECT count(*) 
            FROM paypal_transactions
            WHERE website_sport = name_id 
        ) as current 
    FROM sport_limits 
    INNER JOIN sports ON fk_sport_id = sports.id
";
$stmt = $dbh->prepare($sql);

$stmt->execute();

$limits = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en" class="backend limits">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <title>Pratique ton sport - Admin</title>

        <link rel="stylesheet" href="../styles/compiled/styles.css" />
    </head>
    <body>
        <?php include('../includes/backend/menu.php'); ?>
        
        <div class="limits">
            <?php foreach ($limits as $key => $limit) { ?>
                <div class="limit">
                    <div class="top">
                        <div class="name"><?php echo $limit['name'] ?></div>
                    </div>
                    <div class="center">
                        <div class="total <?php echo ((int) $limit['current'] >= (int) $limit['total']) ? 'full' : '' ?>">
                            <strong>Inscriptions: </strong><?php echo $limit['current'].'/'.$limit['total'] ?>
                        </div>
                        <div class="form">
                            <div class="edit-label">Modifier le maximum d'inscriptions</div>
                            <form action="?" method="post">
                                <input type="hidden" name="hidden" value="42">
                                <input type="hidden" name="limit_id" value="<?php echo $limit['id'] ?>">
                                <input type="text" name="total" value="<?php echo $limit['total'] ?>">
                                <input type="submit" name="edit" value="Modifier">
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="../js/backend/limits.js"></script>
    </body>
</html>