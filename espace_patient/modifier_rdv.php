<?php
session_start();
require_once 'db_connect.php';

//if (!isset($_SESSION['user_id'])) {
 //   header("Location: page de login.html");
 //   exit();
//}

$patient_id = $_SESSION['user_id'];
$rdv_id = isset($_GET['id']) ? (int)$_GET['id'] : 0; // ID du rendez-vous à modifier

$rdv_data = null;
if ($rdv_id > 0) {
    // Récupérer les détails du rendez-vous et s'assurer qu'il appartient bien au patient connecté
    $sql = "SELECT r.ID_rdv, r.Date_rdv, r.Heure_rdv, r.Notes, m.Nom_med, m.Prenom_med, m.Specialite
            FROM rendez_vous r
            JOIN medecin m ON r.ID_medecin = m.ID_medecin
            WHERE r.ID_rdv = ? AND r.ID_patient = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $rdv_id, $patient_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $rdv_data = $result->fetch_assoc();
        } else {
            // Rendez-vous non trouvé ou n'appartient pas à l'utilisateur
            $_SESSION['error_message'] = "Rendez-vous introuvable ou vous n'avez pas l'autorisation de le modifier.";
            header("Location: rendez_vous.php");
            exit();
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Erreur de préparation de la requête de récupération: " . $conn->error;
        header("Location: rendez_vous.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "ID de rendez-vous non valide.";
    header("Location: rendez_vous.php");
    exit();
}

// Gérer la soumission du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_rdv'])) {
    $new_date = $_POST['date_rdv'] ?? '';
    $new_time = $_POST['heure_rdv'] ?? '';
    $new_notes = $_POST['notes'] ?? '';

    $errors = [];

    // Validation des inputs
    if (empty($new_date) || empty($new_time)) {
        $errors[] = "La date et l'heure sont requises.";
    }
    // Ajoutez plus de validation si nécessaire (ex: format de date, heure valide, etc.)

    if (empty($errors)) {
        // Mettre à jour le rendez-vous dans la base de données
        // Vous ne pouvez modifier que la date, l'heure et les notes.
        // Changer le médecin nécessiterait un processus de "re-planification".
        $update_sql = "UPDATE rendez_vous SET Date_rdv = ?, Heure_rdv = ?, Notes = ? WHERE ID_rdv = ? AND ID_patient = ?";
        if ($update_stmt = $conn->prepare($update_sql)) {
            $update_stmt->bind_param("sssis", $new_date, $new_time, $new_notes, $rdv_id, $patient_id);
            if ($update_stmt->execute()) {
                $_SESSION['success_message'] = "Rendez-vous mis à jour avec succès !";
                header("Location: rendez_vous.php");
                exit();
            } else {
                $errors[] = "Erreur lors de la mise à jour du rendez-vous: " . $update_stmt->error;
            }
            $update_stmt->close();
        } else {
            $errors[] = "Erreur de préparation de la requête de mise à jour: " . $conn->error;
        }
    }
    // Si des erreurs surviennent, elles seront affichées sur la page du formulaire
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Modifier Rendez-vous</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
    <style>
        /* Copiez ici le CSS pertinent de vos autres pages pour une cohérence */
        body {
            min-height: 100vh;
            background: linear-gradient(120deg, #f8fafc 0%, #e0ecf7 100%);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-top: 50px;
            max-width: 600px;
        }
        .btn-primary {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        .btn-primary:hover {
            background-color: #1a56c7;
            border-color: #1a56c7;
        }
        .error {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Modifier le rendez-vous</h2>
        <?php
        if (!empty($errors)) {
            echo '<div class="alert alert-danger" role="alert">';
            foreach ($errors as $error) {
                echo $error . '<br>';
            }
            echo '</div>';
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
            unset($_SESSION['error_message']); // Nettoyer le message d'erreur
        }
        ?>

        <?php if ($rdv_data): ?>
            <form action="modifier_rdv.php?id=<?php echo htmlspecialchars($rdv_data['ID_rdv']); ?>" method="POST">
                <div class="mb-3">
                    <label for="medecin_info" class="form-label">Médecin</label>
                    <input type="text" id="medecin_info" class="form-control" value="Dr. <?php echo htmlspecialchars($rdv_data['Prenom_med'] . ' ' . $rdv_data['Nom_med'] . ' (' . $rdv_data['Specialite'] . ')'); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="date_rdv" class="form-label">Nouvelle Date</label>
                    <input type="date" id="date_rdv" name="date_rdv" class="form-control" value="<?php echo htmlspecialchars($rdv_data['Date_rdv']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="heure_rdv" class="form-label">Nouvelle Heure</label>
                    <input type="time" id="heure_rdv" name="heure_rdv" class="form-control" value="<?php echo htmlspecialchars($rdv_data['Heure_rdv']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes (optionnel)</label>
                    <textarea id="notes" name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($rdv_data['Notes'] ?? ''); ?></textarea>
                </div>
                <button type="submit" name="update_rdv" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="rendez_vous.php" class="btn btn-secondary">Annuler</a>
            </form>
        <?php else: ?>
            <p>Impossible de charger les détails du rendez-vous.</p>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>