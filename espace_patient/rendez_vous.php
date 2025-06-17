<?php
session_start();
require_once 'db_connect.php';

// Rediriger si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: page_de_login.php');
    exit();
}

// Initialiser les variables avec des valeurs par défaut
$userName = $_SESSION['user_nom'] ?? 'Utilisateur';
$userPrenom = $_SESSION['user_prenom'] ?? '';

$appointments = [];
try {
    // Correction: Utilisation des bonnes tables et colonnes
    $stmt = $pdo->prepare("
        SELECT
            r.Date_RDV AS appointment_date,
            r.Heure AS appointment_time,
            m.Nom_med AS doctor_nom,
            r.Statut AS status
        FROM
            rendez_vous r
        JOIN
            medecin m ON r.ID_medecin = m.ID_medecin
        WHERE
            r.ID_patient = ?
        ORDER BY
            r.Date_RDV DESC, r.Heure DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $appointments = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des rendez-vous: " . $e->getMessage());
    $appointments = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mes Rendez-vous - Shafadmedcare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
    <style>
      body {
        min-height: 100vh;
        background: linear-gradient(120deg, #f8fafc 0%, #e0ecf7 100%);
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      }
      .navbar {
        background: #fff !important;
        border-bottom: 1px solid #e3eafc;
        box-shadow: 0 2px 8px 0 rgba(31,38,135,0.03);
        min-height: 56px;
      }
      .navbar .navbar-brand {
        color: #2563eb !important;
        font-weight: 700;
        font-size: 1.4rem;
        letter-spacing: 1px;
      }
      .navbar .nav-link {
        color: #1a237e !important;
        font-weight: 500;
        font-size: 1.07rem;
        margin-left: 10px;
        margin-right: 10px;
        transition: color 0.2s;
      }
      .navbar .nav-link.active,
      .navbar .nav-link:hover {
        color: #2563eb !important;
      }
      .navbar .dropdown-menu {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      }
      .navbar .dropdown-item {
        color: #4a5568;
      }
      .navbar .dropdown-item:hover {
        background-color: #f0f4f8;
        color: #2563eb;
      }
      .btn-primary {
        background-color: #2563eb;
        border-color: #2563eb;
      }
      .btn-primary:hover {
        background-color: #1e40af;
        border-color: #1e40af;
      }
      .header-hero {
        background: linear-gradient(120deg, #e0ecf7 60%, #f8fafc 100%);
        border-radius: 0 0 36px 36px;
        box-shadow: 0 4px 24px 0 rgba(31,38,135,0.06);
        padding: 48px 0 32px 0;
        margin-bottom: 0;
        text-align: center;
        position: relative;
      }
      .header-hero .welcome-message {
        font-size: 2rem;
        color: #1a237e;
        font-weight: 700;
        margin-bottom: 18px;
        letter-spacing: 0.5px;
      }
      .header-hero .subtitle {
        color: #2563eb;
        font-size: 1.15rem;
        margin-bottom: 32px;
        font-weight: 400;
      }
      .main-content {
        padding: 30px 0;
      }
      .rdv-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        padding: 30px;
      }
      .table th {
        color: #2563eb;
        font-weight: 600;
      }
      .table .badge {
        font-size: 0.85em;
        padding: 0.5em 0.75em;
      }
      .btn-custom {
        background-color: #60a5fa;
        color: white;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        transition: background-color 0.3s ease;
      }
      .btn-custom:hover {
        background-color: #3b82f6;
      }
      @media (max-width: 768px) {
        .header-hero .welcome-message {
          font-size: 1.8rem;
        }
        .header-hero .subtitle {
          font-size: 1rem;
        }
        .rdv-card {
          padding: 20px;
        }
      }
    </style>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg bg-light sticky-top">
      <div class="container-fluid">
        <a class="navbar-brand" href="proto.php">Shafadmedcare</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link" href="proto.php">Accueil</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="rendez_vous.php">Mes Rendez-vous</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="Mes_medecins.php">Mes Médecins</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="ordonance.php">Mes Ordonnances</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="document.php">Mes Résultats</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="messagerie.php">Messagerie</a>
            </li>
          </ul>
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-primary fw-semibold" href="#" id="patientDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Bonjour, <?php echo htmlspecialchars($userPrenom . ' ' . $userName); ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="profil.php">Mon profil</a></li>
                <li><a class="dropdown-item" href="logout.php">Déconnexion</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="header-hero">
      <div class="welcome-message">
        Mes Rendez-vous
      </div>
      <div class="subtitle">
        Se soigner, c’est aussi prendre soin de soi chaque jour.
      </div>
    </div>

    <div class="main-content container">
      <div class="rdv-card text-center">
        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead>
              <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Médecin</th>
                <th>Statut</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($appointments)): ?>
                <tr>
                  <td colspan="4">Aucun rendez-vous trouvé.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($appointments as $rdv): ?>
                  <tr>
                    <td><?php echo htmlspecialchars((new DateTime($rdv['appointment_date']))->format('d/m/Y')); ?></td>
                    <td><?php echo htmlspecialchars((new DateTime($rdv['appointment_time']))->format('H:i')); ?></td>
                    <td>Dr. <?php echo htmlspecialchars($rdv['doctor_nom']); ?></td>
                    <td>
                      <?php
                        $statusClass = '';
                        switch ($rdv['status']) {
                            case 'à venir':
                                $statusClass = 'bg-success';
                                break;
                            case 'passé':
                                $statusClass = 'bg-secondary';
                                break;
                            case 'annulé':
                                $statusClass = 'bg-danger';
                                break;
                            default:
                                $statusClass = 'bg-info';
                        }
                      ?>
                      <span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars(ucfirst($rdv['status'])); ?></span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <a href="Page_de_prise_de_rendez_vous.php" class="btn btn-custom mt-3"><i class="bi bi-plus-circle me-2"></i>Prendre un rendez-vous</a>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>