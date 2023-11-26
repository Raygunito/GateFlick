<?php
define('ATTRIBUTES_TO_EXCLUDE', ['id_personne', 'login_per', 'mot_de_passe_per', 'id_cinema', 'id_employee', 'id_client']);

define('ATTRIBUTES_BEAUTIFUL', [
    'nom_per' => 'Nom',
    'prenom_per' => 'Prénom',
    'email_per' => 'Email',
    'poste' => 'Poste',
    'niveau' => 'Niveau',
    'level' => 'Privilège',
    'carte_credit' => 'CB par défaut',
    'langue_prefere' => 'Langue préféré',
    'date_naissance' => 'Date de naissance'
]);

function replaceFromPost($key)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST[$key])) {
        return $_POST[$key];
    }
    return "";
}

function tryLogin($pdo, $filePath)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['login']) && isset($_POST['mot_de_passe'])) {
            $login = $_POST['login'];
            $mot_de_passe = $_POST['mot_de_passe'];

            try {
                $sql = "SELECT * FROM Personne WHERE Login_per = :login";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':login', $login);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (password_verify($mot_de_passe, $user['mot_de_passe_per'])) {
                        session_start();
                        $_SESSION['user_id'] = $user['id_personne'];

                        header('Location: ' . $filePath . 'index.php');
                        exit();
                    } else {
                        return '<p>Identifiants incorrects.</p>';
                    }
                } else {
                    return '<p>Identifiants incorrects.</p>';
                }
            } catch (PDOException $e) {
                return 'Erreur lors de la connexion : ' . $e->getMessage();
            }
        } else {
            return '<p>Tous les champs sont requis.</p>';
        }
    }
}

function generateId($role)
{
    $prefix = ($role === 'client') ? 'Cli' : 'Emp';

    $random_part = str_pad(rand(0, 9999), 8, '0', STR_PAD_LEFT);

    $id = $prefix . $random_part;

    return $id;
}

function tryInscription($pdo)
{

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
                $getMail = "SELECT Email_per FROM Personne WHERE Email_per = :mail";
                $checkMail = $pdo->prepare($getMail);
                $checkMail->bindParam(':mail', $email);
                $checkMail->execute();
                if ($checkMail->rowCount() > 0) {
                    return '<p>Email déjà utilisé !</p>';
                }
                $pdo->beginTransaction();
                $role = isset($_POST['role']) ? $_POST['role'] : '';
                $hashedPassword = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                $id_personne = generateId($role);

                $sql = "INSERT INTO Personne (ID_Personne, Nom_per, Prenom_per, Email_per, Login_per, Mot_de_passe_per)
                    VALUES (:id_personne, :nom, :prenom, :email, :login, :mot_de_passe)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id_personne', $id_personne);
                $stmt->bindParam(':nom', $nom);
                $stmt->bindParam(':prenom', $prenom);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':login', $login);
                $stmt->bindParam(':mot_de_passe', $hashedPassword);
                $stmt->execute();

                $pdo->commit();
                if (isset($_POST['role']) && $_POST['role'] == 'client') {
                    $pdo->beginTransaction();
                    $query2 = "INSERT INTO Client (ID_Client, Level, Carte_credit, Langue_prefere, Date_naissance)
                VALUES (:client_id, 'New', NULL , 'French', NULL);";
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

                return '<p>Inscription réussie !</p>';
            } catch (PDOException $e) {
                $pdo->rollBack();
                return 'Erreur lors de l\'inscription : ' . $e->getMessage();
            }
        } else {
            return '<p>Tous les champs sont requis.</p>';
        }
    }
}

function showInfo($sessionUserID, $pdo)
{
    $role = 'Client';
    $res = '';
    if (str_contains($sessionUserID, 'Emp')) {
        $role = 'Employee';
    }
    $profile = getFullInfoUser($sessionUserID, $pdo, $role);
    foreach ($profile as $attr => $info) {
        if (!in_array($attr, ATTRIBUTES_TO_EXCLUDE)) {
            $res .= "<p>" . ATTRIBUTES_BEAUTIFUL[$attr] . " : " . $info . "</p>";
        }
    }
    return $res;
}


function getFullInfoUser($sessionUserID, $pdo, $role)
{
    $query = 'SELECT * FROM Personne NATURAL JOIN ' . $role . ' WHERE id_personne = :id';
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $sessionUserID);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row;
}
?>