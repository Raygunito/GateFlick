<?php

$kw = 'gate flick, films, tmdb';
$desc = 'Inscription page du site Gate Flick';
$title = 'Inscription | Gate Flick';
$filePath = "../";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once $filePath.'db/db_connection.php';
}
require_once $filePath.'include/header.inc.php';

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
            if (isset($_POST['role']) && $_POST['role'] == 'client') {
                $pdo->beginTransaction();
                $query2 = "INSERT INTO Client (ID_Client, Level, Carte_credit, Langue_prefere, Date_naissance)
                VALUES (:client_id, 'Normal', NULL, NULL, NULL);";
                $stmt2 = $pdo->prepare($query2);
                $stmt2->bindParam(":client_id", $id_personne);
                $stmt2->execute();
                $pdo->commit();
            } else {
                $pdo->beginTransaction();
                $query2 = "INSERT INTO Employee (ID_Employee, poste,niveau,id_cinema)
                VALUES (:id_employee, 'non_assigne', 'employee', NULL);";
                $stmt2 = $pdo->prepare($query2);
                $stmt2->bindParam(":id_employee", $id_personne);
                $stmt2->execute();
                $pdo->commit();
            }

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
            <input type="radio" name="role" value="client" required> Client
        </span>
        <span>
            <input type="radio" name="role" value="employe" required> Employé
        </span>

        <input type="submit" value="S'inscrire">
        <p>Déjà un compte ? <a href="<?=$filePath?>pages/connexion.php">Connectez-vous ici</a>.</p>
    </form>
</main>

<?php require '../include/footer.inc.php'; ?>

</html>