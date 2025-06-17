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

$doctors = [];
$successMessage = '';
$errorMessage = '';

// Récupérer les médecins depuis la base de données
try {
    $stmt = $pdo->query("SELECT id, nom, specialite FROM doctors ORDER BY nom");
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des médecins: " . $e->getMessage());
    $errorMessage = "Impossible de charger la liste des médecins.";
}

// Gérer la soumission du formulaire de rendez-vous
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rdv'])) {
    $selectedDoctorId = $_POST['medecin'] ?? '';
    $selectedDate = $_POST['date_rdv'] ?? '';
    $selectedTime = $_POST['creneau_rdv'] ?? '';
    $reason = htmlspecialchars(trim($_POST['raison_rdv'] ?? ''));

    if (empty($selectedDoctorId) || empty($selectedDate) || empty($selectedTime)) {
        $errorMessage = "Veuillez choisir un médecin, une date et un créneau horaire.";
    } else {
        try {
            // Vérifier si le créneau est encore disponible (simple vérification, pas de gestion de conflits complexes)
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? AND status = 'à venir'");
            $checkStmt->execute([$selectedDoctorId, $selectedDate, $selectedTime]);
            if ($checkStmt->fetchColumn() > 0) {
                $errorMessage = "Ce créneau est déjà pris. Veuillez en choisir un autre.";
            } else {
                // Insérer le nouveau rendez-vous
                $insertStmt = $pdo->prepare("
                    INSERT INTO appointments (user_id, doctor_id, appointment_date, appointment_time, reason, status)
                    VALUES (?, ?, ?, ?, ?, 'à venir')
                ");
                $insertStmt->execute([$userId, $selectedDoctorId, $selectedDate, $selectedTime, $reason]);

                $successMessage = "Votre rendez-vous a été pris avec succès !";
                // Optionnel: Réinitialiser les champs du formulaire après succès
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de la prise de rendez-vous: " . $e->getMessage());
            $errorMessage = "Une erreur est survenue lors de la prise de rendez-vous. Veuillez réessayer.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Prendre un rendez-vous - Shafadmedcare</title>
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
      .form-label {
        font-weight: 600;
        color: #333;
      }
      .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(37,99,235,0.25);
        border-color: #2563eb;
      }
      .medecin-select-card {
        cursor: pointer;
        border: 1px solid #e0e7ff;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        background-color: #f9fbff;
        transition: all 0.2s ease;
      }
      .medecin-select-card:hover, .medecin-select-card.selected {
        background-color: #e0ecf7;
        border-color: #2563eb;
      }
      .creneau-btn {
        background-color: #e0e7ff;
        border: 1px solid #c3d4f0;
        border-radius: 5px;
        padding: 8px 12px;
        margin: 5px;
        cursor: pointer;
        transition: all 0.2s ease;
      }
      .creneau-btn:hover, .creneau-btn.selected {
        background-color: #2563eb;
        color: white;
        border-color: #2563eb;
      }
      .alert-success-custom {
        background-color: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
        display: none; /* Hidden by default */
      }
      .alert-danger-custom {
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
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
              <a class="nav-link" href="rendez_vous.php">Mes Rendez-vous</a>
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
        Prendre un rendez-vous
      </div>
      <div class="subtitle">
        Choisissez votre médecin, la date et l'heure qui vous conviennent.
      </div>
    </div>

    <div class="main-content container">
      <div class="rdv-card">
        <h4 class="mb-4 text-primary">Nouveau rendez-vous</h4>

        <?php if (!empty($successMessage)): ?>
          <div class="alert alert-success-custom" style="display: block;">
            <?php echo htmlspecialchars($successMessage); ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
          <div class="alert alert-danger-custom" style="display: block;">
            <?php echo htmlspecialchars($errorMessage); ?>
          </div>
        <?php endif; ?>

        <form id="rdvForm" method="POST" action="Page_de_prise_de_rendez_vous.php">
          <div class="mb-3">
            <label for="medecin" class="form-label">Choisissez un médecin :</label>
            <select class="form-select" id="medecin" name="medecin" required>
              <option value="">-- Sélectionnez un médecin --</option>
              <?php foreach ($doctors as $doctor): ?>
                <option value="<?php echo htmlspecialchars($doctor['id']); ?>">
                  Dr. <?php echo htmlspecialchars($doctor['nom']); ?> (<?php echo htmlspecialchars($doctor['specialite']); ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="date_rdv" class="form-label">Choisissez une date :</label>
            <input type="date" class="form-control" id="date_rdv" name="date_rdv" required min="<?php echo date('Y-m-d'); ?>">
          </div>

          <div class="mb-3">
            <label for="creneau_rdv" class="form-label">Choisissez un créneau horaire :</label>
            <select class="form-select" id="creneau_rdv" name="creneau_rdv" required>
                <option value="">-- Sélectionnez un créneau --</option>
                <option value="09:00">09:00</option>
                <option value="09:30">09:30</option>
                <option value="10:00">10:00</option>
                <option value="10:30">10:30</option>
                <option value="11:00">11:00</option>
                <option value="11:30">11:30</option>
                <option value="14:00">14:00</option>
                <option value="14:30">14:30</option>
                <option value="15:00">15:00</option>
                <option value="15:30">15:30</option>
                <option value="16:00">16:00</option>
                <option value="16:30">16:30</option>
            </select>
            <small class="form-text text-muted">Les créneaux disponibles peuvent varier selon le médecin et la date.</small>
          </div>

          <div class="mb-3">
            <label for="raison_rdv" class="form-label">Raison de la consultation (optionnel) :</label>
            <textarea class="form-control" id="raison_rdv" name="raison_rdv" rows="3" placeholder="Décrivez brièvement la raison de votre visite."></textarea>
          </div>

          <button type="submit" name="submit_rdv" class="btn btn-primary btn-lg w-100">Confirmer le rendez-vous</button>
        </form>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // The JavaScript for dynamic creneaux based on doctor/date would require an AJAX call
      // to a PHP script that queries the database for available slots.
      // For this integration, basic creneaux are listed in the HTML select.
      // The form submission is now handled by PHP.

      // Hide success/error messages after a few seconds
      document.addEventListener('DOMContentLoaded', function() {
        const successMsg = document.querySelector('.alert-success-custom');
        const errorMsg = document.querySelector('.alert-danger-custom');

        if (successMsg && successMsg.style.display === 'block') {
          setTimeout(() => {
            successMsg.style.display = 'none';
          }, 5000); // Hide after 5 seconds
        }
        if (errorMsg && errorMsg.style.display === 'block') {
          setTimeout(() => {
            errorMsg.style.display = 'none';
          }, 5000); // Hide after 5 seconds
        }
      });
    </script>
  </body>
</html>