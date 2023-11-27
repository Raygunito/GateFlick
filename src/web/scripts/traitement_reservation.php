<?php
require '../db/db_connection.php';
// Vérifier si l'utilisateur est connecté
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/connexion.php');
    exit();
}
$idTicket = 'Com' . str_pad(rand(0, 9999), 9, '0', STR_PAD_LEFT);

$dateCreation = date('Y-m-d H:i:s');

$dateExpiration = date('Y-m-d H:i:s', strtotime('+7 days', strtotime($dateCreation)));

$statutUsage = 'En attente';

$remarque = NULL;

$idFilm = isset($_POST['id_film']) ? $_POST['id_film'] : null;
$idSeance = isset($_POST['id_seance']) ? $_POST['id_seance'] : null;

$siege = isset($_POST['siege']) ? $_POST['siege'] : null;


try {
    $checkSiegeSql =
        "SELECT s.statut 
                      FROM siege s
                      JOIN salle sa ON s.id_salle = sa.id_salle
                      JOIN seance se ON sa.id_salle = se.id_salle
                      JOIN cinema ci ON sa.id_cinema = ci.id_cinema
                      WHERE s.id_siege = :siege AND se.id_seance = :idSeance FOR UPDATE";


    $checkSiegeStmt = $pdo->prepare($checkSiegeSql);
    $checkSiegeStmt->bindParam(':siege', $siege);
    $checkSiegeStmt->bindParam(':idSeance', $idSeance);
    $checkSiegeStmt->execute();

    if ($checkSiegeStmt->rowCount() > 0) {
        $siegeStatut = $checkSiegeStmt->fetchColumn();

        if ($siegeStatut == 'Libre') {
            $pdo->beginTransaction();

            try {
                $insertTicketSql = "INSERT INTO ticket (id_ticket, date_creation, date_expiration, statut_usage, remarque, id_siege)
                VALUES (:idTicket, :dateCreation, :dateExpiration, :statutUsage, :remarque, :siege)";

                $insertTicketStmt = $pdo->prepare($insertTicketSql);
                $insertTicketStmt->bindParam(':idTicket', $idTicket);
                $insertTicketStmt->bindParam(':dateCreation', $dateCreation);
                $insertTicketStmt->bindParam(':dateExpiration', $dateExpiration);
                $insertTicketStmt->bindParam(':statutUsage', $statutUsage);
                $insertTicketStmt->bindParam(':remarque', $remarque);
                $insertTicketStmt->bindParam(':siege', $siege);
                $insertTicketStmt->execute();

                $updateSiegeSql = "UPDATE siege SET statut = 'Occupé' WHERE id_siege = :siege";
                $updateSiegeStmt = $pdo->prepare($updateSiegeSql);
                $updateSiegeStmt->bindParam(':siege', $siege);
                $updateSiegeStmt->execute();

                $sqlGetPrice = "SELECT prix FROM seance WHERE id_seance = :id";
                $getprice = $pdo->prepare($sqlGetPrice);
                $getprice->bindParam(":id", $idSeance);
                $getprice->execute();
                $rowPrice = $getprice->fetch(PDO::FETCH_ASSOC);

                $sqlAddToClient = "INSERT INTO Acheter (id_ticket, id_client, nombre_billet,prix_total) VALUES
                (:idTicket,:idClient,1,:prix_total)";
                $stmtAcheter = $pdo->prepare($sqlAddToClient);
                $stmtAcheter->bindParam(":idTicket", $idTicket);
                $stmtAcheter->bindParam(":idClient", $_SESSION['user_id']);
                $stmtAcheter->bindParam(':prix_total', $rowPrice['prix']);
                $stmtAcheter->execute();

                $pdo->commit();

                header("Location: ../pages/confirmation.php");
                exit();
            } catch (PDOException $e) {
                // En cas d'erreur, annulez la transaction
                $pdo->rollBack();
                echo 'Erreur lors du traitement de la réservation : ' . $e->getMessage();
            }
        } else {
            echo 'Erreur lors du traitement de la réservation : Le siège n\'est pas disponible.';
        }
    } else {
        echo 'Erreur lors du traitement de la réservation : Le siège n\'existe pas pour cette séance.';
    }
} catch (PDOException $e) {
    echo 'Erreur lors du traitement de la réservation : ' . $e->getMessage();
} catch (Exception $e) {
    echo 'Erreur lors du traitement de la réservation : ' . $e->getMessage();
}
