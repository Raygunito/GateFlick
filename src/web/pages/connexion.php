<?php

$kw = 'gate flick, films, tmdb';
$desc = 'Page de connexion du site Gate Flick';
$title = 'Connexion | Gate Flick';
$filePath = "../";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once $filePath . 'db/db_connection.php';
}
require_once $filePath . "include/functions.inc.php";
require_once $filePath . 'include/header.inc.php';

?>

<main>
    <h1>Connexion</h1>

    <form method="post" action="<?= $filePath ?>pages/connexion.php" class="form-container">
        <label for="login">Login :<input id="login" type="text" name="login" value="<?= replaceFromPost("login")?>" required></label>
        <label for="mot_de_passe">Mot de passe :<input id="mot_de_passe" type="password" name="mot_de_passe"
                required></label>
        <input type="submit" value="Se connecter">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo tryLogin($pdo, $filePath);
        }
        ?>
        <p>Vous n'avez pas de compte ? <a href="<?= $filePath ?>pages/inscription.php">Inscrivez-vous ici</a>.</p>
    </form>

</main>
<?php require_once $filePath.'include/footer.inc.php'; ?>