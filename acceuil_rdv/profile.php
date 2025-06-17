<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: login.php");
    exit();
}

$nom = $_SESSION['nom'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang=\"fr\">
<head><meta charset=\"UTF-8\"><title>Mon Profil</title></head>
<body>
  <h1>Bienvenue, <?= htmlspecialchars($nom) ?> !</h1>
  <p>Rôle : <strong><?= htmlspecialchars($role) ?></strong></p>
  <?php if ($role === 'medecin'): ?>
    <p>Accédez à vos horaires, vos patients et vos rendez-vous.</p>
  <?php elseif ($role === 'patient'): ?>
    <p>Consultez vos rendez-vous, vos ordonnances et votre dossier.</p>
  <?php elseif ($role === 'admin'): ?>
    <p>Panel d’administration : gestion des comptes, statistiques, etc.</p>
  <?php endif; ?>
  <a href=\"logout.php\">Se déconnecter</a>
</body>
</html>
