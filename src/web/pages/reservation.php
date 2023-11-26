<?php
require '../db/db_connection.php';

$kw = 'gate flick, films, tmdb';
$desc = 'Page de réservation sur Gate Flick';
$title = 'Réservation | Gate Flick';
$filePath = "../";
require '../include/header.inc.php';

try {
    // Requête pour récupérer les réservations avec les détails de la séance, de la salle, du cinéma, et du siège
    $reservationSql =
        "SELECT t.*, s.heure_projection, s.langue, s.statut AS seance_statut, s.prix AS seance_prix, sg.numero_siege, sl.nom_salle, sl.type_projection, sl.taille_ecran, c.nom_cinema
                       FROM ticket t
                       JOIN siege sg ON t.id_siege = sg.id_siege
                       JOIN salle sl ON sg.id_salle = sl.id_salle
                       JOIN cinema c ON sl.id_cinema = c.id_cinema
                       JOIN seance s ON sg.id_salle = s.id_salle
                       WHERE s.id_film = :filmId AND s.id_seance = :seanceId";

    $reservationStmt = $pdo->prepare($reservationSql);

    // Ajout d'une vérification si les paramètres sont définis
    if (isset($_POST['id_film']) && isset($_POST['id_seance'])) {
        $reservationStmt->bindParam(':filmId', $_POST['id_film']);
        $reservationStmt->bindParam(':seanceId', $_POST['id_seance']);
        $reservationStmt->execute();

        echo '<main>';
        echo '<h1>Réservations</h1>';

        if ($reservationStmt->rowCount() > 0) {
            echo '<ul>';
            foreach ($reservationStmt as $reservation) {
                echo '<li>';
                echo 'ID Ticket : ' . $reservation['id_ticket'] . '<br>';
                echo 'Date de création : ' . $reservation['date_creation'] . '<br>';
                echo 'Date d\'expiration : ' . $reservation['date_expiration'] . '<br>';
                echo 'Statut d\'usage : ' . $reservation['statut_usage'] . '<br>';
                echo 'Remarque : ' . $reservation['remarque'] . '<br>';
                echo 'Numéro de siège : ' . $reservation['numero_siege'] . '<br>';
                echo 'Heure de projection : ' . $reservation['heure_projection'] . '<br>';
                echo 'Langue : ' . $reservation['langue'] . '<br>';
                echo 'Statut de la séance : ' . $reservation['seance_statut'] . '<br>';
                echo 'Prix de la séance : ' . $reservation['seance_prix'] . '<br>';
                echo 'Nom de la salle : ' . $reservation['nom_salle'] . '<br>';
                echo 'Type de projection : ' . $reservation['type_projection'] . '<br>';
                echo 'Taille de l\'écran : ' . $reservation['taille_ecran'] . '<br>';
                echo 'Nom du cinéma : ' . $reservation['nom_cinema'] . '<br>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>Aucune réservation trouvée.</p>';
        }

        echo '</main>';
    } else {
        echo '<p>Paramètres d\'ID de film et d\'ID de séance manquants.</p>';
    }
} catch (PDOException $e) {
    echo 'Erreur lors de la récupération des réservations : ' . $e->getMessage();
}

require '../include/footer.inc.php';
