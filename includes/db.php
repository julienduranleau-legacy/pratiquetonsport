<?php

$pdoUser = "root";
$pdoPass = "**********";

$dbh = new PDO('mysql:host=localhost;dbname=pratiquetonsport', $pdoUser, $pdoPass);
$dbh->exec("set names utf8");

return $dbh;
