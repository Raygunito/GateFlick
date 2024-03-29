<?php

$kw = 'gate flick, films, tmdb';
$desc = 'Page de détails du cinéma sur Gate Flick';
$title = 'Détails du Cinéma | Gate Flick';
$filePath = "../";

require_once '../db/db_connection.php';
require_once '../include/header.inc.php';
require_once $filePath."include/functions.inc.php";
?>
<main>
    <h1>Détails du film</h1>
    <?php
    if (isset($_GET['id_film']) && !empty($_GET['id_film'])) {
        $filmId = $_GET['id_film'];

        try {
            $filmSql = "SELECT * FROM film WHERE id_film = :filmId";
            $filmStmt = $pdo->prepare($filmSql);
            $filmStmt->bindParam(':filmId', $filmId);
            $filmStmt->execute();

            if ($filmStmt->rowCount() > 0) {
                $filmDetails = $filmStmt->fetch(PDO::FETCH_ASSOC);

                printFilmDetails($filmDetails);

                $cinemaSql = "SELECT c.nom_cinema, s.nom_salle, se.id_seance, se.heure_projection, se.langue, se.statut, se.prix
                          FROM cinema c
                          JOIN salle s ON c.id_cinema = s.id_cinema
                          JOIN seance se ON s.id_salle = se.id_salle
                          WHERE se.id_film = :filmId  AND se.statut != 'En cours'";
                $cinemaStmt = $pdo->prepare($cinemaSql);
                $cinemaStmt->bindParam(':filmId', $filmId);
                $cinemaStmt->execute();

                if ($cinemaStmt->rowCount() > 0) {
                    $seancesParCinema = [];

                    foreach ($cinemaStmt as $row) {
                        $cinemaNom = $row['nom_cinema'];

                        $seancesParCinema[$cinemaNom][] = [
                            'salleNom' => $row['nom_salle'],
                            'id_seance' => $row['id_seance'],
                            'heure_projection' => $row['heure_projection'],
                            'langue' => $row['langue'],
                            'statut' => $row['statut'],
                            'prix' => $row['prix']
                        ];
                    }

                    foreach ($seancesParCinema as $cinemaNom => $seances) {
                        echo '<h3>Cinéma ' . $cinemaNom . '</h3>';
                        foreach ($seances as $seance) {
                            echo '<p>';
                            echo 'Salle ' . $seance['salleNom'] . '<br>';
                            echo 'Heure de projection : ' . $seance['heure_projection'] . '<br>';
                            echo 'Langue : ' . $seance['langue'] . '<br>';
                            echo 'Statut : ' . $seance['statut'] . '<br>';
                            echo 'Prix : ' . $seance['prix'] . '<br>';
                            echo '</p>';
                            echo '<form action="../scripts/traitement_reservation.php" method="post">';
                            echo '<input type="hidden" name="id_film" value="' . $filmId . '">';
                            echo '<input type="hidden" name="id_seance" value="' . $seance['id_seance'] . '">';
                            echo afficherSiegesDisponibles($pdo, $seance['id_seance']);
                            echo '<input type="submit" value="Réserver" class="siege">';
                            echo '</form>';
                        }
                    }
                } else {
                    echo '<p>Aucun cinéma trouvé proposant ce film.</p>';
                }
            } else {
                echo '<p>Aucun film trouvé avec cet ID.</p>';
            }
        } catch (PDOException $e) {
            echo 'Erreur lors de la récupération des détails du film : ' . $e->getMessage();
        }
    } else {
        echo '<p>Paramètre d\'ID de film manquant.</p>';
    }
    ?>
</main>
<?php
require_once '../include/footer.inc.php';
?>