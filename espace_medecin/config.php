<?php
$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'gestion_rdv_medical';
$username = getenv('DB_USER') ?: 'user';
$password = getenv('DB_PASSWORD') ?: 'password';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
