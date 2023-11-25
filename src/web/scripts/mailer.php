<?php
// Fonction pour l'envoi d'email (utilisez une bibliothèque comme PHPMailer pour une gestion plus avancée)
function sendMail($to, $subject, $message)
{
    // Paramètres supplémentaires pour l'en-tête du courrier
    $headers = "From: noreply@gate-flick.com" . "\r\n" .
        "X-Mailer: PHP/" . phpversion();

    // Envoi de l'e-mail
    $success = mail($to, $subject, $message, $headers);

    if ($success) {
        echo "L'e-mail a été envoyé avec succès.";
    } else {
        echo "Erreur lors de l'envoi de l'e-mail.";
    }
}
