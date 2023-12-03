<?php

$kw = 'gate flick, films, tmdb';
$desc = 'Administration de Gate Flick';
$title = 'Admin | Gate Flick';
$filePath = "../";
require_once $filePath . 'db/db_connection.php';
require_once $filePath . "include/functions.inc.php";
require_once $filePath . 'include/header.inc.php';

if ((!isset($_SESSION['user_id']))) {
    header('Location: ../pages/connexion.php');
    exit();
}

if ((isset($_SESSION['user_id']))&&(!empty($_SESSION['user_id']))&&(!str_contains($_SESSION['user_id'],"Emp"))) {
    header('Location: ../index.php');
    exit();
}

if (isset($_GET['idCom']) && !empty($_GET['idCom']) && isset($_GET['decline']) && !empty($_GET['decline'])) {
    $pdo->beginTransaction();
    $getCom = "SELECT * FROM ticket WHERE id_ticket = :id ";
    $st = $pdo->prepare($getCom);
    $st->bindParam(":id", $_GET["idCom"]);
    $st->execute();
    $pdo->commit();
    if ($st->rowCount() > 0) {
        $resCom = $st->fetch(PDO::FETCH_ASSOC);
        if ($_GET['decline'] == "True" && $resCom["statut_usage"] != "Utilisé") {
            $pdo->beginTransaction();
            $declineSQL = "UPDATE ticket SET statut_usage = 'En attente' WHERE id_ticket = :id";
            $stdecline = $pdo->prepare($declineSQL);
            $stdecline->bindParam(":id", $_GET["idCom"]);
            $stdecline->execute();
            $pdo->commit();
        } else {
            if ($_GET['decline'] == "False") {
                $pdo->beginTransaction();
                $declineSQL = "UPDATE ticket SET statut_usage = 'Utilisé' WHERE id_ticket = :id";
                $stdecline = $pdo->prepare($declineSQL);
                $stdecline->bindParam(":id", $_GET["idCom"]);
                $stdecline->execute();
                $pdo->commit();
            }
        }
        header("Location: " . $filePath . 'pages/administration.php');
    }
}

?>
<main>
    <h1>Administration</h1>
    <section>
        <h2>Commandes avec problème</h2>
        <div class="card-wrapper">
            <?php
            generateAdminTicketSection($pdo);
            ?>
        </div>
    </section>
    <section>
        <h2>Statistiques</h2>
        <div class="card-wrapper" style="gap:15vmin;">
            <?php
            generateAdminStatsSection($pdo);
            ?>
        </div>        
    </section>
</main>


<?php require_once $filePath . 'include/footer.inc.php'; ?>