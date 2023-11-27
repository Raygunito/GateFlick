<?php

$kw = 'gate flick, films, tmdb';
$desc = 'Inscription page du site Gate Flick';
$title = 'Inscription | Gate Flick';
$filePath = "../";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once $filePath . 'db/db_connection.php';
}
require_once $filePath . 'include/header.inc.php';
require_once $filePath . 'include/functions.inc.php';

?>

<main>
    <h1>Inscription</h1>

    <!-- Formulaire d'inscription -->
    <form method="post" action="inscription.php" class="form-container">
        <!-- Les champs du formulaire ici -->
        <label for="nom">Nom :<input id="nom" type="text" name="nom" required></label>
        <label for="prenom">Prénom :<input id="prenom" type="text" name="prenom" required></label>
        <label for="email">Email :<input id="email" type="email" name="email" required></label>
        <label for="login">Login :<input id="login" type="text" name="login" required></label>
        <label for="mot_de_passe">Mot de passe :<input id="mot_de_passe" type="password" name="mot_de_passe"
                required></label>
        <!-- Ajoutez le champ radio pour le rôle -->
        <label>Rôle :</label>
        <span>
            <input type="radio" name="role" value="client" required onclick="showClientFields()"> Client
        </span>
        <span>
            <input type="radio" name="role" value="employe" required onclick="showEmployeeFields()"> Employé
        </span>
        <div id="employeeFields" class="hidden">
            <label for="poste">Poste:<input id="poste" type="text"
                    name="poste"></label>
            <label for="niveau">Niveau:<input id="niveau" type="text"
                    name="niveau"></label>
            <label for="idCinema">Code cinéma:<input id="idCinema" type="text"
                    name="idCinema"></label>
        </div>

        <!-- Additional fields for Client -->
        <div id="clientFields" class="hidden">
            <label for="preferredLanguage">Langue préféré:
                <select name="preferredLanguage" id="preferredLanguage">
                    <option value="FR">FR</option>
                    <option value="EN">EN</option>
                    <option value="ES">ES</option>
                    <option value="IT">IT</option>
                    <option value="DE">DE</option>
                </select>
            </label>
            <label for="birthDate">Date de naissance:<input id="birthDate" type="date" name="birthDate"></label>
        </div>
        <input type="submit" value="S'inscrire">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo tryInscription($pdo);
        }
        ?>
        <p>Déjà un compte ? <a href="<?= $filePath ?>pages/connexion.php">Connectez-vous ici</a>.</p>
    </form>
</main>

<?php require_once $filePath . 'include/footer.inc.php'; ?>
