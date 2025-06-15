<?php
session_start();
if (isset($_SESSION['nom'])) {
    $nom = htmlspecialchars($_SESSION['nom']);
    $role = htmlspecialchars($_SESSION['role']);
    echo "<div style='text-align:center;background-color:#dff0d8;padding:10px;'>Bienvenue $nom ($role) | <a href='profile.php'>Profil</a> | <a href='logout.php'>Déconnexion</a></div>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Plateforme Médicale</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #fff;
      color: #333;
    }
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
      background-color: #f0f0f0;
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
    .login-btn {
      padding: 10px 15px;
      background-color: #4be0c2;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .search-section {
      text-align: center;
      padding: 1rem;
    }
    .search-section input[type="text"] {
      width: 300px;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .search-section button {
      padding: 10px 20px;
      background-color: #4be0c2;
      border: none;
      border-radius: 5px;
      margin-left: 10px;
    }
    nav {
      display: flex;
      justify-content: center;
      gap: 2rem;
      padding: 1rem;
    }
    .features {
      text-align: center;
      padding: 2rem 0;
    }
    .features-icons {
      display: flex;
      justify-content: center;
      gap: 4rem;
      padding: 1rem 0;
    }
    .section-info {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 2rem;
      padding: 2rem;
    } 
    .photo{ 
      border-radius: 15px;
      
 }
      
    .info-box {
      background-color: #4be0c2;
      padding: 1.5rem;
      border-radius: 15px;
      color: black;
    }
    .info-box ul {
      list-style: none;
      padding: 0;
    }
    .info-box li::before {
      content: "\2714 ";
      color: green;
      margin-right: 8px;
    }
    footer {
      background-color: #f0f0f0;
      padding: 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }
    .footer-left {
      max-width: 300px;
    }
    .footer-right {
      text-align: right;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">
    <img src="logo.jpg" alt="Logo Cabinet" class="logo img-fluid">
    </div>
    <div class="header-right">
  
      <a href="login.php"><button class="login-btn">Connexion</button></a>
    </div>
  </header>

  <div class="search-section">
    <input type="text" placeholder="Par ex. spécialité, médecin...">
    <button>Rechercher</button>
    <p>Prenez rendez-vous en quelques clics</p>
  </div>

  <nav>
    <a href="#">Accueil</a>

    <a href="../espace_patient/inscription.php">Espace patient</a>
    <a href="../projet_rdv/inscription_medecin.php">Médecins</a>
    <a href="#">Aide</a>
  </nav>

  <div class="features">
    <h2>Votre partenaire santé au quotidien</h2>
    <div class="features-icons">
      <div>📅 <br> prendre rendez-vous</div>
      <div>💊 <br> ordonnance</div>
      <div>📄 <br> Suivez vos consultations</div>
    </div>
  </div>

  <div class="section-info">
    <img src="photo1.jpg" class="photo" alt="Illustration Médecin" width="200" height="200">
    <div class="info-box">
      <ul>
        <li>Un service de prise de rendez-vous disponible.</li>
        <li>Possibilité de prendre rendez-vous en quelques clics.</li>
        <li>Gain de temps et d’autonomie.</li>
        <li>Minimisation du temps d’attente et des rendez-vous non honorés.</li>
        <li>Être en accord avec vos praticiens.</li>
      </ul>
    </div>
  </div>

  <footer>
    <div class="footer-left">
      <div class="logo"><img src="logo.jpg" alt="Logo Cabinet" class="logo img-fluid"></div>
      <p>Plateforme web dédiée aux rendez-vous médicaux en ligne. Département IT - 2025</p>
    </div>
    <div class="footer-right">
      <p>Contactez-nous :</p>
      <p>Centre d’aide<br>Contact rapide</p>
    </div>
  </footer>
</body>
</html>
