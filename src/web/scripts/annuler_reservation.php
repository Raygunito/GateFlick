<?php
require '../db/db_connection.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/connexion.php');
    exit();
}

if (isset($_GET['id_ticket'])) {
    $idTicket = $_GET['id_ticket'];

    try {
        $checkTicketSql = "SELECT statut_usage, date_expiration FROM ticket WHERE id_ticket = :idTicket";
        $checkTicketStmt = $pdo->prepare($checkTicketSql);
        $checkTicketStmt->bindParam(':idTicket', $idTicket);
        $checkTicketStmt->execute();
        $ticketInfo = $checkTicketStmt->fetch(PDO::FETCH_ASSOC);

        if ($ticketInfo['statut_usage'] !== 'Utilisé' && strtotime($ticketInfo['date_expiration']) > time()) {
            $pdo->beginTransaction();

            $getSeatSql = "SELECT id_siege FROM ticket WHERE id_ticket = :idTicket";
            $getSeatStmt = $pdo->prepare($getSeatSql);
            $getSeatStmt->bindParam(':idTicket', $idTicket);
            $getSeatStmt->execute();
            $seatId = $getSeatStmt->fetchColumn();

            $updateSiegeSql = "UPDATE siege SET statut = 'Libre' WHERE id_siege = :seatId";
            $updateSiegeStmt = $pdo->prepare($updateSiegeSql);
            $updateSiegeStmt->bindParam(':seatId', $seatId);
            $updateSiegeStmt->execute();

            $deleteCommandSql = "DELETE FROM acheter WHERE id_ticket = :idTicket";
            $deleteCommandStmt = $pdo->prepare($deleteCommandSql);
            $deleteCommandStmt->bindParam(':idTicket', $idTicket);
            $deleteCommandStmt->execute();

            $updateTicketStmt = "DELETE FROM ticket WHERE id_ticket = :idTicket";
            $updateTicketStmt = $pdo->prepare($updateTicketStmt);
            $updateTicketStmt->bindParam(':idTicket', $idTicket);
            $updateTicketStmt->execute();

            $pdo->commit();

            header("Location: ../pages/reservation.php");
            exit();
        } else {
            header("Location: ../pages/reservation.php?error=conditions_non_rencontrees");
            exit();
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Erreur lors de l'annulation de la réservation :  " . $e->getMessage();
    }
} else {
    header("Location: ../pages/reservation.php");
    exit();
}
