<?php
require '../db/db_connection.php';

$kw = 'gate flick, films, tmdb';
$desc = 'Inscription page du site Gate Flick';
$title = 'Inscription | Gate Flick';
$css = '../styles/light.css';

require '../include/header.inc.php';

function generateId($role)
{
    $prefix = ($role === 'client') ? 'Cli' : 'Emp';

    $random_part = str_pad(rand(0, 9999), 8, '0', STR_PAD_LEFT);

    $id = $prefix . $random_part;

    return $id;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['nom']) &&
        isset($_POST['prenom']) &&
        isset($_POST['email']) &&
        isset($_POST['login']) &&
        isset($_POST['mot_de_passe'])
    ) {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $login = $_POST['login'];
        $mot_de_passe = $_POST['mot_de_passe'];

        try {
            $pdo->beginTransaction();

            $role = isset($_POST['role']) ? $_POST['role'] : '';

            $id_personne = generateId($role);

            $sql = "INSERT INTO Personne (ID_Personne, Nom_per, Prenom_per, Email_per, Login_per, Mot_de_passe_per)
                    VALUES (:id_personne, :nom, :prenom, :email, :login, :mot_de_passe)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id_personne', $id_personne);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':mot_de_passe', $mot_de_passe);
            $stmt->execute();

            $pdo->commit();

            echo '<p>Inscription réussie ! ID de la personne : ' . $id_personne . '</p>';
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo 'Erreur lors de l\'inscription : ' . $e->getMessage();
        }
    } else {
        echo '<p>Tous les champs sont requis.</p>';
    }
}
?>

<main>
    <h1>Inscription</h1>

    <!-- Formulaire d'inscription -->
    <form method="post" action="">
        <!-- Les champs du formulaire ici -->
        <label for="nom">Nom :</label>
        <input type="text" name="nom" required>

        <label for="prenom">Prénom :</label>
        <input type="text" name="prenom" required>

        <label for="email">Email :</label>
        <input type="email" name="email" required>

        <label for="login">Login :</label>
        <input type="text" name="login" required>

        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" name="mot_de_passe" required>

        <!-- Ajoutez le champ radio pour le rôle -->
        <label>Rôle :</label>
        <input type="radio" name="role" value="employe" required> Employé
        <input type="radio" name="role" value="client" required> Client

        <input type="submit" value="S'inscrire">
    </form>
</main>

<?php require '../include/footer.inc.php'; ?>

</html>