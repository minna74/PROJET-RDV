
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Shafadmedcare - Accueil</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <style>
    :root {
      --main-bg: #f7fafd;
      --main-blue: #1976d2;
      --main-blue-light: #e3f0fc;
      --main-grey: #6c757d;
      --main-dark: #222;
      --main-accent: #43b0f1;
      --footer-bg: #0a2342;
    }
    body {
      background: var(--main-bg);
      color: var(--main-dark);
      font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
    }
    .logo img {
      height: 60px;
    }
    .header-right {
      display: flex;
      align-items: center;
      gap: 1rem;
      flex: 1;
      justify-content: flex-end;
    }
    .header-right img {
      width: 100%;
      max-width: 400px;
      height: auto;
      border-radius: 15px;
    }
    .navbar {
      background: #fff;
      box-shadow: 0 2px 8px rgba(60,80,120,0.06);
    }
    .navbar-brand {
      font-weight: bold;
      color: var(--main-blue) !important;
      letter-spacing: 1px;
    }
    .nav-link {
      color: var(--main-grey) !important;
      font-weight: 500;
      transition: color 0.2s;
    }
    .nav-link.active, .nav-link:hover {
      color: var(--main-blue) !important;
    }
    .btn-main {
      background: var(--main-blue);
      color: #fff;
      border-radius: 25px;
      padding: 10px 32px;
      font-weight: 500;
      transition: background 0.2s;
      border: none;
    }
    .btn-main:hover {
      background: var(--main-accent);
      color: var(--main-dark);
    }
    .btn-medecin {
      background: var(--main-accent);
      color: #fff;
      border-radius: 25px;
      padding: 10px 32px;
      font-weight: 500;
      border: none;
      margin-left: 10px;
      transition: background 0.2s;
    }
    .btn-medecin:hover {
      background: var(--main-blue);
      color: #fff;
    }
    /* Hero Section */
    .hero {
      background: linear-gradient(120deg, var(--main-blue-light) 60%, #fff 100%);
      color: var(--main-dark);
      padding: 90px 0 70px 0;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .hero h1 {
      font-size: 2.7rem;
      font-weight: bold;
      letter-spacing: 1px;
      animation: fadeInDown 1s;
    }
    .hero p {
      font-size: 1.2rem;
      margin-bottom: 32px;
      animation: fadeInUp 1.2s;
    }
    .hero .btn-main {
      margin-right: 12px;
      animation: fadeInUp 1.3s;
    }
    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-30px);}
      to { opacity: 1; transform: translateY(0);}
    }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px);}
      to { opacity: 1; transform: translateY(0);}
    }
    /* Section Titles */
    .section-title {
      margin-top: 60px;
      margin-bottom: 30px;
      font-weight: bold;
      color: var(--main-blue);
      letter-spacing: 1px;
    }
    /* Services */
    .service-card {
      background: #fff;
      border: none;
      border-radius: 18px;
      box-shadow: 0 2px 16px rgba(60,80,120,0.07);
      padding: 32px 20px;
      transition: transform 0.18s, box-shadow 0.18s;
      text-align: center;
      margin-bottom: 24px;
      min-height: 180px;
      /* min-height réduit car plus d'icône */
    }
    .service-card:hover {
      transform: translateY(-8px) scale(1.03);
      box-shadow: 0 8px 32px rgba(60,80,120,0.13);
      background: var(--main-blue-light);
    }
    /* Blog */
    .blog-card {
      border: none;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 2px 16px rgba(60,80,120,0.07);
      transition: transform 0.18s, box-shadow 0.18s;
      background: #fff;
      min-height: 220px;
    }
    .blog-card:hover {
      transform: translateY(-6px) scale(1.02);
      box-shadow: 0 8px 32px rgba(60,80,120,0.13);
    }
    /* Footer */
    .footer {
      background: var(--footer-bg);
      color: #e0e0e0;
      padding: 50px 0 20px 0;
      margin-top: 60px;
    }
    .footer .footer-title {
      color: var(--main-accent);
      font-weight: bold;
      margin-bottom: 18px;
      letter-spacing: 1px;
    }
    .footer a {
      color: #e0e0e0;
      text-decoration: none;
      transition: color 0.2s;
    }
    .footer a:hover {
      color: var(--main-accent);
    }
    .footer .social-icons a {
      font-size: 1.5rem;
      margin-right: 16px;
      color: var(--main-accent);
      transition: color 0.2s;
    }
    .footer .social-icons a:hover {
      color: #fff;
    }
    .newsletter-input {
      border-radius: 20px 0 0 20px;
      border: none;
      padding: 8px 16px;
      width: 70%;
      max-width: 220px;
    }
    .newsletter-btn {
      border-radius: 0 20px 20px 0;
      border: none;
      background: var(--main-accent);
      color: #222;
      padding: 8px 18px;
      font-weight: 500;
      transition: background 0.2s;
    }
    .newsletter-btn:hover {
      background: var(--main-blue);
      color: #fff;
    }
    @media (max-width: 991px) {
      .hero h1 { font-size: 2rem; }
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="logo">
    <img src="logo.jpg" alt="Logo Cabinet" class="logo img-fluid">
    </div>
    <div class="header-right">
    <div class="container">
      <a class="navbar-brand" href="#">Shafadmedcare</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" href="#accueil">Accueil</a></li>
          <li class="nav-item"><a class="nav-link" href="#apropos">À propos</a></li>
          <li class="nav-item"><a class="nav-link" href="#services">Nos services</a></li>
          <li class="nav-item"><a class="nav-link" href="#blog">Blog</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
          <li class="nav-item ms-2">
            <a class="btn btn-main" href="../espace_patient/inscription.php"><i class="bi bi-person"></i> Connexion</a>
          </li>
          <li class="nav-item ms-2">
            <a class="btn btn-medecin" href="../projet_rdv/inscription_medecin.php"><i class="bi bi-briefcase"></i> Vous êtes médecin ?</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero" id="accueil">
    <div class="container">
      <h1>Votre santé, notre priorité</h1>
      <p class="mt-3 mb-4">Prenez rendez-vous, accédez à vos documents, échangez avec vos médecins et découvrez nos services innovants.</p>
      <a href="#services" class="btn btn-main me-2">Découvrir nos services</a>
      <a href="#contact" class="btn btn-outline-secondary btn-lg">Contactez-nous</a>
    </div>
  </section>

  <!-- À propos -->
  <section id="apropos" class="container py-5">
    <h2 class="section-title text-center mb-4">À propos de Shafadmedcare</h2>
    <div class="row align-items-center justify-content-center">
      <div class="col-lg-6 mb-4 mb-lg-0">
        <div class="bg-white rounded-4 shadow p-4 h-100 animate__animated animate__fadeInLeft">
          <h4 class="mb-3 text-primary"><i class="bi bi-heart-pulse-fill me-2"></i>Notre mission</h4>
          <p>
            <strong>Shafadmedcare</strong> est une plateforme médicale innovante qui rapproche patients et professionnels de santé grâce à des outils numériques simples, sécurisés et performants.
          </p>
          <ul class="list-unstyled mt-3">
            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Prise de rendez-vous en ligne 24h/24</li>
            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Accès sécurisé à vos documents médicaux</li>
            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Messagerie directe avec vos praticiens</li>
            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Conseils et actualités santé</li>
          </ul>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="bg-gradient rounded-4 shadow p-4 h-100 d-flex flex-column justify-content-center animate__animated animate__fadeInRight" style="background: linear-gradient(120deg, #e3f0fc 60%, #fff 100%);">
          <h4 class="mb-3 text-primary"><i class="bi bi-stars me-2"></i>Pourquoi choisir Shafadmedcare ?</h4>
          <ul class="list-unstyled">
            <li class="mb-2"><i class="bi bi-arrow-right-circle text-accent me-2"></i>Interface intuitive et moderne</li>
            <li class="mb-2"><i class="bi bi-arrow-right-circle text-accent me-2"></i>Respect de la confidentialité et des données</li>
            <li class="mb-2"><i class="bi bi-arrow-right-circle text-accent me-2"></i>Support réactif et à l’écoute</li>
            <li class="mb-2"><i class="bi bi-arrow-right-circle text-accent me-2"></i>Accessible sur tous vos appareils</li>
          </ul>
          <div class="mt-4">
            <a href="#services" class="btn btn-main me-2">Découvrir nos services</a>
            <a href="#contact" class="btn btn-outline-secondary">Nous contacter</a>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- Nos services -->
  <section id="services" class="container">
    <h2 class="section-title text-center">Nos services</h2>
    <div class="row">
      <div class="col-md-4">
        <div class="service-card">
          <h5>Prise de rendez-vous</h5>
          <p>Réservez vos consultations en quelques clics avec le praticien de votre choix.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="service-card">
          <h5>Gestion des documents</h5>
          <p>Accédez à vos ordonnances, résultats d’analyses et comptes-rendus médicaux en toute sécurité.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="service-card">
          <h5>Messagerie sécurisée</h5>
          <p>Échangez facilement avec vos médecins et recevez des notifications importantes.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="service-card">
          <h5>Espace Médecins</h5>
          <p>Un espace dédié aux professionnels pour gérer leurs rendez-vous et dossiers patients.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="service-card">
          <h5>Conseils santé</h5>
          <p>Retrouvez des articles, conseils et actualités pour prendre soin de votre santé au quotidien.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="service-card">
          <h5>Support & Assistance</h5>
          <p>Notre équipe est à votre écoute pour toute question ou besoin d’accompagnement.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Blog -->
  <section id="blog" class="container">
    <h2 class="section-title text-center">Blog & Actualités</h2>
    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="card blog-card">
          <div class="card-body">
            <h5 class="card-title">Bien préparer sa consultation</h5>
            <p class="card-text">Découvrez nos conseils pour optimiser vos rendez-vous médicaux et poser les bonnes questions à votre praticien.</p>
            <a href="#" class="btn btn-outline-primary btn-sm">Lire plus</a>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card blog-card">
          <div class="card-body">
            <h5 class="card-title">Les bienfaits du suivi médical régulier</h5>
            <p class="card-text">Pourquoi consulter régulièrement son médecin est essentiel pour prévenir et détecter les maladies à temps.</p>
            <a href="#" class="btn btn-outline-primary btn-sm">Lire plus</a>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card blog-card">
          <div class="card-body">
            <h5 class="card-title">Santé & numérique : les nouveaux outils</h5>
            <p class="card-text">Découvrez comment les technologies transforment la prise en charge médicale et le suivi des patients.</p>
            <a href="#" class="btn btn-outline-primary btn-sm">Lire plus</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact -->
  <section id="contact" class="container">
    <h2 class="section-title text-center">Contactez-nous</h2>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <form>
          <div class="row">
            <div class="col-md-6 mb-3">
              <input type="text" class="form-control" placeholder="Votre nom" required>
            </div>
            <div class="col-md-6 mb-3">
              <input type="email" class="form-control" placeholder="Votre email" required>
            </div>
          </div>
          <div class="mb-3">
            <input type="text" class="form-control" placeholder="Sujet" required>
          </div>
          <div class="mb-3">
            <textarea class="form-control" rows="5" placeholder="Votre message" required></textarea>
          </div>
          <button type="submit" class="btn btn-main">Envoyer</button>
        </form>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer mt-5">
    <div class="container">
      <div class="row mb-4">
        <div class="col-md-4 mb-3">
          <div class="footer-title">Shafadmedcare</div>
          <p>Plateforme médicale innovante pour patients et professionnels. Votre santé, notre priorité.</p>
        </div>
        <div class="col-md-2 mb-3">
          <div class="footer-title">Liens rapides</div>
          <ul class="list-unstyled">
            <li><a href="#accueil">Accueil</a></li>
            <li><a href="#apropos">À propos</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#blog">Blog</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="page de login.php">Connexion</a></li>
            <li><a href="connexion_medecin.php">Vous êtes médecin ?</a></li>
          </ul>
        </div>
        <div class="col-md-3 mb-3">
          <div class="footer-title">Contact</div>
          <ul class="list-unstyled">
            <li><i class="bi bi-envelope"></i> contact@shafadmedcare.com</li>
            <li><i class="bi bi-telephone"></i> +33 1 23 45 67 89</li>
            <li><i class="bi bi-geo-alt"></i> 123, Avenue de la Santé, Paris</li>
          </ul>
        </div>
        <div class="col-md-3 mb-3">
          <div class="footer-title">Newsletter</div>
          <form class="d-flex mb-2">
            <input type="email" class="newsletter-input" placeholder="Votre email" required>
            <button type="submit" class="newsletter-btn">S'abonner</button>
          </form>
          <div class="social-icons mt-2">
            <a href="#"><i class="bi bi-facebook"></i></a>
            <a href="#"><i class="bi bi-twitter"></i></a>
            <a href="#"><i class="bi bi-linkedin"></i></a>
            <a href="#"><i class="bi bi-envelope"></i></a>
          </div>
        </div>
      </div>
      <div class="text-center small mt-3">
        &copy; 2025 Shafadmedcare. Tous droits réservés.
      </div>
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Animation de scroll pour la navbar
    document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', function(e) {
        if(this.hash) {
          e.preventDefault();
          document.querySelector(this.hash).scrollIntoView({behavior: 'smooth'});
        }
      });
      });
  </script>
</body>
</html>