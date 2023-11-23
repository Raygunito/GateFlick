<?php
session_start();

// Vérifier si l'utilisateur est déjà connecté, puis le rediriger vers la page d'accueil
if (isset($_SESSION["utilisateur_connecte"])) {
    header("Location: compte.php");
    exit;
}
$kw = 'gatd flick, films, tmdb';
$desc = 'Connexion page du site Gate Flick';
$title = 'Connexion | Gate Flick';
$css = '../styles/light.css';

require '../include/header.inc.php';
?>
<main>
    <h1>Connexion</h1>
    <div class="form-connexion">
        <form action="../scripts/authentifier.php" method="post">

            <div class="form-group-user">
                <label for="login">Nom d'utilisateur:</label>
                <input type="text" id="login" name="login" required>
            </div>

            <div class="form-group-password">
                <label for="mot_de_passe">Mot de passe:</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            </div>

            <div class="form-group-connexion">
                <input type="submit" value="Se Connecter" />
            </div>

            <div class="form-group-option">
                <a href="./inscription.php">Vous n'avez pas de compte ?</a>
            </div>
        </form>

        <!-- Zone pour afficher les messages d'erreur -->
        <div id="error-message" style="color: red;"></div>
    </div>
</main>
<?php
require '../include/footer.inc.php';
?>
</body>

</html>