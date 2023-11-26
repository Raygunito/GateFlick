<?php

$kw = 'gate flick, films, tmdb';
$desc = 'Page de connexion du site Gate Flick';
$title = 'Connexion | Gate Flick';
$filePath = "../";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once $filePath . 'db/db_connection.php';
}
require_once $filePath . "include/functions.inc.php";
require_once $filePath . 'include/header.inc.php';
?>



<?php require_once $filePath . 'include/footer.inc.php';?>