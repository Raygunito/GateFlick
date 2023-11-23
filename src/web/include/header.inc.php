<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name='keywords' content='<?= $kw ?>' />
    <meta name='description' content='<?= $desc ?>' />
    <link rel='icon' href='../img/favicon.svg' type='image/svg' />
    <link rel='stylesheet' href='../styles/light.css' />
    <title> <?= $title ?> </title>
</head>

<body>
    <header class="flex-group">
        <a class="logo" href="../index.php"><img src="../img/gate-flick.svg" alt="logo" /></a>
        <?php
        echo '<nav id="primary-navbar">';
        require "navigation.inc.php";
        echo '</nav>';
        ?>

        <div class="buttons | flex-group">
            <form method="get">
                <input type="text" id="search" name="search" placeholder="Entrez le nom du film" required />
                <button class="buttons__search | icon" type="submit"></button>
            </form>
            <a href="../pages/login.php"><button class="buttons__account | icon" type="button"></button></a>
        </div>
    </header>