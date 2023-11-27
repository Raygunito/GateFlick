<?php

$kw = 'gate flick, films, tmdb';
$desc = 'Page de connexion du site Gate Flick';
$title = 'Connexion | Gate Flick';
$filePath = "../";
require_once $filePath . 'db/db_connection.php';
require_once $filePath . "include/functions.inc.php";
require_once $filePath . 'include/header.inc.php';
?>
<main>
    <h1>Administration</h1>
    <div class="card-wrapper">
        <?php
        generateAdminTicketSection($pdo);
        // generateHorizontalCard("Com472524213", "CinemaName", "refuseTicket.php","acceptTicket.php");
        // generateHorizontalCard("Com432473985", "CinemaName", "refuseTicket.php","acceptTicket.php");
        // generateHorizontalCard("Com172951217", "CinemaName", "refuseTicket.php","acceptTicket.php");
        // generateHorizontalCard("Com412724132", "CinemaName", "refuseTicket.php","acceptTicket.php");
        // generateHorizontalCard("Com272272135", "CinemaName", "refuseTicket.php","acceptTicket.php");
        ?>
    </div>
</main>


<?php require_once $filePath . 'include/footer.inc.php'; ?>