<?php
require '../db/db_connection.php';

$kw = 'gate flick, films, tmdb';
$desc = 'Page de réservation sur Gate Flick';
$title = 'Réservation | Gate Flick';
$filePath = "../";
require '../include/header.inc.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/connexion.php');
    exit();
}

try {
    $userId = $_SESSION['user_id'];

    $reservationsSql = "SELECT t.id_ticket, f.titre, c.nom_cinema, sa.nom_salle, se.heure_projection, s.numero_siege, t.statut_usage
                        FROM acheter a
                        JOIN ticket t ON a.id_ticket = t.id_ticket
                        JOIN siege s ON t.id_siege = s.id_siege
                        JOIN salle sa ON s.id_salle = sa.id_salle
                        JOIN cinema c ON sa.id_cinema = c.id_cinema
                        JOIN seance se ON sa.id_salle = se.id_salle
                        JOIN film f ON se.id_film = f.id_film
                        WHERE a.id_client = :userId";

    $reservationsStmt = $pdo->prepare($reservationsSql);
    $reservationsStmt->bindParam(':userId', $userId);
    $reservationsStmt->execute();

    if ($reservationsStmt->rowCount() > 0) {
        echo '<h1>Mes Réservations</h1>';
        echo '<table border="1">';
        echo '<tr><th>ID Ticket</th><th>Titre du Film</th><th>Cinéma</th><th>Salle</th><th>Heure de Projection</th><th>Numéro de Siège</th><th>Statut</th><th>Action</th></tr>';
        foreach ($reservationsStmt as $row) {
            echo '<tr>';
            echo '<td>' . $row['id_ticket'] . '</td>';
            echo '<td>' . $row['titre'] . '</td>';
            echo '<td>' . $row['nom_cinema'] . '</td>';
            echo '<td>' . $row['nom_salle'] . '</td>';
            echo '<td>' . $row['heure_projection'] . '</td>';
            echo '<td>' . $row['numero_siege'] . '</td>';
            echo '<td>' . $row['statut_usage'] . '</td>';
            echo '<td><a href="../scripts/annuler_reservation.php?id_ticket=' . $row['id_ticket'] . '">Annuler la réservation</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<main><p>Aucune réservation trouvée.</p></main>';
    }
} catch (PDOException $e) {
    echo 'Erreur lors de la récupération des réservations : ' . $e->getMessage();
}

require '../include/footer.inc.php';
