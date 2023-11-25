<?php
// register_user.php
require_once '../db/db_connection.php';
require_once 'mailer.php';
require_once '../include/functions.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Vérifier si l'utilisateur existe déjà
        $stmt = $pdo->prepare("SELECT * FROM Personne WHERE login_per = :username OR email_per = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            echo "exists";
        } else {
            // Hacher le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Générer le code d'activation
            $activationCode = uniqid();

            // Insérer l'utilisateur dans la base de données avec le code d'activation
            $stmt = $pdo->prepare("INSERT INTO Personne (Nom_per, Email_per, Mot_de_passe_per, Code_activation) VALUES (:username, :email, :password, :activation_code)");
            $stmt->execute(['username' => $username, 'email' => $email, 'password' => $hashedPassword, 'activation_code' => $activationCode]);

            // Envoyer l'email d'activation
            $activationLink = "http://gate-flick.alwaysdata.net/scripts/activation.php?code=$activationCode";

            $subject = "Activation de votre compte Gate Flick";
            $message = "Bienvenue sur Gate Flick!\n\n";
            $message .= "Cliquez sur le lien suivant pour activer votre compte:\n$activationLink";

            sendMail($email, $subject, $message);

            echo "success";
        }
    } catch (Exception $e) {
        // Imprimer l'exception pour le débogage
        echo "Erreur lors de l'inscription : " . $e->getMessage();

        // Ajouter des informations détaillées sur l'erreur SQL
        $errorInfo = $stmt->errorInfo();
        echo "\nSQLSTATE error code: " . $errorInfo[0];
        echo "\nDriver-specific error code: " . $errorInfo[1];
        echo "\nDriver-specific error message: " . $errorInfo[2];
    }
} else {
    // Requête non autorisée
    http_response_code(403);
    echo "Forbidden";
}
