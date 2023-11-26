<?php

$kw = 'gate flick, films, tmdb';
$desc = 'Page de connexion du site Gate Flick';
$title = 'Connexion | Gate Flick';
$filePath = "../";
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    require_once $filePath.'db/db_connection.php';
}
require_once $filePath.'include/header.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login']) && isset($_POST['mot_de_passe'])) {
        $login = $_POST['login'];
        $mot_de_passe = $_POST['mot_de_passe'];

        try {
            $sql = "SELECT * FROM Personne WHERE Login_per = :login AND Mot_de_passe_per = :mot_de_passe";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':mot_de_passe', $mot_de_passe);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                session_start();
                $_SESSION['user_id'] = $user['id_personne'];

                header('Location: '.$filePath.'index.php');
                exit();
            } else {
                echo '<p>Identifiants incorrects.</p>';
            }
        } catch (PDOException $e) {
            echo 'Erreur lors de la connexion : ' . $e->getMessage();
        }
    } else {
        echo '<p>Tous les champs sont requis.</p>';
    }
}
?>

<main>
    <h1>Connexion</h1>

    <form method="post" action="<?=$filePath?>pages/connexion.php" class="form-container">
        <label for="login">Login :<input id="login" type="text" name="login" required></label>
        <label for="mot_de_passe">Mot de passe :<input id="mot_de_passe" type="password" name="mot_de_passe" required></label>
        <input type="submit" value="Se connecter">
        <p>Vous n'avez pas de compte ? <a href="<?=$filePath?>pages/inscription.php">Inscrivez-vous ici</a>.</p>
    </form>

</main>

<?php require '../include/footer.inc.php'; ?>

</html>