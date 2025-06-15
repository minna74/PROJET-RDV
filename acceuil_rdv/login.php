<?php
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=gestion_rdv_medical", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$email = $_POST['email'] ?? '';
$mot_de_passe = $_POST['mot_de_passe'] ?? '';

// Tentative avec les administrateurs
$stmt = $pdo->prepare("SELECT * FROM administrateur WHERE Email_admin = ?");
$stmt->execute([$email]);
$admin = $stmt->fetch();

if ($admin && password_verify($mot_de_passe, $admin['Mot_de_passep'])) {
    $_SESSION['utilisateur_id'] = $admin['ID_admin'];
    $_SESSION['nom'] = $admin['Nom_admin'];
    $_SESSION['role'] = 'admin';
    header("Location: profile.php");
    exit();
}

// Tentative avec les médecins
$stmt = $pdo->prepare("SELECT * FROM medecin WHERE email_med = ?");
$stmt->execute([$email]);
$med = $stmt->fetch();

if ($med && password_verify($mot_de_passe, $med['Mot_de_passe'])) {
    $_SESSION['utilisateur_id'] = $med['ID_medecin'];
    $_SESSION['nom'] = $med['Nom_med'];
    $_SESSION['role'] = 'medecin';
    header("Location: profile.php");
    exit();
}

// Tentative avec les patients
$stmt = $pdo->prepare("SELECT * FROM patient WHERE email_patient = ?");
$stmt->execute([$email]);
$pat = $stmt->fetch();

if ($pat && password_verify($mot_de_passe, $pat['Mot_de_passep'])) {
    $_SESSION['utilisateur_id'] = $pat['ID_patient'];
    $_SESSION['nom'] = $pat['Nom_patient'];
    $_SESSION['role'] = 'patient';
    header("Location: profile.php");
    exit();
}

// Sinon : erreur
echo "<p style='color:red;text-align:center;'>Identifiants invalides.</p>";
?>
