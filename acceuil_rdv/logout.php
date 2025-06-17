<?php
session_start();
session_destroy();
header("Location: index.php"); // redirige vers la page d'accueil
exit();
