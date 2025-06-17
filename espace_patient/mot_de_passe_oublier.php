<?php
session_start();
require_once 'db_connect.php'; // Inclure le fichier de connexion à la base de données

$message = '';
$messageType = ''; // 'success' or 'error'

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        $message = "Veuillez entrer votre adresse e-mail.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Le format de l'adresse e-mail est invalide.";
        $messageType = "error";
    } else {
        try {
            // Dans une application réelle:
            // 1. Vérifier si l'email existe dans votre base de données.
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $userExists = $stmt->fetch();

            if ($userExists) {
                // 2. Générer un jeton de réinitialisation unique et stocker-le en DB avec une date d'expiration.
                // $token = bin2hex(random_bytes(32));
                // $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                // $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires) VALUES (?, ?, ?)");
                // $stmt->execute([$userExists['id'], $token, $expires]);

                // 3. Envoyer un email à $email avec un lien contenant le jeton.
                //    Exemple: mail($email, "Réinitialisation de votre mot de passe", "Cliquez sur ce lien pour réinitialiser: votre_site.com/reset_password.php?token=" . $token);

                $message = "Si cette adresse e-mail est associée à un compte, un lien de réinitialisation de mot de passe vous a été envoyé.";
                $messageType = "success";
            } else {
                // Ne pas indiquer si l'email n'existe pas pour des raisons de sécurité (empêcher l'énumération d'emails)
                $message = "Si cette adresse e-mail est associée à un compte, un lien de réinitialisation de mot de passe vous a été envoyé.";
                $messageType = "success"; // Afficher le même message pour des raisons de sécurité
            }
        } catch (PDOException $e) {
            error_log("Password reset request error: " . $e->getMessage());
            $message = "Une erreur est survenue lors du traitement de votre demande. Veuillez réessayer.";
            $messageType = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Réinitialisation du mot de passe - Shafadmedcare</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(to right, #61d4c3, #e9fffc);
      color: #f0fdf4;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    }

    .reset-container {
      background: #ecfdf5;
      color: #7ad8bf;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #4ebdac;
    }

   form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    label {
      font-weight: bold;
      color: #555;
      text-align: left;
    }

    input[type="email"],
    button {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      border-radius: 8px;
      box-sizing: border-box;
    }

    input[type="email"] {
      border: 2px solid #1f9b8c;
    }

    button {
      background-color: #61d4c3;
      color: white;
      border: none;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background-color: #137667;
    }

    .back-link {
      text-align: center;
      margin-top: 15px;
    }

    .back-link a {
      color: #06ae9e;
      text-decoration: none;
      font-weight: bold;
    }

    .back-link a:hover {
      text-decoration: underline;
    }

    /* Styles for messages */
    .message {
      margin-top: 15px;
      padding: 10px;
      border-radius: 8px;
      text-align: center;
      font-weight: bold;
    }

    .message.success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .message.error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
  </style>
</head>
<body>

  <div class="reset-container">
    <h2>Réinitialiser le mot de passe</h2>
    <form method="POST" action="mot_de_passe_oublier.php">
      <label for="email">Adresse e-mail :</label>
      <input type="email" id="email" name="email" placeholder="Votre e-mail" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
      <button type="submit">Envoyer le lien de réinitialisation</button>
    </form>

    <?php if (!empty($message)): ?>
      <div class="message <?php echo $messageType; ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <div class="back-link">
      <a href="page_de_login.php">Retour à la connexion</a>
    </div>
  </div>

  <script>
    // Hide messages after a few seconds
    document.addEventListener('DOMContentLoaded', function() {
        const messageDiv = document.querySelector('.message');
        if (messageDiv) {
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 5000); // Hide after 5 seconds
        }
    });
  </script>

</body>
</html>