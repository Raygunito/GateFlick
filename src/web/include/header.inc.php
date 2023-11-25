<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name='keywords' content='<?= $kw ?>' />
    <meta name='description' content='<?= $desc ?>' />
    <link rel='icon' href='../img/favicon.svg' type='image/svg' />
    <title> <?= $title ?> </title>
</head>

<body>
    <header>
        <nav>
            <ul>
                <li>
                    <a href="../pages/films.php">Films</a>
                </li>
                <li>
                    <a href="../pages/reservation.php">Reservation</a>
                </li>
                <li>
                    <a href="../pages/watchlist.php">WatchList</a>
                </li>
            </ul>
        </nav>
        <div>
            <form method="get" class="flex-group">
                <input type="text" id="search" name="search" placeholder="Entrez le nom du film" required />
                <button class="buttons__search | icon" type="submit"></button>
            </form>
            <a href="../pages/connexion.php"><button class="buttons__account | icon" type="button"></button></a>
            <a href="deconnexion.php">Se déconnecter</a>
        </div>

    </header>