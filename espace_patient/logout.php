<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Le reste du code...
// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Réinitialiser toutes les variables de session
$_SESSION = array();

// Supprimer le cookie de session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 86400,  // Expiration dans le passé
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Détruire la session
session_destroy();

// Redirection vers inscription.php
header("HTTP/1.1 302 Found");
header("Location: inscription.php");
exit();
?>