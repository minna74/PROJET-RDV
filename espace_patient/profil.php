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

$userData = [];
$successMessage = '';
$errorMessage = '';

// Récupérer les données de l'utilisateur depuis la base de données
try {
    $stmt = $pdo->prepare("SELECT nom, prenom, date_de_naissance, email, telephone, adresse, ville, code_postal FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        // Cela ne devrait pas arriver si l'utilisateur est connecté, mais par sécurité
        header('Location: logout.php'); // Déconnecter l'utilisateur si ses données ne sont pas trouvées
        exit();
    }
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération du profil: " . $e->getMessage());
    $errorMessage = "Impossible de charger les informations de votre profil.";
}

// Gérer la soumission du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_profile'])) {
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
    $naissance = htmlspecialchars(trim($_POST['naissance'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $tel = htmlspecialchars(trim($_POST['tel'] ?? ''));
    $adresse = htmlspecialchars(trim($_POST['adresse'] ?? ''));

    // Basic validation
    if (empty($nom) || empty($prenom) || empty($email) || empty($tel) || empty($adresse) || empty($naissance)) {
        $errorMessage = "Tous les champs obligatoires doivent être remplis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Le format de l'adresse e-mail est invalide.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE users SET nom = ?, prenom = ?, date_de_naissance = ?, email = ?, telephone = ?, adresse = ? WHERE id = ?");
            $stmt->execute([$nom, $prenom, $naissance, $email, $tel, $adresse, $userId]);

            $successMessage = "Votre profil a été mis à jour avec succès !";
            // Recharger les données pour que les changements s'affichent immédiatement
            $stmt = $pdo->prepare("SELECT nom, prenom, date_de_naissance, email, telephone, adresse, ville, code_postal FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            // Mettre à jour les variables de session pour la navbar
            $_SESSION['user_nom'] = $userData['nom'];
            $_SESSION['user_prenom'] = $userData['prenom'];

        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du profil: " . $e->getMessage());
            $errorMessage = "Une erreur est survenue lors de la mise à jour. Veuillez réessayer.";
            // Gérer les erreurs spécifiques, ex: email déjà pris
            if ($e->getCode() == '23000') { // Code d'erreur pour les violations d'unicité
                $errorMessage = "Cette adresse e-mail est déjà utilisée.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <title>Mon profil - Shafadmedcare</title>
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
        opacity: 0.18;
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
        position: relative;
        z-index: 10;
      }
      .profil-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        padding: 30px;
      }
      .profil-info .list-group-item {
        border: none;
        padding: 10px 0;
        font-size: 1.1rem;
        color: #4a5568;
        display: flex;
        align-items: center;
      }
      .profil-info .list-group-item strong {
        width: 120px;
        flex-shrink: 0;
        color: #2563eb;
      }
      .edit-form {
        display: none;
      }
      .form-label {
        font-weight: 600;
        color: #333;
      }
      .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(37,99,235,0.25);
        border-color: #2563eb;
      }
      .alert-success-custom, .alert-danger-custom {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: none; /* Hidden by default */
      }
      .alert-success-custom {
        background-color: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
      }
      .alert-danger-custom {
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
      }
      @media (max-width: 768px) {
        .header-hero .welcome-message {
          font-size: 1.8rem;
        }
        .header-hero .subtitle {
          font-size: 1rem;
        }
        .profil-card {
          padding: 20px;
        }
        .profil-info .list-group-item {
          flex-direction: column;
          align-items: flex-start;
          padding: 10px 0;
        }
        .profil-info .list-group-item strong {
          width: auto;
          margin-bottom: 5px;
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
              <a class="nav-link" href="Mes_medecins.php">Mes Médecins</a>
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
        Mon Profil
      </div>
      <div class="subtitle">
        Gérez vos informations personnelles et vos préférences.
      </div>
    </div>

    <div class="main-content container">
      <div class="profil-card">
        <?php if (!empty($successMessage)): ?>
          <div class="alert alert-success-custom" id="successMsg" style="display: block;">
            <?php echo htmlspecialchars($successMessage); ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
          <div class="alert alert-danger-custom" id="errorMsg" style="display: block;">
            <?php echo htmlspecialchars($errorMessage); ?>
          </div>
        <?php endif; ?>

        <div id="profilDisplay">
          <h4 class="mb-4 text-primary">Mes informations</h4>
          <ul class="list-group list-group-flush profil-info" id="profilList">
            <li class="list-group-item"><strong>Nom:</strong> <span id="nomValue"><?php echo htmlspecialchars($userData['nom'] ?? ''); ?></span></li>
            <li class="list-group-item"><strong>Prénom:</strong> <span id="prenomValue"><?php echo htmlspecialchars($userData['prenom'] ?? ''); ?></span></li>
            <li class="list-group-item"><strong>Date de Naissance:</strong> <span id="naissanceValue"><?php echo htmlspecialchars($userData['date_de_naissance'] ? (new DateTime($userData['date_de_naissance']))->format('d/m/Y') : ''); ?></span></li>
            <li class="list-group-item"><strong>Email:</strong> <span id="emailValue"><?php echo htmlspecialchars($userData['email'] ?? ''); ?></span></li>
            <li class="list-group-item"><strong>Téléphone:</strong> <span id="telValue"><?php echo htmlspecialchars($userData['telephone'] ?? ''); ?></span></li>
            <li class="list-group-item"><strong>Adresse:</strong> <span id="adresseValue"><?php echo htmlspecialchars($userData['adresse'] ?? ''); ?></span></li>
            <li class="list-group-item"><strong>Ville:</strong> <span id="villeValue"><?php echo htmlspecialchars($userData['ville'] ?? ''); ?></span></li>
            <li class="list-group-item"><strong>Code Postal:</strong> <span id="codePostalValue"><?php echo htmlspecialchars($userData['code_postal'] ?? ''); ?></span></li>
          </ul>
          <button class="btn btn-primary mt-4" id="editBtn">Modifier le profil</button>
        </div>

        <form id="editForm" class="edit-form" method="POST" action="profil.php">
          <h4 class="mb-4 text-primary">Modifier mes informations</h4>
          <div class="mb-3">
            <label for="nomInput" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nomInput" name="nom" value="<?php echo htmlspecialchars($userData['nom'] ?? ''); ?>" required>
          </div>
          <div class="mb-3">
            <label for="prenomInput" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="prenomInput" name="prenom" value="<?php echo htmlspecialchars($userData['prenom'] ?? ''); ?>" required>
          </div>
          <div class="mb-3">
            <label for="naissanceInput" class="form-label">Date de Naissance</label>
            <input type="date" class="form-control" id="naissanceInput" name="naissance" value="<?php echo htmlspecialchars($userData['date_de_naissance'] ?? ''); ?>" required>
          </div>
          <div class="mb-3">
            <label for="emailInput" class="form-label">Email</label>
            <input type="email" class="form-control" id="emailInput" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
          </div>
          <div class="mb-3">
            <label for="telInput" class="form-label">Téléphone</label>
            <input type="tel" class="form-control" id="telInput" name="tel" value="<?php echo htmlspecialchars($userData['telephone'] ?? ''); ?>" required>
          </div>
          <div class="mb-3">
            <label for="adresseInput" class="form-label">Adresse</label>
            <input type="text" class="form-control" id="adresseInput" name="adresse" value="<?php echo htmlspecialchars($userData['adresse'] ?? ''); ?>" required>
          </div>
          <div class="mb-3">
            <label for="villeInput" class="form-label">Ville</label>
            <input type="text" class="form-control" id="villeInput" name="ville" value="<?php echo htmlspecialchars($userData['ville'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label for="codePostalInput" class="form-label">Code Postal</label>
            <input type="text" class="form-control" id="codePostalInput" name="codepostal" value="<?php echo htmlspecialchars($userData['code_postal'] ?? ''); ?>">
          </div>

          <button type="submit" name="submit_profile" class="btn btn-primary mt-3 me-2">Enregistrer les modifications</button>
          <button type="button" class="btn btn-secondary mt-3" id="cancelEditBtn">Annuler</button>
        </form>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const editBtn = document.getElementById('editBtn');
        const cancelEditBtn = document.getElementById('cancelEditBtn');
        const profilDisplay = document.getElementById('profilDisplay');
        const editForm = document.getElementById('editForm');
        const successMsg = document.getElementById('successMsg');
        const errorMsg = document.getElementById('errorMsg'); // Get error message div

        if (editBtn) {
            editBtn.onclick = function() {
                profilDisplay.style.display = 'none';
                editForm.style.display = 'block';
                editBtn.style.display = 'none';
                if (successMsg) successMsg.style.display = 'none'; // Hide success on edit
                if (errorMsg) errorMsg.style.display = 'none'; // Hide error on edit
            };
        }

        if (cancelEditBtn) {
            cancelEditBtn.onclick = function() {
                editForm.style.display = 'none';
                profilDisplay.style.display = 'block';
                editBtn.style.display = 'inline-block';
                if (successMsg) successMsg.style.display = 'none'; // Hide messages on cancel
                if (errorMsg) errorMsg.style.display = 'none';
            };
        }

        // Hide success/error messages after a few seconds if they were displayed by PHP
        setTimeout(() => {
          if (successMsg && successMsg.style.display === 'block') {
            successMsg.style.display = 'none';
          }
          if (errorMsg && errorMsg.style.display === 'block') {
            errorMsg.style.display = 'none';
          }
        }, 5000); // Hide after 5 seconds
      });
    </script>
  </body>
</html>