<?php
require '../db/db_connection.php';

$kw = 'gate flick, films, tmdb';
$desc = 'Liste des films sur Gate Flick';
$title = 'Films | Gate Flick';
$filePath = "../";
require '../include/header.inc.php';

try {
    $sql = "SELECT * FROM film";
    $stmt = $pdo->query($sql);
} catch (PDOException $e) {
    echo 'Erreur lors de la récupération des films : ' . $e->getMessage();
}

?>

<main>
    <h1>Films</h1>

    <?php
    foreach ($stmt as $film) {
        echo '<div>';
        echo '<h2><a href="film_details.php?id_film=' . $film['id_film'] . '">' . $film['titre'] . '</a></h2>';
        // Affichez d'autres détails du film si nécessaire
        echo '</div>';
    }
    ?>

</main>

<?php require '../include/footer.inc.php'; ?>

</html>