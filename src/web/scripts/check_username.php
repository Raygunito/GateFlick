<?php
require_once '../db/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];

    // Vérifier si le nom d'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Personne WHERE login_per = :username");
    $stmt->execute(['username' => $username]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo "exists";
    } else {
        echo "available";
    }
} else {
    http_response_code(403);
    echo "Forbidden";
}
