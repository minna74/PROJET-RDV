<?php
// db_connect.php

// --- PARAMÈTRES DE CONNEXION À LA BASE DE DONNÉES ---
// ATTENTION : Remplacez ces valeurs par celles de votre base de données réelle !
$dbHost     = 'localhost'; // L'hôte de votre base de données (souvent 'localhost' ou '127.0.0.1')
$dbName     = 'gestion_rdv_medical'; // Le nom de votre base de données
$dbUser     = 'root';     // Votre nom d'utilisateur de la base de données
$dbPass     = '';         // Votre mot de passe de la base de données (laissez vide si pas de mot de passe)

// --- OPTIONS PDO ---
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Afficher les erreurs sous forme d'exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     // Récupérer les résultats sous forme de tableau associatif
    PDO::ATTR_EMULATE_PREPARES   => false,                // Désactiver l'émulation des requêtes préparées pour une meilleure sécurité
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"      // S'assurer que l'encodage est UTF-8
];

// --- TENTATIVE DE CONNEXION À LA BASE DE DONNÉES ---
try {
    // Création d'une nouvelle instance PDO
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8",
        $dbUser,
        $dbPass,
        $options
    );
    // echo "Connexion à la base de données réussie !"; // Pour débogage, à supprimer en production
} catch (PDOException $e) {
    // En cas d'erreur de connexion, arrêter le script et afficher un message d'erreur
    // IMPORTANT : En production, ne JAMAIS afficher $e->getMessage() directement à l'utilisateur
    // Enregistrez l'erreur dans un fichier log à la place (ex: error_log($e->getMessage());)
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// La variable $pdo est maintenant disponible pour être utilisée dans d'autres scripts
// qui incluent ce fichier.

?>