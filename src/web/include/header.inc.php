<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name='keywords' content='<?= $kw ?>' />
    <meta name='description' content='<?= $desc ?>' />
    <link rel='icon' href='img/favicon.svg' type='image/svg' />
    <link rel="stylesheet" href="<?= $filePath?>css/main.css">
    <title>
        <?= $title ?>
    </title>
</head>

<body>
    <header>
        <div class="logo"><img src="<?= $filePath?>img/gate-flick.svg" alt="logo GateFlick"></div>
        <nav>
            <ul>
                <li>
                    <a href="pages/films.php">Films</a>
                </li>
                <li>
                    <a href="pages/reservation.php">Reservation</a>
                </li>
                <li>
                    <a href="pages/watchlist.php">WatchList</a>
                </li>
            </ul>
        </nav>
        <div class="searchAccount">
            <a href="pages/connexion.php"><button class="buttons__account | icon" type="button"></button></a>
            <a href="pages/deconnexion.php">Se déconnecter</a>
        </div>

    </header>