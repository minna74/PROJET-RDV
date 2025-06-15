<?php
session_start();

// Détruire toutes les variables de session
$_SESSION = array();

// Si vous voulez détruire complètement la session, supprimez aussi le cookie de session.
// Note : cela détruira la session et non juste les données de session !
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalement, détruire la session.
session_destroy();

// Rediriger vers la page de connexion
header("Location: page_de_login.php");
exit();
?>