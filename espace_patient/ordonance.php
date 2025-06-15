<?php
session_start();
require_once 'db_connect.php';

// Rediriger si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: page_de_login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_nom'] ?? 'Utilisateur';
$userPrenom = $_SESSION['user_prenom'] ?? '';

$prescriptions = [];
try {
    // Récupérer les ordonnances de l'utilisateur
    // Assurez-vous que le `file_path` pointe vers un fichier réel ou un script qui sert le fichier
    $stmt = $pdo->prepare("SELECT id, issue_date, description, file_path FROM prescriptions WHERE user_id = ? ORDER BY issue_date DESC");
    $stmt->execute([$userId]);
    $prescriptions = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des ordonnances: " . $e->getMessage());
    $prescriptions = []; // Assurez-vous que c'est un tableau vide en cas d'erreur
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mes Ordonnances - Shafadmedcare</title>
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
        color: #4a5568 !important;
        font-weight: 500;
        margin-right: 15px;
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
        background: linear-gradient(45deg, #2563eb 0%, #60a5fa 100%);
        color: white;
        padding: 60px 0;
        text-align: center;
        border-radius: 0 0 15px 15px;
        margin-bottom: 30px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      }
      .header-hero .welcome-message {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        line-height: 1.2;
      }
      .header-hero .subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        max-width: 700px;
        margin: 0 auto;
      }
      .main-content {
        padding: 30px 0;
      }
      .ordonance-card, .documents-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        padding: 30px;
      }
      .list-group-item {
        border: 1px solid #e0e7ff;
        border-radius: 8px;
        margin-bottom: 10px;
        padding: 15px;
      }
      .list-group-item:last-child {
        margin-bottom: 0;
      }
      @media (max-width: 768px) {
        .header-hero .welcome-message {
          font-size: 1.8rem;
        }
        .header-hero .subtitle {
          font-size: 1rem;
        }
        .ordonance-card, .documents-card {
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
              <a class="nav-link" href="rendez_vous.php">Mes Rendez-vous</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="Mes_medecins.php">Mes Médecins</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="ordonance.php">Mes Ordonnances</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="documents.php">Mes Résultats</a>
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
        Mes Ordonnances
      </div>
      <div class="subtitle">
        Retrouvez ici toutes vos ordonnances médicales à télécharger.
      </div>
    </div>

    <div class="main-content container">
      <div class="ordonance-card">
        <ul class="list-group">
          <?php if (empty($prescriptions)): ?>
            <li class="list-group-item text-center">Aucune ordonnance trouvée.</li>
          <?php else: ?>
            <?php foreach ($prescriptions as $ordonnance): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Ordonnance du <?php echo htmlspecialchars((new DateTime($ordonnance['issue_date']))->format('d/m/Y')); ?>
                <?php if (!empty($ordonnance['description'])): ?>
                  - <?php echo htmlspecialchars($ordonnance['description']); ?>
                <?php endif; ?>
                <?php if (!empty($ordonnance['file_path'])): ?>
                    <a href="<?php echo htmlspecialchars($ordonnance['file_path']); ?>" class="btn btn-sm btn-primary" download><i class="bi bi-download me-1"></i>Télécharger</a>
                <?php else: ?>
                    <span class="text-muted">Fichier non disponible</span>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>