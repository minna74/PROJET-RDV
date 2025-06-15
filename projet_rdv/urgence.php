<?php
session_start();
require_once 'config.php';

// Rediriger si l'utilisateur n'est pas connecté
if (!isset($_SESSION['ID_medecin'])) {
    header("Location: login.php");
    exit;
}

// Récupérer les RDV urgents du médecin connecté
$id_medecin = $_SESSION['ID_medecin'];
$stmt = $pdo->prepare("
    SELECT r.Date_RDV, r.Heure, r.Motif, p.Nom_patient, p.Prenom_patient
    FROM rendez_vous r
    JOIN patient p ON r.ID_patient = p.ID_patient
    WHERE r.Statut = 'urgent' AND r.ID_medecin = ?
    ORDER BY r.Date_RDV, r.Heure
");
$stmt->execute([$id_medecin]);
$urgences = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rendez-vous urgents</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card-title {
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-danger"><i class="fas fa-exclamation-triangle"></i> Rendez-vous urgents</h3>
        <a href="doctor_dashboard.php" class="btn btn-outline-secondary">← Retour au tableau de bord</a>
    </div>

    <?php if (count($urgences) === 0): ?>
        <div class="alert alert-info">Aucun rendez-vous urgent pour l’instant.</div>
    <?php else: ?>
        <table class="table table-bordered bg-white shadow-sm">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Patient</th>
                    <th>Motif</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($urgences as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Date_RDV']) ?></td>
                        <td><?= htmlspecialchars(substr($row['Heure'], 0, 5)) ?></td>
                        <td><?= htmlspecialchars($row['Prenom_patient'] . ' ' . $row['Nom_patient']) ?></td>
                        <td><?= htmlspecialchars($row['Motif']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- FontAwesome for the alert icon -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
