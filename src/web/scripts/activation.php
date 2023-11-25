<?php
require_once '../db/db_connection.php';

if (isset($_GET['code'])) {
    $activationCode = $_GET['code'];

    // Vérifier le code d'activation dans la base de données
    $stmt = $pdo->prepare("SELECT * FROM Personne WHERE Code_activation = :activation_code");
    $stmt->execute(['activation_code' => $activationCode]);
    $user = $stmt->fetch();

    if ($user) {
        // Activer le compte (à adapter selon votre logique)
        $stmt = $pdo->prepare("UPDATE Personne SET is_active = true WHERE ID_Personne = :user_id");
        $stmt->execute(['user_id' => $user['ID_Personne']]);

        // Rediriger l'utilisateur vers une page de confirmation
        header("Location: ../pages/confirmation.php");
        exit();
    } else {
        // Code d'activation invalide
        http_response_code(400);
        echo "Code d'activation invalide";
    }
} else {
    // Code d'activation non fourni
    http_response_code(400);
    echo "Bad Request";
}
