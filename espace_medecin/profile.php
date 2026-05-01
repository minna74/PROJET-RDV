<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['ID_medecin'])) {
    header("Location: login.php");
    exit;
}

// Récupérer les infos du médecin connecté
$id = $_SESSION['ID_medecin'];
$stmt = $pdo->prepare("SELECT Nom_med, Prenom_med, email_med, Numtel_med, Specialite, Tarif, Horaires_disponible FROM medecin WHERE ID_medecin = ?");
$stmt->execute([$id]);
$medecin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medecin) {
    echo "Médecin introuvable.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil du médecin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Profil du Dr. <?= htmlspecialchars($medecin['Prenom_med'] . ' ' . $medecin['Nom_med']) ?></h4>
        </div>
        <div class="card-body">
            <p><strong>Email :</strong> <?= htmlspecialchars($medecin['email_med']) ?></p>
            <p><strong>Téléphone :</strong> <?= htmlspecialchars($medecin['Numtel_med']) ?></p>
            <p><strong>Spécialité :</strong> <?= htmlspecialchars($medecin['Specialite']) ?></p>
            <p><strong>Tarif consultation :</strong> <?= htmlspecialchars($medecin['Tarif']) ?> €</p>
            <p><strong>Horaires disponibles :</strong> <?= nl2br(htmlspecialchars($medecin['Horaires_disponible'])) ?></p>
            <a href="doctor_dashboard.php" class="btn btn-secondary">Retour au tableau de bord</a>
        </div>
    </div>
</div>
</body>
</html>
