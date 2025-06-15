<?php
session_start();
session_destroy();
header("Location: login.php"); // redirige vers la page de connexion
exit;
?>
