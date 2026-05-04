<?php
$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'gestion_rdv_medical';
$user = getenv('DB_USER') ?: 'user';
$password = getenv('DB_PASSWORD') ?: 'password';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    $pdo = null;
    echo "<!-- DB error : " . $e->getMessage() . " -->";
}
?>
