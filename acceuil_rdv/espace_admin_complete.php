<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: ?page=login");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'], $_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM administrateur WHERE Email_admin = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($admin && password_verify($password, $admin['Mot_de_passep'])) {
        $_SESSION['admin'] = $admin['Prenom_admin'] . " " . $admin['Nom_admin'];
        $_SESSION['admin_id'] = $admin['ID_admin'];
        header("Location: ?page=dashboard");
        exit();
    } else {
        $error = "Identifiants incorrects";
    }
}

$page = $_GET['page'] ?? 'login';

function nav_bar() {
    echo '
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container-fluid">
        <a class="navbar-brand" href="?page=dashboard">Espace Admin</a>
        <div class="d-flex">
          <a href="accueilprin.php" class="btn btn-outline-light me-2">🏠 Accueil site</a>
          <span class="navbar-text text-white me-3">Connecté : ' . $_SESSION['admin'] . '</span>
          <a href="?action=logout" class="btn btn-outline-light">Déconnexion</a>
        </div>
      </div>
    </nav>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
if ($page === 'login' && !isset($_SESSION['admin'])) {
    ?>
    <div class="container mt-5">
        <div class="row justify-content-center"><div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center"><h4>Connexion Administrateur</h4></div>
                <div class="card-body">
                    <form method="POST">
                        <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
                        <input type="password" name="password" class="form-control mb-3" placeholder="Mot de passe" required>
                        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                        <button class="btn btn-primary w-100">Se connecter</button>
                    </form>
                </div>
            </div>
        </div></div>
    </div>
    <?php
} else if (!isset($_SESSION['admin'])) {
    header("Location: ?page=login");
    exit();
} else if ($page === 'dashboard') {
    nav_bar();
    ?>
    <div class="container mt-5">
        <h2 class="mb-4">Tableau de bord</h2>
        <div class="row">
            <?php
            $nbPatients  = $pdo->query("SELECT COUNT(*) FROM patient")->fetchColumn();
            $nbMedecins  = $pdo->query("SELECT COUNT(*) FROM medecin")->fetchColumn();
            $nbRDVAttente = $pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE Statut = 'en attente'")->fetchColumn();
            $nbModifs    = $pdo->query("SELECT COUNT(*) FROM modifier")->fetchColumn();
            ?>
            <div class="col-md-3"><div class="card text-bg-info mb-3"><div class="card-body"><h5>Patients</h5><p class="fs-4"><?= $nbPatients ?></p></div></div></div>
            <div class="col-md-3"><div class="card text-bg-primary mb-3"><div class="card-body"><h5>Médecins</h5><p class="fs-4"><?= $nbMedecins ?></p></div></div></div>
            <div class="col-md-3"><div class="card text-bg-warning mb-3"><div class="card-body"><h5>RDV en attente</h5><p class="fs-4"><?= $nbRDVAttente ?></p></div></div></div>
            <div class="col-md-3"><div class="card text-bg-danger mb-3"><div class="card-body"><h5>Modifs à valider</h5><p class="fs-4"><?= $nbModifs ?></p></div></div></div>
        </div>
        <ul class="list-group mt-4">
            <li class="list-group-item"><a href="?page=medecins">✅ Gérer les médecins</a></li>
            <li class="list-group-item"><a href="?page=rdv">📅 Gérer les rendez-vous</a></li>
            <li class="list-group-item"><a href="?page=planning">🕐 Voir planning des médecins</a></li>
            <li class="list-group-item"><a href="?page=modifications">✏️ Modifications à valider</a></li>
        </ul>
    </div>
    <?php
} else if ($page === 'medecins') {
    nav_bar();
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $pdo->prepare("DELETE FROM medecin WHERE ID_medecin = ?")->execute([$id]);
        header("Location: ?page=medecins");
        exit();
    }
    ?>
    <div class="container mt-5">
        <h2>Gestion des médecins</h2>
        <?php
        $stmt = $pdo->query("SELECT * FROM medecin ORDER BY Nom_med");
        $medecins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($medecins) > 0) {
            echo '<table class="table table-striped">';
            echo '<thead><tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Spécialité</th><th>Email</th><th>Téléphone</th><th>Tarif</th><th>Horaires</th><th>Actions</th></tr></thead><tbody>';
            foreach ($medecins as $med) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($med['ID_medecin']) . '</td>';
                echo '<td>' . htmlspecialchars($med['Nom_med']) . '</td>';
                echo '<td>' . htmlspecialchars($med['Prenom_med']) . '</td>';
                echo '<td>' . htmlspecialchars($med['Specialite']) . '</td>';
                echo '<td>' . htmlspecialchars($med['email_med']) . '</td>';
                echo '<td>' . htmlspecialchars($med['Numtel_med']) . '</td>';
                echo '<td>' . htmlspecialchars($med['Tarif']) . ' €</td>';
                echo '<td>' . nl2br(htmlspecialchars($med['Horaires_disponible'])) . '</td>';
                echo '<td><a href="?page=medecins&action=delete&id=' . $med['ID_medecin'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Supprimer ?\')">Supprimer</a></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo "<p>Aucun médecin trouvé.</p>";
        }
        ?>
    </div>
    <?php
} else if ($page === 'rdv') {
    nav_bar();
    if (isset($_GET['action'], $_GET['id'])) {
        $id = intval($_GET['id']);
        if ($_GET['action'] === 'confirm') {
            $stmt = $pdo->prepare("UPDATE rendez_vous SET Statut = 'confirmé' WHERE ID_rendez_vous = ?");
            $stmt->execute([$id]);
        } else if ($_GET['action'] === 'cancel') {
            $stmt = $pdo->prepare("UPDATE rendez_vous SET Statut = 'annulé' WHERE ID_rendez_vous = ?");
            $stmt->execute([$id]);
        }
        header("Location: ?page=rdv");
        exit();
    }
    ?>
    <div class="container mt-5">
        <h2>Gestion des rendez-vous</h2>
        <?php
        $stmt = $pdo->query(
            "SELECT r.ID_rendez_vous, r.Date_RDV, r.Heure, r.Statut, r.Specialite, r.Motif,
                    p.Nom_patient, p.Prenom_patient, m.Nom_med, m.Prenom_med
             FROM rendez_vous r
             JOIN patient p ON r.ID_patient = p.ID_patient
             JOIN medecin m ON r.ID_medecin = m.ID_medecin
             ORDER BY r.Date_RDV, r.Heure"
        );
        $rdvs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($rdvs) > 0) {
            echo '<table class="table table-bordered">';
            echo '<thead><tr><th>Date</th><th>Heure</th><th>Statut</th><th>Spécialité</th><th>Patient</th><th>Médecin</th><th>Motif</th><th>Actions</th></tr></thead><tbody>';
            foreach ($rdvs as $rdv) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($rdv['Date_RDV']) . '</td>';
                echo '<td>' . htmlspecialchars(substr($rdv['Heure'], 0, 5)) . '</td>';
                echo '<td>' . htmlspecialchars($rdv['Statut']) . '</td>';
                echo '<td>' . htmlspecialchars($rdv['Specialite']) . '</td>';
                echo '<td>' . htmlspecialchars($rdv['Nom_patient'] . ' ' . $rdv['Prenom_patient']) . '</td>';
                echo '<td>' . htmlspecialchars($rdv['Nom_med'] . ' ' . $rdv['Prenom_med']) . '</td>';
                echo '<td>' . htmlspecialchars($rdv['Motif']) . '</td>';
                echo '<td>';
                echo '<a href="?page=rdv&action=confirm&id=' . $rdv['ID_rendez_vous'] . '" class="btn btn-success btn-sm me-1" onclick="return confirm(\'Confirmer ?\')">Confirmer</a>';
                echo '<a href="?page=rdv&action=cancel&id=' . $rdv['ID_rendez_vous'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Annuler ?\')">Annuler</a>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo "<p>Aucun rendez-vous trouvé.</p>";
        }
        ?>
    </div>
    <?php
} else if ($page === 'planning') {
    nav_bar();
    ?>
    <div class="container mt-5">
        <h2>Planning des médecins</h2>
        <?php
        $medecins = $pdo->query("SELECT Nom_med, Prenom_med, Specialite, Horaires_disponible FROM medecin ORDER BY Nom_med")->fetchAll(PDO::FETCH_ASSOC);
        if (count($medecins) > 0) {
            foreach ($medecins as $med) {
                echo '<div class="card mb-3">';
                echo '<div class="card-header"><strong>' . htmlspecialchars($med['Nom_med'] . ' ' . $med['Prenom_med']) . '</strong> - ' . htmlspecialchars($med['Specialite']) . '</div>';
                echo '<div class="card-body"><pre>' . htmlspecialchars($med['Horaires_disponible']) . '</pre></div>';
                echo '</div>';
            }
        } else {
            echo "<p>Aucun médecin trouvé.</p>";
        }
        ?>
    </div>
    <?php
} else if ($page === 'modifications') {
    nav_bar();
    if (isset($_GET['action'], $_GET['id'])) {
        $id = intval($_GET['id']);
        $pdo->prepare("DELETE FROM modifier WHERE ID_modification = ?")->execute([$id]);
        header("Location: ?page=modifications");
        exit();
    }
    ?>
    <div class="container mt-5">
        <h2>Modifications à valider</h2>
        <?php
        $stmt = $pdo->query(
            "SELECT mo.ID_modification, a.Prenom_admin, a.Nom_admin, d.ID_dossier, mo.Date_modification, mo.Description_modification
             FROM modifier mo
             LEFT JOIN administrateur a ON mo.ID_admin = a.ID_admin
             LEFT JOIN dossier d ON mo.ID_dossier = d.ID_dossier
             ORDER BY mo.Date_modification DESC"
        );
        $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($mods) > 0) {
            echo '<table class="table table-striped">';
            echo '<thead><tr><th>ID</th><th>Admin</th><th>ID Dossier</th><th>Date</th><th>Description</th><th>Actions</th></tr></thead><tbody>';
            foreach ($mods as $mod) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($mod['ID_modification']) . '</td>';
                echo '<td>' . htmlspecialchars($mod['Prenom_admin'] . ' ' . $mod['Nom_admin']) . '</td>';
                echo '<td>' . htmlspecialchars($mod['ID_dossier']) . '</td>';
                echo '<td>' . htmlspecialchars($mod['Date_modification']) . '</td>';
                echo '<td>' . htmlspecialchars($mod['Description_modification']) . '</td>';
                echo '<td>';
                echo '<a href="?page=modifications&action=validate&id=' . $mod['ID_modification'] . '" class="btn btn-success btn-sm me-1" onclick="return confirm(\'Valider ?\')">Valider</a>';
                echo '<a href="?page=modifications&action=delete&id=' . $mod['ID_modification'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Supprimer ?\')">Supprimer</a>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo "<p>Aucune modification à valider.</p>";
        }
        ?>
    </div>
    <?php
} else {
    header("Location: ?page=dashboard");
    exit();
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>