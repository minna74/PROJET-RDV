
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'db_connect.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
    $dob = htmlspecialchars(trim($_POST['dob'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $telephone = htmlspecialchars(trim($_POST['telephone'] ?? ''));
    $motdepasse = $_POST['motdepasse'] ?? '';
    $confirmpassword = $_POST['confirmpassword'] ?? '';

    // Validation des données
    if (empty($nom)) $errors['nom'] = "Le nom est requis.";
    if (empty($prenom)) $errors['prenom'] = "Le prénom est requis.";
    if (empty($dob)) $errors['dob'] = "La date de naissance est requise.";
    if (empty($email)) $errors['email'] = "L'email est requis.";
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Format d'email invalide.";
    if (empty($telephone)) $errors['telephone'] = "Le téléphone est requis.";
    if (empty($motdepasse)) $errors['motdepasse'] = "Le mot de passe est requis.";
    if (strlen($motdepasse) < 6) $errors['motdepasse'] = "Le mot de passe doit contenir au moins 6 caractères.";
    if ($motdepasse !== $confirmpassword) $errors['confirmpassword'] = "Les mots de passe ne correspondent pas.";

    if (empty($errors)) {
        // Hash du mot de passe
        $motdepasseHashed = password_hash($motdepasse, PASSWORD_DEFAULT);

        try {
            // Insertion dans la base de données
            $stmt = $pdo->prepare("INSERT INTO patient (Nom_patient, Prenom_patient, Date_naiss, email_patient, Numtel, Mot_de_passep) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $dob, $email, $telephone, $motdepasseHashed]);

           // Récupérer l'ID du patient nouvellement inscrit
$id_patient = $pdo->lastInsertId();

// Définir les variables de session
$_SESSION['user_id'] = $id_patient;
$_SESSION['user_nom'] = $nom;
$_SESSION['user_prenom'] = $prenom;

header("Location: document.php");
exit();
        } catch (PDOException $e) {
            error_log("Erreur d'inscription: " . $e->getMessage());
            if ($e->getCode() == '23000') {
                $errors['db'] = "Cette adresse e-mail est déjà utilisée. Veuillez en choisir une autre.";
            } else {
                $errors['db'] = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription Patient - Shafadmedcare</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    body {
      background: linear-gradient(135deg, #61d4c3, #4CAF9C);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }
    
    .container {
      display: flex;
      width: 100%;
      max-width: 1000px;
      background: white;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
    }
    
    .left-panel {
      flex: 1;
      background: linear-gradient(to right bottom, #4CAF9C, #2E7D32);
      color: white;
      padding: 50px 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      text-align: center;
      position: relative;
    }
    
    .logo {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 30px;
    }
    
    .logo-icon {
      font-size: 42px;
      margin-right: 15px;
      color: white;
    }
    
    .logo-text {
      font-size: 28px;
      font-weight: 700;
      letter-spacing: 1px;
    }
    
    .left-panel h1 {
      font-size: 2.5rem;
      margin-bottom: 20px;
      font-weight: 700;
    }
    
    .left-panel p {
      font-size: 1.1rem;
      line-height: 1.6;
      margin-bottom: 30px;
      max-width: 400px;
      margin: 0 auto 30px;
    }
    
    .features {
      text-align: left;
      max-width: 350px;
      margin: 0 auto;
    }
    
    .feature {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }
    
    .feature i {
      font-size: 24px;
      margin-right: 15px;
      color: #fff;
      background: rgba(255, 255, 255, 0.2);
      width: 45px;
      height: 45px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .feature div {
      font-size: 0.95rem;
    }
    
    .right-panel {
      flex: 1;
      padding: 60px 50px;
      background: white;
    }
    
    .form-header {
      text-align: center;
      margin-bottom: 40px;
    }
    
    .form-header h2 {
      color: #2C3E50;
      font-size: 2.2rem;
      margin-bottom: 10px;
      font-weight: 700;
    }
    
    .form-header p {
      color: #7f8c8d;
      font-size: 1.1rem;
    }
    
    .form-container {
      margin-top: 20px;
    }
    
    .form-group {
      margin-bottom: 25px;
      position: relative;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: #34495E;
      font-weight: 500;
      font-size: 1.05rem;
    }
    
    .form-group input {
      width: 100%;
      padding: 15px;
      border: 2px solid #e1e5eb;
      border-radius: 10px;
      font-size: 1rem;
      outline: none;
      transition: all 0.3s ease;
    }
    
    .form-group input:focus {
      border-color: #4CAF9C;
      box-shadow: 0 0 0 3px rgba(76, 175, 156, 0.2);
    }
    
    .error-message {
      color: #e74c3c;
      font-size: 0.9rem;
      margin-top: 6px;
      display: block;
      font-weight: 500;
    }
    
    .password-container {
      position: relative;
    }
    
    .toggle-password {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #7f8c8d;
    }
    
    .submit-btn {
      width: 100%;
      padding: 16px;
      background: linear-gradient(to right, #4CAF9C, #2E7D32);
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(76, 175, 156, 0.3);
      margin-top: 10px;
    }
    
    .submit-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(76, 175, 156, 0.4);
    }
    
    .submit-btn:active {
      transform: translateY(-1px);
    }
    
    .login-link {
      text-align: center;
      margin-top: 25px;
      color: #7f8c8d;
      font-size: 1rem;
    }
    
    .login-link a {
      color: #4CAF9C;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s;
    }
    
    .login-link a:hover {
      text-decoration: underline;
    }
    
    .form-row {
      display: flex;
      gap: 20px;
      margin-bottom: 25px;
    }
    
    .form-row .form-group {
      flex: 1;
      margin-bottom: 0;
    }
    
    .db-error {
      color: #e74c3c;
      text-align: center;
      font-weight: 600;
      padding: 15px;
      background: #fdeded;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    
    @media (max-width: 900px) {
      .container {
        flex-direction: column;
      }
      
      .left-panel {
        padding: 40px 30px;
      }
      
      .right-panel {
        padding: 40px 30px;
      }
      
      .form-row {
        flex-direction: column;
        gap: 25px;
      }
    }
    
    @media (max-width: 480px) {
      .left-panel {
        padding: 30px 20px;
      }
      
      .right-panel {
        padding: 30px 20px;
      }
      
      .form-header h2 {
        font-size: 1.8rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="left-panel">
      <div class="logo">
        <i class="fas fa-heartbeat logo-icon"></i>
        <div class="logo-text">ShafadMedCare</div>
      </div>
      <h1>Inscription Patient</h1>
      <p>Rejoignez notre plateforme pour une gestion simplifiée de votre santé.</p>
      
      <div class="features">
        <div class="feature">
          <i class="fas fa-calendar-check"></i>
          <div>Prenez rendez-vous en ligne avec les meilleurs spécialistes</div>
        </div>
        <div class="feature">
          <i class="fas fa-file-medical"></i>
          <div>Accédez à votre dossier médical à tout moment</div>
        </div>
        <div class="feature">
          <i class="fas fa-bell"></i>
          <div>Recevez des rappels pour vos rendez-vous et traitements</div>
        </div>
      </div>
    </div>
    
    <div class="right-panel">
      <div class="form-header">
        <h2>Créer un compte</h2>
        <p>Entrez vos informations personnelles pour vous inscrire</p>
      </div>
      
      <?php if (isset($errors['db'])): ?>
        <div class="db-error"><?php echo htmlspecialchars($errors['db']); ?></div>
      <?php endif; ?>
      
      <form class="form-container" method="POST" action="inscription.php">
        <div class="form-row">
          <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>" required>
            <?php if (isset($errors['nom'])): ?>
              <span class="error-message"><?php echo htmlspecialchars($errors['nom']); ?></span>
            <?php endif; ?>
          </div>
          
          <div class="form-group">
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>" required>
            <?php if (isset($errors['prenom'])): ?>
              <span class="error-message"><?php echo htmlspecialchars($errors['prenom']); ?></span>
            <?php endif; ?>
          </div>
        </div>
        
        <div class="form-group">
          <label for="dob">Date de naissance</label>
          <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>" required>
          <?php if (isset($errors['dob'])): ?>
            <span class="error-message"><?php echo htmlspecialchars($errors['dob']); ?></span>
          <?php endif; ?>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            <?php if (isset($errors['email'])): ?>
              <span class="error-message"><?php echo htmlspecialchars($errors['email']); ?></span>
            <?php endif; ?>
          </div>
          
          <div class="form-group">
            <label for="telephone">Téléphone</label>
            <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($_POST['telephone'] ?? ''); ?>" required>
            <?php if (isset($errors['telephone'])): ?>
              <span class="error-message"><?php echo htmlspecialchars($errors['telephone']); ?></span>
            <?php endif; ?>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="motdepasse">Mot de passe</label>
            <div class="password-container">
              <input type="password" id="motdepasse" name="motdepasse" required>
              <i class="fas fa-eye toggle-password" id="togglePassword"></i>
            </div>
            <?php if (isset($errors['motdepasse'])): ?>
              <span class="error-message"><?php echo htmlspecialchars($errors['motdepasse']); ?></span>
            <?php endif; ?>
          </div>
          
          <div class="form-group">
            <label for="confirmpassword">Confirmer le mot de passe</label>
            <div class="password-container">
              <input type="password" id="confirmpassword" name="confirmpassword" required>
              <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"></i>
            </div>
            <?php if (isset($errors['confirmpassword'])): ?>
              <span class="error-message"><?php echo htmlspecialchars($errors['confirmpassword']); ?></span>
            <?php endif; ?>
          </div>
        </div>
        
        <button type="submit" class="submit-btn">S'inscrire</button>
      </form>
      
      <div class="login-link">
        Vous avez déjà un compte? <a href="profil.php">Connectez-vous</a>
      </div>
    </div>
  </div>

  <script>
    // Fonction pour basculer la visibilité du mot de passe
    document.querySelectorAll('.toggle-password').forEach(icon => {
      icon.addEventListener('click', function() {
        const input = this.previousElementSibling;
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
      });
    });
    
    // Validation du formulaire côté client
    document.querySelector('form').addEventListener('submit', function(e) {
      const password = document.getElementById('motdepasse').value;
      const confirmPassword = document.getElementById('confirmpassword').value;
      
      if (password !== confirmPassword) {
        e.preventDefault();
        alert("Les mots de passe ne correspondent pas!");
      }
    });
  </script>
</body>
</html>