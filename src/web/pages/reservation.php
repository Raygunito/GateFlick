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

function error()
{
    if (isset($_GET['error']) && !empty($_GET['error']) && $_GET['error'] == "conditions_non_rencontrees") {
        return "<p>Vous ne pouvez pas annuler une réservation avec un ticket déjà utilisé ou expiré !</p>\n";
    }
}



?>
<main>
    <h1>Mes Réservations</h1>
    <?php
    echo error();
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
            echo '<table border="1">';
            echo '<tr><th>ID Ticket</th><th>Titre du Film</th><th>Cinéma</th><th>Salle</th><th>Heure de Projection</th><th>Numéro de Siège</th><th>Statut</th><th>Action</th></tr>';
            foreach ($reservationsStmt as $row) {
                echo '<tr>';
                echo '<td>' . $row['id_ticket'] . "</td>\n";
                echo '<td>' . $row['titre'] . "</td>\n";
                echo '<td>' . $row['nom_cinema'] . "</td>\n";
                echo '<td>' . $row['nom_salle'] . "</td>\n";
                echo '<td>' . $row['heure_projection'] . "</td>\n";
                echo '<td>' . $row['numero_siege'] . "</td>\n";
                echo '<td>' . $row['statut_usage'] . "</td>\n";
                echo '<td><a href="../scripts/annuler_reservation.php?id_ticket=' . $row['id_ticket'] . '">Annuler la réservation</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>Aucune réservation trouvée.</p>';
        }
    } catch (PDOException $e) {
        echo 'Erreur lors de la récupération des réservations : ' . $e->getMessage();
    }
    ?>
</main>
<?php require '../include/footer.inc.php'; ?>