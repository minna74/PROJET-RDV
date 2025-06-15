<?php
session_start();
require_once 'db_connect.php';

// Rediriger si l'utilisateur n'est pas connecté
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
    $stmt = $pdo->query("SELECT id, nom, specialite, photo_url FROM doctors");
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
    <title>Accueil Patient - Shafadmedcare</title>
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
      .search-section {
        background-color: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin-top: -80px; /* Overlap with hero section */
        position: relative;
        z-index: 1;
      }
      .search-section h3 {
        color: #2563eb;
        margin-bottom: 25px;
        font-weight: 600;
      }
      .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(37,99,235,0.25);
        border-color: #2563eb;
      }
      .btn-outline-primary {
        color: #2563eb;
        border-color: #2563eb;
      }
      .btn-outline-primary:hover {
        background-color: #2563eb;
        color: white;
      }
      .section-title {
        color: #2563eb;
        font-weight: 600;
        margin-top: 40px;
        margin-bottom: 25px;
        text-align: center;
      }
      .service-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        padding: 25px;
        text-align: center;
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
      }
      .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      }
      .service-card .icon {
        font-size: 3rem;
        color: #2563eb;
        margin-bottom: 15px;
      }
      .service-card h5 {
        color: #333;
        font-weight: 600;
        margin-bottom: 10px;
      }
      .service-card p {
        color: #666;
        font-size: 0.95rem;
      }
      .medecin-result {
        display: flex;
        align-items: center;
        padding: 15px;
        border: 1px solid #e0e7ff;
        border-radius: 8px;
        margin-bottom: 15px;
        background-color: #f9fbff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.03);
      }
      .medecin-photo {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 15px;
        border: 2px solid #2563eb;
      }
      .medecin-info {
        flex-grow: 1;
      }
      .medecin-info h6 {
        margin: 0;
        color: #333;
        font-weight: 600;
      }
      .medecin-info small {
        color: #666;
      }
      .btn-rdv {
        background-color: #60a5fa;
        color: white;
        border-radius: 5px;
        padding: 8px 15px;
        font-size: 0.9rem;
        white-space: nowrap;
      }
      .btn-rdv:hover {
        background-color: #3b82f6;
      }
      .text-muted {
        text-align: center;
        padding: 20px;
      }
      @media (max-width: 768px) {
        .header-hero .welcome-message {
          font-size: 1.8rem;
        }
        .header-hero .subtitle {
          font-size: 1rem;
        }
        .search-section {
          margin-top: -50px;
          padding: 20px;
        }
        .service-card {
          margin-bottom: 20px;
        }
      }
    </style>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg bg-light sticky-top">
      <div class="container-fluid">
        <a class="navbar-brand" href="../acceuil_rdv/index.php">Shafadmedcare</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="proto.php">Accueil</a>
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
        Bienvenue sur Shafadmedcare !
      </div>
      <div class="subtitle">
        Votre santé, notre priorité. Trouvez et réservez vos rendez-vous médicaux facilement.
      </div>
    </div>

    <div class="container search-section">
      <h3 class="text-center">Trouver un médecin ou une spécialité</h3>
      <form id="searchForm">
        <div class="input-group mb-3">
          <input type="text" class="form-control form-control-lg" id="searchInput" placeholder="Rechercher par nom ou spécialité..." aria-label="Rechercher un médecin">
          <button class="btn btn-outline-primary btn-lg" type="submit" id="searchButton">Rechercher</button>
        </div>
      </form>
      <div id="searchResults" class="mt-4">
        </div>
    </div>

    <div class="container my-5">
      <h3 class="section-title">Nos services clés</h3>
      <div class="row row-cols-1 row-cols-md-3 g-4">
        <div class="col">
          <div class="service-card">
            <i class="bi bi-calendar-check icon"></i>
            <h5>Prendre rendez-vous</h5>
            <p>Consultez la disponibilité des médecins et réservez votre consultation en quelques clics.</p>
            <a href="Page_de_prise_de_rendez_vous.php" class="btn btn-primary mt-3">Prendre un RDV</a>
          </div>
        </div>
        <div class="col">
          <div class="service-card">
            <i class="bi bi-file-earmark-medical icon"></i>
            <h5>Mes ordonnances</h5>
            <p>Accédez et téléchargez toutes vos ordonnances en toute sécurité, à tout moment.</p>
            <a href="ordonance.php" class="btn btn-primary mt-3">Voir mes ordonnances</a>
          </div>
        </div>
        <div class="col">
          <div class="service-card">
            <i class="bi bi-box-arrow-down icon"></i>
            <h5>Mes résultats</h5>
            <p>Consultez vos résultats d'analyses et d'examens médicaux directement en ligne.</p>
            <a href="documents.php" class="btn btn-primary mt-3">Voir mes résultats</a>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Passe les données des médecins de PHP au JavaScript
      const medecins = <?php echo json_encode($medecins); ?>;

      const searchForm = document.getElementById('searchForm');
      const searchInput = document.getElementById('searchInput');
      const searchResults = document.getElementById('searchResults');

      searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const query = searchInput.value.trim().toLowerCase();
        searchResults.innerHTML = ''; // Clear previous results

        if (query.length === 0) return;

        // Filter based on the PHP-fetched doctors data
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
                <img src="${m.photo_url || 'https://via.placeholder.com/60'}" alt="${m.nom}" class="medecin-photo" />
                <div class="medecin-info">
                  <h6>Dr. ${m.nom}</h6>
                  <small>${m.specialite}</small>
                </div>
                <a href="Page_de_prise_de_rendez_vous.php?doctor_id=${m.id}" class="btn btn-rdv">Prendre rendez-vous</a>
              </div>
            `;
          });
        }
      });
    </script>
  </body>
</html>