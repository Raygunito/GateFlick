<?php
require '../db/db_connection.php';

$kw = 'gate flick, films, tmdb';
$desc = 'Page de détails du cinéma sur Gate Flick';
$title = 'Détails du Cinéma | Gate Flick';
$filePath = "../";
require '../include/header.inc.php';

// Récupérer l'ID du film depuis la requête GET
if (isset($_GET['id_film'])) {
    $filmId = $_GET['id_film'];

    try {
        // Requête pour récupérer les détails du film
        $filmSql = "SELECT * FROM film WHERE id_film = :filmId";
        $filmStmt = $pdo->prepare($filmSql);
        $filmStmt->bindParam(':filmId', $filmId);
        $filmStmt->execute();

        if ($filmStmt->rowCount() > 0) {
            $filmDetails = $filmStmt->fetch(PDO::FETCH_ASSOC);

            echo '<main>';
            echo '<h1>Détails du Film</h1>';
            echo '<h2>' . $filmDetails['titre'] . '</h2>';
            echo '<p>Nation : ' . $filmDetails['nation'] . '</p>';
            echo '<p>Durée : ' . $filmDetails['duree'] . '</p>';
            echo '<p>Metteur en scène : ' . $filmDetails['metteur_en_scene'] . '</p>';
            echo '<p>Date de sortie : ' . $filmDetails['date_sortie'] . '</p>';
            echo '</main>';

            // Requête pour récupérer les cinémas et les séances proposant ce film
            $cinemaSql = "SELECT c.nom_cinema, s.nom_salle, se.id_seance, se.heure_projection, se.langue, se.statut, se.prix
                          FROM cinema c
                          JOIN salle s ON c.id_cinema = s.id_cinema
                          JOIN seance se ON s.id_salle = se.id_salle
                          WHERE se.id_film = :filmId  AND se.statut != 'En cours'";
            $cinemaStmt = $pdo->prepare($cinemaSql);
            $cinemaStmt->bindParam(':filmId', $filmId);
            $cinemaStmt->execute();

            if ($cinemaStmt->rowCount() > 0) {
                // Utiliser un tableau pour organiser les séances par cinéma
                $seancesParCinema = [];

                foreach ($cinemaStmt as $row) {
                    $cinemaNom = $row['nom_cinema'];

                    // Ajouter la séance à la liste des séances pour ce cinéma
                    $seancesParCinema[$cinemaNom][] = [
                        'salleNom' => $row['nom_salle'],
                        'id_seance' => $row['id_seance'],
                        'heure_projection' => $row['heure_projection'],
                        'langue' => $row['langue'],
                        'statut' => $row['statut'],
                        'prix' => $row['prix']
                        // Ajoutez d'autres détails si nécessaire
                    ];
                }

                // Afficher les séances par cinéma
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
                        echo '<form action="reservation.php" method="post">';
                        echo '<input type="hidden" name="id_film" value="' . $filmId . '">';
                        echo '<input type="hidden" name="id_seance" value="' . $seance['id_seance'] . '">';
                        echo '<input type="submit" value="Réserver">';
                        echo '</form>';
                    }
                }
            } else {
                echo '<p>Aucun cinéma trouvé proposant ce film.</p>';
            }

            echo '</main>';
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

<?php require '../include/footer.inc.php'; ?>

</html>