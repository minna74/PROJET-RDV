<?php
session_start();
require_once 'db_connect.php';

// Si l'utilisateur est déjà connecté, le rediriger
if (isset($_SESSION['user_id'])) {
    header('Location: proto.php');
    exit();
}

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $loginError = "Veuillez saisir votre email et votre mot de passe.";
    } else {
        try {
            // Rechercher l'utilisateur par email
           $stmt = $pdo->prepare("SELECT ID_patient AS id, Nom_patient AS nom, Prenom_patient AS prenom, Mot_de_passep AS mot_de_passe FROM patient WHERE email_patient = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['mot_de_passe'])) {
                // Mot de passe correct, démarrer la session utilisateur
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                header("Location: proto.php"); // Rediriger vers la page d'accueil après connexion
                exit();
            } else {
                $loginError = "Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage()); // Pour le débogage serveur
            $loginError = "Une erreur est survenue lors de la connexion. Veuillez réessayer.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Connexion - Shafadmedcare</title>
  <link rel="stylesheet" href="style de page de login.css">
</head>
<body>
  <div class="login-container">
      <div class="left-side">
        <a class="connect"  href="page_de_login.php">CONNEXION</a>
        <a class="inscri"  href="inscription.php">INSCRIPTION</a>
      </div>

    <div class="right-side">
      <div class="logo">
        <h2>CONNEXION</h2>
      </div>
      <form method="POST" action="page_de_login.php">
        <div class="input-group">
          <label for="email">Email</label>
          <input type="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
        </div>
        <div class="input-group">
          <label for="password">Mot de passe</label>
          <input type="password" name="password" placeholder="mot de passe" required>
        </div>
        <a href="mot_de_passe_oublier.php" class="forgot-password">Mot de passe oublié ?</a>
        <button type="submit" class="login-button">SE CONNECTER</button>
      </form>
      <?php if (!empty($loginError)): ?>
        <p style="color: red; text-align: center; margin-top: 15px;"><?php echo htmlspecialchars($loginError); ?></p>
      <?php endif; ?>
      <div class="social-login">
        <p>SE CONNECTER AVEC :</p>
        <div class="social-buttons">
          <a href="#" class="social-btn google">
            <img src="https://img.icons8.com/color/48/000000/google-logo.png" alt="Google Logo">
            Google
          </a>
          <a href="#" class="social-btn facebook">
            <img src="https://img.icons8.com/color/48/000000/facebook-new.png" alt="Facebook Logo">
            Facebook
          </a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>