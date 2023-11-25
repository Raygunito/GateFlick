<?php

$configFile = '../db_login.json';
$configData = file_get_contents($configFile);
$config = json_decode($configData, true);
 
$host = $config['host'];
$port = $config['port'];
$dbname =$config['database']; 
$user =$config['user']; 
$password =$config['password'];

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}
