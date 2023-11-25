<?php
$kw = 'gate flick, films, tmdb';
$desc = 'Inscription page du site Gate Flick';
$title = 'Inscription | Gate Flick';
$css = '../styles/light.css';

require '../include/header.inc.php';
?>
<main>
    <h1>Inscription</h1>
    <div class="form-inscription">
        <form id="registration-form">
            <div class="form-group-user">
                <label for="username">Nom d'utilisateur:</label>
                <input type="text" id="username" name="username" required>
                <!-- Zone pour afficher les messages d'erreur -->
                <div id="error-message" style="color: red;"></div>
            </div>

            <div class="form-group-password">
                <label for="email-confirm">Adresse e-mail :</label>
                <input type="email" id="email-confirm" name="email-confirm" required>
                <!-- Zone pour afficher les messages de validation en temps réel -->
                <div id="email-validation" style="color: green;"></div>
            </div>

            <div class="form-group-mail">
                <label for="password">Mot de passe:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group-inscription">
                <button type="button" id="submit-button">S'inscrire</button>
            </div>

            <div class="form-group-option">
                <a href="login.php">Vous avez déjà un compte </a>
            </div>
        </form>
    </div>
</main>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="../scripts/inscription.js"></script>
</body>
<?php require '../include/footer.inc.php'; ?>

</html>