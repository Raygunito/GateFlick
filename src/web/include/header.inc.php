<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name='keywords' content='<?= $kw ?>' />
    <meta name='description' content='<?= $desc ?>' />
    <link rel='icon' href='img/favicon.svg' type='image/svg' />
    <link rel="stylesheet" href="<?= $filePath ?>css/main.css" />
    <title>
        <?= $title ?>
    </title>
</head>

<body>
    <header>
        <div class="logo"><a href="<?= $filePath ?>index.php"><img src="<?= $filePath ?>img/gate-flick.svg" alt="logo GateFlick" /></a></div>
        <nav>
            <ul>
                <li>
                    <a href="<?= $filePath ?>pages/films.php">Films</a>
                </li>
                <li>
                    <a href="<?= $filePath ?>pages/reservation.php">Réservation</a>
                </li>
                <?php
                if (isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"]) && str_contains($_SESSION["user_id"], "Emp")) {
                    echo '<li><a href="' . $filePath . 'pages/administration.php">Administration</a></li>';
                }
                ?>
            </ul>
        </nav>
        <div class="searchAccount">
            <a href="<?= $filePath ?>pages/profil.php"><img src="<?= $filePath ?>img/account_button.svg" alt="mon compte"></a>
            <?php
            if (isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"])) {
                echo '<a href="' . $filePath . 'pages/deconnexion.php"><img src="' . $filePath . 'img/logout_button.svg" alt="se deconnecter"></a>';
            }
            ?>
        </div>

    </header>