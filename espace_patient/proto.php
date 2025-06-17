<?php
session_start();
require_once 'db_connect.php';

 //Rediriger si l'utilisateur n'est pas connecté
 if (!isset($_SESSION['user_id'])) {
    header('Location: page_de_login.php');
    exit();
}

// Récupérer les informations de l'utilisateur connecté
$userName = $_SESSION['user_nom'] ?? 'Utilisateur';
$userPrenom = $_SESSION['user_prenom'] ?? '';

// Récupérer les médecins depuis la base de données pour la recherche
$medecins = [];
try {
    $stmt = $pdo->query("SELECT id, nom, specialite FROM doctors");
    $medecins = $stmt->fetchAll();
} catch (PDOException $e) {
    // Log l'erreur mais ne pas l'afficher à l'utilisateur
    error_log("Erreur lors de la récupération des médecins: " . $e->getMessage());
    // Vous pouvez laisser $medecins vide ou afficher un message d'erreur côté client si vous voulez
}

?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <title>Accueil Patient</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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
      .navbar .nav-link, .navbar .dropdown-toggle {
        color: #1a237e !important;
        font-weight: 500;
        font-size: 1.07rem;
        margin-left: 10px;
        margin-right: 10px;
        transition: color 0.2s;
      }
      .navbar .nav-link.active, .navbar .nav-link:focus, .navbar .nav-link:hover {
        color: #2563eb !important;
        text-decoration: underline;
        text-underline-offset: 3px;
      }
      .navbar .dropdown-menu {
        border-radius: 12px;
        font-size: 1rem;
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
      .search-bar-modern .input-group {
        background: #fff;
        border-radius: 50px;
        box-shadow: 0 2px 8px 0 rgba(31,38,135,0.07);
      }
      .search-bar-modern input:focus {
        box-shadow: none;
        background: #f6f8fb;
      }
      .search-bar-modern .input-group-text {
        border: none;
        background: #fff;
      }
      .main-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 36px 4vw 0 4vw;
      }
      .rubrique-card {
        display: flex;
        align-items: center;
        background: #fff;
        border-radius: 22px;
        box-shadow: 0 2px 16px 0 rgba(31,38,135,0.06);
        padding: 32px 32px 32px 24px;
        margin-bottom: 32px;
        gap: 32px;
        border-left: 6px solid #2563eb;
        transition: box-shadow 0.2s, border-color 0.2s;
      }
      .rubrique-card:hover {
        box-shadow: 0 6px 32px 0 rgba(31,38,135,0.10);
        border-left-color: #43cea2;
      }
      .rubrique-card .rubrique-img {
        width: 90px;
        height: 90px;
        border-radius: 18px;
        object-fit: cover;
        box-shadow: 0 2px 12px 0 rgba(31,38,135,0.10);
        background: #f2f6fa;
      }
      .rubrique-card .rubrique-content {
        flex: 1;
      }
      .rubrique-card .rubrique-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a237e;
        margin-bottom: 8px;
      }
      .rubrique-card .rubrique-text {
        font-size: 1.07rem;
        color: #3a3a3a;
        margin-bottom: 0;
        line-height: 1.6;
      }
      .rubrique-card.rubrique-conseil { border-left-color: #43cea2; }
      .rubrique-card.rubrique-actu { border-left-color: #2563eb; }
      .actions-title {
        color: #1a237e;
        font-weight: 600;
        font-size: 1.2rem;
        margin: 40px 0 18px 0;
        letter-spacing: 0.5px;
      }
      .actions-row {
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
        margin: 0 0 36px 0;
      }
      .tile-action {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 2px 8px 0 rgba(31, 38, 135, 0.04);
        min-width: 220px;
        flex: 1 1 220px;
        max-width: 320px;
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 18px;
        text-decoration: none !important;
        transition: background 0.18s, box-shadow 0.18s, transform 0.15s;
        color: inherit;
      }
      .tile-action:hover {
        background: #e3eafc;
        box-shadow: 0 4px 16px rgba(25, 99, 235, 0.08);
        transform: translateY(-2px) scale(1.01);
      }
      .tile-action .bi {
        font-size: 1.7rem;
        color: #2563eb;
        margin-bottom: 0;
        flex-shrink: 0;
      }
      .tile-action h5 {
        font-size: 1.07rem;
        color: #1a237e;
        font-weight: 500;
        margin-bottom: 2px;
      }
      .tile-action p {
        color: #444;
        font-size: 0.98rem;
        margin-bottom: 0;
        font-weight: 400;
      }
      .medecin-result {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 8px 0 rgba(31, 38, 135, 0.04);
        padding: 18px 18px;
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 14px;
      }
      .medecin-photo {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        background: #e3eafc;
        border: none;
      }
      .medecin-info h6 {
        margin: 0;
        font-size: 1.08rem;
        font-weight: 600;
        color: #1976d2;
      }
      .medecin-info small {
        color: #555;
        font-size: 0.97rem;
      }
      .btn-rdv {
        margin-left: auto;
        background: #2563eb;
        color: #fff;
        border-radius: 20px;
        padding: 8px 22px;
        font-weight: 500;
        border: none;
        transition: background 0.2s;
      }
      .btn-rdv:hover {
        background: #1a237e;
        color: #fff;
      }
      footer {
        background: #fff;
        color: #1a237e;
        text-align: center;
        padding: 28px 0 10px 0;
        margin-top: 48px;
        font-size: 1rem;
        border-top: 1px solid #e3eafc;
        letter-spacing: 0.5px;
      }
      .footer-links a {
        color: #2563eb;
        margin: 0 10px;
        text-decoration: none;
        font-weight: 500;
      }
      .footer-links a:hover {
        text-decoration: underline;
      }
      @media (max-width: 991px) {
        .main-content { padding: 24px 2vw 0 2vw; }
        .rubrique-card { padding: 18px 10px 18px 10px; gap: 16px; }
        .rubrique-card .rubrique-img { width: 60px; height: 60px; }
      }
      @media (max-width: 600px) {
        .rubrique-card { flex-direction: column; align-items: flex-start; padding: 12px 4vw; }
        .rubrique-card .rubrique-img { margin-bottom: 10px; }
        .header-hero { padding: 32px 0 18px 0; }
      }
    </style>
  </head>
  <body class="min-vh-100 d-flex flex-column">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
      <div class="container">
        <a class="navbar-brand fw-bold" href="../acceuil_rdv/accueilprin.php">Shafadmedcare</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
          <ul class="navbar-nav align-items-center">
            <li class="nav-item">
              <a class="nav-link active" href="#">Accueil</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="rendez_vous.php">Mes rendez-vous</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="ordonance.php">Mes ordonnances</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="messagerie.php">Messagerie</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="document.php">Mes résultats</a>
            </li>
            <li class="nav-item dropdown ms-3">
              <a class="nav-link dropdown-toggle fw-semibold" href="#" id="patientDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
    <!-- Hero Header -->
    <div class="header-hero">
      <div class="welcome-message">
        Bonjour Mr.  <?php echo htmlspecialchars($userPrenom . ' ' . $userName); ?>, bienvenue sur votre espace patient.
      </div>
      <div class="subtitle">
        Retrouvez vos informations médicales, vos rendez-vous et bien plus encore.
      </div>
      <form id="searchForm" class="mx-auto mb-0 search-bar-modern" style="max-width:520px;">
        <div class="input-group shadow-sm rounded-pill">
          <span class="input-group-text bg-white border-0 rounded-start-pill ps-3">
            <i class="bi bi-search text-primary"></i>
          </span>
          <input class="form-control border-0 rounded-end-pill py-2" type="search" id="searchInput" placeholder="Rechercher une spécialité ou un médecin..." aria-label="Rechercher" style="background:#fff;">
        </div>
      </form>
      <div id="searchResults" class="search-results mt-4"></div>
    </div>
    <div class="main-content">
      <!-- Rubriques modernes, épurées et illustrées -->
      <div class="rubrique-card rubrique-conseil mb-4">
        <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=facearea&w=400&h=400&facepad=2&q=80" alt="Hydratation" class="rubrique-img">
        <div class="rubrique-content">
          <div class="rubrique-title">Conseil du jour</div>
          <div class="rubrique-text">
            Buvez de l'eau régulièrement et prenez un moment pour respirer profondément.<br>
            <span style="color:#43cea2;font-weight:500;">Votre bien-être mental compte autant que votre santé physique.</span>
          </div>
        </div>
      </div>
      <div class="rubrique-card rubrique-actu mb-4">
        <img src="https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=facearea&w=400&h=400&facepad=2&q=80" alt="Vaccination" class="rubrique-img">
        <div class="rubrique-content">
          <div class="rubrique-title">Actualité santé</div>
          <div class="rubrique-text">
            <strong>Vaccination :</strong> La campagne contre la grippe saisonnière est ouverte.<br>
            <span style="color:#2563eb;font-weight:500;">Discutez-en avec votre médecin lors de votre prochain rendez-vous.</span>
          </div>
        </div>
      </div>
      <!-- Actions rapides -->
      <div class="actions-title mb-3 mt-5">Actions rapides</div>
      <div class="actions-row mb-5">
        <a href="rendez_vous.php" class="tile-action">
          <i class="bi bi-calendar2-week"></i>
          <div>
            <h5>Mes Rendez-vous</h5>
            <p>Voir mes rendez-vous passés et à venir</p>
          </div>
        </a>
        <a href="ordonance.php" class="tile-action">
          <i class="bi bi-file-earmark-medical"></i>
          <div>
            <h5>Mes Ordonnances</h5>
            <p>Consulter et télécharger mes ordonnances</p>
          </div>
        </a>
        <a href="Page_de_prise_de_rendez_vous.php" class="tile-action">
          <i class="bi bi-plus-circle"></i>
          <div>
            <h5>Prendre un rendez-vous</h5>
            <p>Planifier un nouveau rendez-vous</p>
          </div>
        </a>
        <a href="modifier_rdv.php" class="tile-action">
          <i class="bi bi-file-earmark-medical"></i>
          <div>
            <h5>Modifier un rendez-vous</h5>
            <p>Consulter et modifier vos rendez-vous</p>
          </div>
        </a>
        <a href="Mes_medecins.php" class="tile-action">
          <i class="bi bi-person-vcard"></i>
          <div>
            <h5>Médecins consultés</h5>
            <p>Liste des professionnels rencontrés</p>
          </div>
        </a>
        <a href="document.php" class="tile-action">
          <i class="bi bi-clipboard2-pulse"></i>
          <div>
            <h5>Résultats d'examens</h5>
            <p>Consulter les résultats médicaux</p>
          </div>
        </a>
        <a href="messagerie.php" class="tile-action">
          <i class="bi bi-bell"></i>
          <div>
            <h5>Messagerie</h5>
            <p>Voir les rappels et messages importants</p>
          </div>
        </a>
      </div>
      <!-- Recherche médecin (résultats déjà inclus dans le header) -->
    </div>
    <footer>
      <div class="footer-links mb-2">
        <a href="#">Accueil</a>|
        <a href="rendez_vous.php">Rendez-vous</a>|
        <a href="#">Contact</a>|
        <a href="#">Mentions légales</a>
      </div>
      <div>© 2025 Shafadmedcare – Tous droits réservés</div>
      <div style="font-size:0.95em;">123 Avenue de la Santé, Oujda, Maroc | contact@shafadmedcare.ma</div>
    </footer>
    <script>
      // Liste statique des médecins (remplace la partie PHP)
      const medecins = <?php echo json_encode($medecins); ?>;

      const searchForm = document.getElementById('searchForm');
      const searchInput = document.getElementById('searchInput');
      const searchResults = document.getElementById('searchResults');
      searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const query = searchInput.value.trim().toLowerCase();
        searchResults.innerHTML = '';
        if (query.length === 0) return;
        const found = medecins.filter(m =>
          m.nom.toLowerCase().includes(query) ||
          m.specialite.toLowerCase().includes(query)
        );
        if (found.length === 0) {
          searchResults.innerHTML = '<div class="text-muted">Aucun médecin ou spécialité trouvée.</div>';
        } else {
          found.forEach(m => {
            searchResults.innerHTML += `
              <div class="medecin-result">
                <img src="${m.photo}" alt="${m.nom}" class="medecin-photo" />
                <div class="medecin-info">
                  <h6>${m.nom}</h6>
                  <small>${m.specialite}</small>
                </div>
                <a href="Page_de_prise_de__rendez_vous.php" class="btn btn-rdv">Prendre rendez-vous</a>
              </div>
            `;
          });
        }
      });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>