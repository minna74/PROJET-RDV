<?php
// Connexion DB
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'gestion_rdv_medical';
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Erreur connexion DB : " . $conn->connect_error);

// Étape 1 : Inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $specialite = $_POST['specialite'];
    $email = $_POST['email'];
    $numtel = $_POST['numtel'];
    $mdp = password_hash($_POST['motdepasse'], PASSWORD_DEFAULT);
    $tarif = $_POST['tarif'] ?? 0;
    $dispo = $_POST['dispo'] ?? '';

    $stmt = $conn->prepare("INSERT INTO medecin (Nom_med, Prenom_med, Specialite, email_med, Numtel_med, Mot_de_passe, Tarif, Horaires_disponible)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssds", $nom, $prenom, $specialite, $email, $numtel, $mdp, $tarif, $dispo);
    if ($stmt->execute()) {
        $id_medecin = $stmt->insert_id;
    } else {
        die("Erreur lors de l'inscription.");
    }
}

// Étape 2 : Affichage tableau de bord si médecin existe
if (isset($id_medecin)) {
    $res_med = $conn->query("SELECT * FROM medecin WHERE ID_medecin = $id_medecin");
    $medecin = $res_med->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Médecin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2>Bienvenue Dr. <?= $medecin['Prenom_med'] . ' ' . $medecin['Nom_med'] ?> - <?= $medecin['Specialite'] ?></h2>
    <p>Email : <?= $medecin['email_med'] ?> | Téléphone : <?= $medecin['Numtel_med'] ?> | Tarif : <?= $medecin['Tarif'] ?> €</p>
    <hr>
    <h4>Agenda (données simulées)</h4>
    <table class="table table-bordered">
        <thead><tr><th>Heure</th><th>Lundi</th><th>Mardi</th><th>Mercredi</th><th>Jeudi</th><th>Vendredi</th></tr></thead>
        <tbody>
        <?php
        for ($h = 8; $h <= 17; $h++) {
            echo "<tr><td>{$h}:00 - " . ($h + 1) . ":00</td>";
            for ($d = 1; $d <= 5; $d++) echo "<td>Disponible</td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
<?php
} else {
?>
<!-- Formulaire d'inscription -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Médecin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2>Inscription Médecin</h2>
    <form method="post" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Prénom</label>
            <input type="text" name="prenom" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Spécialité</label>
            <input type="text" name="specialite" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Téléphone</label>
            <input type="text" name="numtel" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="motdepasse" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Tarif (€)</label>
            <input type="number" name="tarif" class="form-control" step="0.01">
        </div>
        <div class="col-12">
            <label class="form-label">Disponibilités (texte libre)</label>
            <textarea name="dispo" class="form-control"></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </div>
    </form>
</div>
</body>
</html>
<?php } ?>
