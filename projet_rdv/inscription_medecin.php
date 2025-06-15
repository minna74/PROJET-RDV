<?php
require_once 'config.php';
$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $specialite = $_POST['specialite'];
    $email = $_POST['email'];
    $tel = $_POST['tel'];
    $tarif = $_POST['tarif'];
    $horaires = $_POST['horaires'];
    $motdepasse = $_POST['motdepasse'];
    $motdepasse2 = $_POST['motdepasse2'];

    if ($motdepasse !== $motdepasse2) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        $hash = password_hash($motdepasse, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO medecin (Nom_med, Prenom_med, Specialite, email_med, Numtel_med, Mot_de_passe, Tarif, Horaires_disponible)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $specialite, $email, $tel, $hash, $tarif, $horaires]);
            $succes = "Inscription réussie. Vous pouvez maintenant vous connecter.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $erreur = "L'email est déjà utilisé.";
            } else {
                $erreur = "Erreur : " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Médecin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f8ff; }
        .register-container {
            max-width: 650px;
            margin-top: 50px;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .logo {
            width: 70px;
            margin-bottom: 20px;
        }
        .btn-login {
            background-color: #6c757d;
            border: none;
        }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center">
    <div class="register-container">
        <div class="text-center">
            <img src="assets/logo.jpg" alt="Logo" class="logo rounded-circle">
            <h4 class="text-primary">Inscription Médecin</h4>
        </div>

        <?php if ($erreur): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
        <?php elseif ($succes): ?>
            <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" required>
                </div>
                <div class="col">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="prenom" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Spécialité</label>
                <input type="text" name="specialite" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Téléphone</label>
                <input type="text" name="tel" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tarif consultation (€)</label>
                <input type="number" step="0.01" name="tarif" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Horaires disponibles</label>
                <textarea name="horaires" class="form-control" rows="3" placeholder="Ex : Lundi 8h-12h; Mardi 10h-17h"></textarea>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="motdepasse" class="form-control" required>
                </div>
                <div class="col">
                    <label class="form-label">Confirmation</label>
                    <input type="password" name="motdepasse2" class="form-control" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-2">S'inscrire</button>
        </form>

        <a href="login.php" class="btn btn-login w-100 text-white">Se connecter</a>
    </div>
</div>
</body>
</html>
