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

$myDoctors = [];
try {
    // Récupérer les médecins de la base de données.
    // Idéalement, vous voudriez ne récupérer que les médecins avec lesquels l'utilisateur a eu des interactions.
    // Pour cet exemple, nous allons récupérer tous les médecins pour illustrer le remplissage dynamique.
    $stmt = $pdo->query("SELECT id, nom, specialite, email, telephone, photo_url FROM doctors ORDER BY nom");
    $myDoctors = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des médecins: " . $e->getMessage());
    $myDoctors = []; // Assurez-vous que c'est un tableau vide en cas d'erreur
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <title>Mes médecins consultés - Shafadmedcare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
    <style>
      body {
        min-height: 100vh;
        background: linear-gradient(120deg, #f8fafc 0%, #e0ecf7 100%);
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        position: relative;
        overflow-x: hidden;
      }
      /* Bulles décoratives animées */
      .bg-bubble {
        position: fixed;
        border-radius: 50%;
        opacity: 0.13;
        z-index: 0;
        animation: float 12s infinite alternate;
        pointer-events: none;
      }
      .bg-bubble.b1 { width: 180px; height: 180px; background: #2563eb; left: -60px; top: 80px; animation-delay: 0s;}
      .bg-bubble.b2 { width: 150px; height: 150px; background: #60a5fa; right: -50px; bottom: 100px; animation-delay: 2s;}
      .bg-bubble.b3 { width: 120px; height: 120px; background: #93c5fd; left: 10%; bottom: 20%; animation-delay: 4s;}
      .bg-bubble.b4 { width: 200px; height: 200px; background: #2563eb; right: 20%; top: 50px; animation-delay: 6s;}
      .bg-bubble.b5 { width: 100px; height: 100px; background: #60a5fa; left: 5%; top: 70%; animation-delay: 8s;}
      @keyframes float {
        0% { transform: translateY(0px) translateX(0px); }
        50% { transform: translateY(-15px) translateX(10px); }
        100% { transform: translateY(0px) translateX(0px); }
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
        position: relative; /* For z-index to work against bubbles */
        z-index: 10;
      }
      .medecin-list-container {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        padding: 30px;
      }
      .medecin-card {
        display: flex;
        align-items: center;
        padding: 15px;
        border: 1px solid #e0e7ff;
        border-radius: 10px;
        margin-bottom: 15px;
        background-color: #f9fbff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        flex-wrap: wrap; /* Allow wrapping on smaller screens */
      }
      .medecin-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 20px;
        border: 3px solid #2563eb;
        flex-shrink: 0;
      }
      .medecin-info-group {
        flex-grow: 1;
        margin-right: 20px; /* Space before actions */
      }
      .medecin-nom {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2563eb;
        margin-bottom: 5px;
      }
      .medecin-specialite {
        font-size: 0.95rem;
        color: #666;
        margin-bottom: 10px;
      }
      .medecin-contact {
        font-size: 0.9rem;
        color: #555;
        margin-bottom: 5px;
      }
      .medecin-contact i {
        color: #60a5fa;
        margin-right: 5px;
      }
      .medecin-actions {
        display: flex;
        gap: 15px;
        flex-shrink: 0; /* Prevent actions from shrinking */
        margin-top: 10px; /* For mobile layout */
        width: 100%; /* For mobile layout */
        justify-content: flex-end; /* Align actions to the right */
      }
      .medecin-actions a {
        color: #2563eb;
        text-decoration: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        transition: color 0.2s ease;
      }
      .medecin-actions a:hover {
        color: #1e40af;
      }
      .medecin-actions i {
        font-size: 1.2rem;
        margin-right: 5px;
      }

      @media (min-width: 768px) {
        .medecin-card {
          flex-wrap: nowrap; /* Prevent wrapping on larger screens */
        }
        .medecin-info-group {
          margin-right: auto; /* Push actions to the right */
        }
        .medecin-actions {
          margin-top: 0;
          width: auto;
        }
      }
      @media (max-width: 768px) {
        .header-hero .welcome-message {
          font-size: 1.8rem;
        }
        .header-hero .subtitle {
          font-size: 1rem;
        }
        .medecin-list-container {
          padding: 20px;
        }
        .medecin-avatar {
          margin-bottom: 10px;
          margin-right: 0;
        }
        .medecin-info-group {
          text-align: center;
          width: 100%;
          margin-right: 0;
        }
        .medecin-actions {
            justify-content: center;
        }
      }
    </style>
  </head>
  <body>
    <div class="bg-bubble b1"></div>
    <div class="bg-bubble b2"></div>
    <div class="bg-bubble b3"></div>
    <div class="bg-bubble b4"></div>
    <div class="bg-bubble b5"></div>

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
              <a class="nav-link active" aria-current="page" href="Mes_medecins.php">Mes Médecins</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="ordonance.php">Mes Ordonnances</a>
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
        Mes médecins
      </div>
      <div class="subtitle">
        Retrouvez les médecins que vous avez consultés et leur spécialité.
      </div>
    </div>

    <div class="main-content container">
      <div class="medecin-list-container">
        <ul class="list-unstyled">
          <?php if (empty($myDoctors)): ?>
            <li class="text-center">Aucun médecin trouvé dans votre liste.</li>
          <?php else: ?>
            <?php foreach ($myDoctors as $doctor): ?>
              <li class="medecin-card">
                <img src="<?php echo htmlspecialchars($doctor['photo_url'] ?: 'https://via.placeholder.com/70'); ?>" class="medecin-avatar" alt="Dr. <?php echo htmlspecialchars($doctor['nom']); ?>" />
                <div class="medecin-info-group">
                  <div class="medecin-nom">Dr. <?php echo htmlspecialchars($doctor['nom']); ?></div>
                  <div class="medecin-specialite"><?php echo htmlspecialchars($doctor['specialite']); ?></div>
                  <div class="medecin-contact"><i class="bi bi-envelope me-1"></i> <?php echo htmlspecialchars($doctor['email']); ?></div>
                  <div class="medecin-contact"><i class="bi bi-telephone me-1"></i> <?php echo htmlspecialchars($doctor['telephone']); ?></div>
                </div>
                <div class="medecin-actions">
                  <a href="messagerie.php?doctor_id=<?php echo htmlspecialchars($doctor['id']); ?>" title="Envoyer un message"><i class="bi bi-chat-dots"></i> Message</a>
                  <a href="rendez_vous.php?doctor_id=<?php echo htmlspecialchars($doctor['id']); ?>" title="Voir les rendez-vous"><i class="bi bi-calendar-event"></i> RDV</a>
                </div>
              </li>
            <?php endforeach; ?>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>