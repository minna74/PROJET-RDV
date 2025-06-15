<?php
session_start();
require_once 'config.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $motdepasse = $_POST['motdepasse'] ?? '';

    $stmt = $pdo->prepare("SELECT ID_medecin, Mot_de_passe FROM medecin WHERE email_med = ?");
    $stmt->execute([$email]);
    $medecin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($medecin && password_verify($motdepasse, $medecin['Mot_de_passe'])) {
        $_SESSION['ID_medecin'] = $medecin['ID_medecin'];
        header("Location: doctor_dashboard.php");
        exit;
    } else {
        $erreur = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Médecin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #eaf0ff;
        }
        .login-container {
            max-width: 400px;
            margin-top: 100px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .logo {
            width: 80px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center">
    <div class="login-container">
        <div class="text-center">
            <img src="assets/logo.jpg" alt="Logo Cabinet" class="logo rounded-circle">
            <h4 class="mb-4 text-primary">Connexion Médecin</h4>
        </div>

        <?php if ($erreur): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="post" action="login.php">
            <div class="mb-3">
                <label class="form-label">Adresse email</label>
                <input type="email" class="form-control" name="email" required placeholder="ex: j.dupont@cabinet.com">
            </div>
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" class="form-control" name="motdepasse" required placeholder="********">
            </div>
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>
    </div>
</div>
</body>
</html>
