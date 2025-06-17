<?php
session_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=gestion_rdv_medical", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // On récupère les bons noms de champs
    $email = $_POST['email_patient'] ?? '';
    $mdp = $_POST['mot_de_passep'] ?? '';

    // On cherche dans la table patient
    $stmt = $pdo->prepare("SELECT * FROM patient WHERE email_patient = ?");
    $stmt->execute([$email]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($patient && password_verify($mdp, $patient['Mot_de_passep'])) {
        $_SESSION['utilisateur_id'] = $patient['ID_patient'];
        $_SESSION['nom'] = $patient['Nom_patient'];
        $_SESSION['email'] = $patient['email_patient'];
        $_SESSION['role'] = 'patient';
        header("Location: index.php");
        exit();
    } else {
        $erreur = "Identifiants invalides.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion Patient</title>
  <style>
    body { font-family: Arial; background: #f0f0f0; display: flex; justify-content: center; align-items: center; height: 100vh; }
    .container { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 350px; }
    input, button { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ccc; }
    button { background-color: #4be0c2; color: white; font-weight: bold; border: none; }
    .error { color: red; text-align: center; }
    h2 { text-align: center; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Connexion</h2>
    <?php if ($erreur): ?>
      <p class="error"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>
    <form method="POST" action="">
      <input type="email" name="email_patient" placeholder="Email" required>
      <input type="password" name="mot_de_passep" placeholder="Mot de passe" required>
      <button type="submit">Se connecter</button>
    </form>
  </div>
</body>
</html>
