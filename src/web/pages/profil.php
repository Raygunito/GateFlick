<?php

$kw = 'gate flick, films, tmdb';
$desc = 'Profil du client ou de l\'employé ';
$title = 'Profil | Gate Flick';
$filePath = "../";
require_once $filePath.'include/header.inc.php';
require_once $filePath."include/functions.inc.php";
if (!isset($_SESSION["user_id"])) {
    header('Location: '.$filePath.'pages/connexion.php');
}
require_once $filePath.'db/db_connection.php';

?>

<main>
    <h1>Mon profil</h1>
    <?= showInfo($_SESSION['user_id'],$pdo)?>
</main>

<?php
require_once $filePath.'include/footer.inc.php';