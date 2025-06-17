<?php
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $date = $_POST['date_naiss'];
    $tel = $_POST['tel'];
    $mdp = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=gestion_rdv_medical", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $check = $pdo->prepare("SELECT * FROM patient WHERE email_patient = ?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {
            $message = "Cet email est déjà utilisé.";
        } else {
            $insert = $pdo->prepare("INSERT INTO patient (Nom_patient, Prenom_patient, Date_naiss, email_patient, Numtel, Mot_de_passep) VALUES (?, ?, ?, ?, ?, ?)");
            $insert->execute([$nom, $prenom, $date, $email, $tel, $mdp]);
            $message = "✅ Inscription réussie. <a href='connexion.php'>Connectez-vous ici</a>";
        }
    } catch (PDOException $e) {
        $message = "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription Patient</title>
  <style>
    body { font-family: Arial; background: #f2f2f2; display: flex; justify-content: center; align-items: center; height: 100vh; }
    .container { background: white; padding: 2rem; border-radius: 10px; width: 400px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    .container h2 { text-align: center; }
    input { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; }
    button { width: 100%; padding: 10px; background: #4be0c2; color: white; border: none; font-weight: bold; border-radius: 5px; }
    .message { text-align: center; margin-top: 10px; color: red; }
    .success { color: green; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Inscription </h2>
    <?php if ($message): ?>
      <p class="message <?= str_contains($message, '✅') ? 'success' : '' ?>"><?= $message ?></p>
    <?php endif; ?>
    <form method="POST">
      <input name="nom" placeholder="Nom" required>
      <input name="prenom" placeholder="Prénom" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="date" name="date_naiss" required>
      <input name="tel" placeholder="Téléphone" required>
      <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
      <button type="submit">S'inscrire</button>
    </form>
  </div>
</body>
</html>
