<?php
require '../db/db_connection.php';

$kw = 'gate flick, films, tmdb';
$desc = 'Page de connexion du site Gate Flick';
$title = 'Connexion | Gate Flick';

require '../include/header.inc.php';

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
                $_SESSION['user_id'] = $user['ID_Personne'];
                $_SESSION['user_role'] = $user['Role'];

                header('Location: ../index.php');
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

    <form method="post" action="">
        <label for="login">Login :</label>
        <input type="text" name="login" required>

        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" name="mot_de_passe" required>

        <input type="submit" value="Se connecter">
    </form>
    <p>Vous n'avez pas de compte ? <a href="inscription.php">Inscrivez-vous ici</a>.</p>

</main>

<?php require '../include/footer.inc.php'; ?>

</html>