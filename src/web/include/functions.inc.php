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
                    }
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

function afficherSiegesDisponibles($pdo, $seanceId)
{
    $siegeSql = "SELECT id_siege FROM siege WHERE id_salle IN (SELECT id_salle FROM seance WHERE id_seance = :seanceId) AND statut = 'Libre'";
    $siegeStmt = $pdo->prepare($siegeSql);
    $siegeStmt->bindParam(':seanceId', $seanceId);
    $siegeStmt->execute();

    echo 'Choisissez votre siège : ';
    echo '<select name="siege" class="siege">';
    foreach ($siegeStmt as $row) {
        echo '<option value="' . $row['id_siege'] . '">' . $row['id_siege'] . '</option>';
    }
    echo '</select>';
}


function printFilmDetails($filmDetails)
{
    echo "<section>";
    echo '<h2>' . $filmDetails['titre'] . "</h2>\n";
    echo '<p>Nation : ' . $filmDetails['nation'] . "</p>\n";
    echo '<p>Durée : ' . $filmDetails['duree'] . "</p>\n";
    echo '<p>Metteur en scène : ' . $filmDetails['metteur_en_scene'] . "</p>\n";
    echo '<p>Date de sortie : ' . $filmDetails['date_sortie'] . "</p>\n";
    echo "</section>\n";
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
                    $langue = $_POST['preferredLanguage'];
                    $birth = $_POST['birthDate'];
                    $pdo->beginTransaction();
                    $query2 = "INSERT INTO Client (ID_Client, Level, Carte_credit, Langue_prefere, Date_naissance)
                VALUES (:client_id, 'New', NULL , :lang, :birth);";
                    $stmt2 = $pdo->prepare($query2);
                    $stmt2->bindParam(":client_id", $id_personne);
                    $stmt2->bindParam(":lang", $langue);
                    $stmt2->bindParam(":birth", $birth);
                    $stmt2->execute();
                    $pdo->commit();
                } else {
                    $pdo->beginTransaction();
                    $poste = $_POST['poste'];
                    $niveau = $_POST['niveau'];
                    $idCine = $_POST['idCinema'];

                    $sql = 'SELECT * FROM cinema WHERE id_cinema = :cine';
                    $stCine = $pdo->prepare($sql);
                    $stCine->bindParam(":cine", $idCine);
                    $stCine->execute();
                    if ($stCine->rowCount() <= 0) {
                        return "<p>L'ID Cinéma n'existe pas </p>";
                    }

                    $query2 = "INSERT INTO Employee (ID_Employee, poste,niveau,id_cinema)
                VALUES (:id_employee, :poste, :niveau, :idC);";
                    $stmt2 = $pdo->prepare($query2);
                    $stmt2->bindParam(":id_employee", $id_personne);
                    $stmt2->bindParam(":poste", $poste);
                    $stmt2->bindParam(":niveau", $niveau);
                    $stmt2->bindParam(":idC", $idCine);
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
    if (str_contains($sessionUserID, 'Cli')) {
        $res .= getSpecialClientInfo($pdo, $profile['id_client']);

    }
    return $res;
}


function getFullInfoUser($sessionUserID, $pdo, $role)
{
    if ($role == "Employee"){
        $join = "e.id_employee";
        $on = "e";
    }else{
        $join = "c.id_client";
        $on = "c";
    }
    $query = 'SELECT * FROM Personne p JOIN ' . $role ." ". $on.' ON p.id_personne = '. $join.' WHERE id_personne = :id';
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $sessionUserID);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row;
}
function getSpecialClientInfo($pdo, $idClient)
{
    $query = 'SELECT SUM(a.prix_total) AS Total_Spent
            FROM acheter a
            JOIN ticket t ON a.id_ticket = t.id_ticket
            JOIN siege s ON t.id_siege = s.id_siege
            JOIN salle sal ON s.id_salle = sal.id_salle
            JOIN cinema c ON sal.id_cinema = c.id_cinema
            WHERE a.id_client = :idClient';
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':idClient', $idClient);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row['total_spent'] <= 0){
            $amount = 0;
        }else{
            $amount = $row['total_spent'];
        }
        return "<p>Montant dépensé chez GateFlick : " . $amount . "&euro;</p>\n";
    }
    return "<p>Erreur lors de la récupération du montant dépensé chez GateFlick</p>";
}

function generateAdminTicketSection($pdo)
{

    $query = "SELECT * FROM Ticket NATURAL JOIN Siege NATURAL JOIN Salle NATURAL JOIN Cinema WHERE statut_usage = 'Problème'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($rows) > 0) {
        foreach ($rows as $row) {
            $idCom = $row["id_ticket"];
            $cinema = $row["nom_cinema"];
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            generateHorizontalCard($idCom, $cinema, $path . '?idCom=' . $idCom . '&decline=True', $path . '?idCom=' . $idCom . '&decline=False');
        }
    } else {
        echo "<p>Aucune commande avec des soucis.</p>";
    }
}

function generateHorizontalCard($title, $description, $linkRefuse, $linkAccept)
{
    $html = '
        <div class="horizontal-card">' . "\n" . '
            <div class="card-content">' . "\n" . '
                <h3 class="card-title">' . $title . '</h3>' . "\n" . '
                <p class="card-description">' . $description . '</p>' . "\n" . '
                <a href="' . $linkRefuse . '" class="card-link">Non utilisé</a>' . "\n" . '
                <a href="' . $linkAccept . '" class="card-link">Utilisé</a>' . "\n" . '
            </div>' . "\n" . '
        </div>' . "\n";

    echo $html;
}

function generateAdminStatsSection($pdo)
{
    generateAdminChiffreAffaireArticle($pdo);
    generateAdminCurrentSeanceArticle($pdo);
    generateAdminPlusFideleClientArticle($pdo);
}

function generateAdminCurrentSeanceArticle($pdo)
{
    $query = "SELECT c.nom_cinema, f.titre
                FROM seance se
                    JOIN film f ON se.id_film = f.id_film
                    JOIN salle sal ON se.id_salle = sal.id_salle
                    JOIN cinema c ON sal.id_cinema = c.id_cinema
                WHERE se.heure_projection <= CURRENT_TIMESTAMP AND CURRENT_TIMESTAMP <= se.heure_projection + INTERVAL '2 day'
                GROUP BY c.nom_cinema, f.titre;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // var_dump($row);
    echo "<article id='stat'>\n";
    echo "<h3>Les séances en cours :</h3>\n";
    foreach ($row as $value) {
        $name = $value['nom_cinema'];
        $revenu = $value['titre'];
        echo '<p>Nom cinéma' . " : " . $name . "</p>\n";
        echo '<p>Film en cours' . " : " . $revenu . "</p>\n";
    }
    echo "</article>\n";
}

function generateAdminChiffreAffaireArticle($pdo)
{
    $query = "SELECT 
    c.nom_cinema,
    SUM(a.prix_total) AS Total_Revenue
        FROM acheter a
        JOIN ticket t ON a.id_ticket = t.id_ticket
        JOIN siege s ON t.id_siege = s.id_siege
        JOIN salle sal ON s.id_salle = sal.id_salle
        JOIN cinema c ON sal.id_cinema = c.id_cinema
    GROUP BY c.nom_cinema ORDER BY Total_Revenue DESC;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // var_dump($row);
    echo "<article id='stat'>\n";
    echo "<h3>Revenu total :</h3>\n";
    foreach ($row as $value) {
        $name = $value['nom_cinema'];
        $revenu = $value['total_revenue'];
        echo '<p>Nom cinéma' . " : " . $name . "</p>\n";
        echo '<p>Revenu total' . " : " . $revenu . "&euro;</p>\n";
    }
    echo "</article>\n";
}

function generateAdminPlusFideleClientArticle($pdo)
{
    $query = "SELECT p.*,c.*, SUM(a.prix_total) AS total_depense
                FROM Client c
                JOIN Personne p ON c.ID_Client = p.ID_personne
                JOIN Acheter a ON c.ID_Client = a.ID_Client
                GROUP BY c.ID_Client,p.id_personne
                ORDER BY total_depense DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $i = 0;
    echo "<article id='stat'>\n";
    echo "<h3>Top 5 des clients fidèles :</h3>\n";
    foreach ($row as $value) {
        if ($i < 5) {
            $name = $value['nom_per'];
            $surname = $value['prenom_per'];
            $revenu = $value['total_depense'];
            echo '<p>Nom et prénom' . " : " . $name . " " . $surname . "</p>\n";
            echo '<p>Total dépensé' . " : " . $revenu . "&euro;</p>\n";
        } else {
            break;
        }
        $i++;
    }
    echo "</article>\n";

}


?>