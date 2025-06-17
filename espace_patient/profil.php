<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: page_de_login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_nom'] ?? 'Utilisateur';
$userPrenom = $_SESSION['user_prenom'] ?? '';

$userData = [];
$successMessage = '';
$errorMessage = '';

try {
    // Correction: Utilisation de la table PATIENT au lieu de USERS
    $stmt = $pdo->prepare("SELECT 
        ID_patient AS id, 
        Nom_patient AS nom, 
        Prenom_patient AS prenom, 
        Date_naiss AS date_de_naissance, 
        email_patient AS email, 
        Numtel AS telephone 
        FROM patient 
        WHERE ID_patient = ?");
    
    $stmt->execute([$userId]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        header('Location: logout.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération du profil: " . $e->getMessage());
    $errorMessage = "Impossible de charger les informations de votre profil.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_profile'])) {
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
    $naissance = htmlspecialchars(trim($_POST['naissance'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $tel = htmlspecialchars(trim($_POST['tel'] ?? ''));

    if (empty($nom) || empty($prenom) || empty($email) || empty($tel) || empty($naissance)) {
        $errorMessage = "Tous les champs obligatoires doivent être remplis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Le format de l'adresse e-mail est invalide.";
    } else {
        try {
            // Correction: Requête UPDATE avec la table PATIENT
            $stmt = $pdo->prepare("UPDATE patient SET 
                Nom_patient = ?, 
                Prenom_patient = ?, 
                Date_naiss = ?, 
                email_patient = ?, 
                Numtel = ? 
                WHERE ID_patient = ?");
            
            $stmt->execute([$nom, $prenom, $naissance, $email, $tel, $userId]);

            $successMessage = "Votre profil a été mis à jour avec succès !";
            
            // Recharger les données
            $stmt = $pdo->prepare("SELECT 
                ID_patient AS id, 
                Nom_patient AS nom, 
                Prenom_patient AS prenom, 
                Date_naiss AS date_de_naissance, 
                email_patient AS email, 
                Numtel AS telephone 
                FROM patient 
                WHERE ID_patient = ?");
            $stmt->execute([$userId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            $_SESSION['user_nom'] = $userData['nom'];
            $_SESSION['user_prenom'] = $userData['prenom'];

        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du profil: " . $e->getMessage());
            $errorMessage = "Une erreur est survenue lors de la mise à jour. Veuillez réessayer.";
            if ($e->getCode() == '23000') {
                $errorMessage = "Cette adresse e-mail est déjà utilisée.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil - Shafadmedcare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #e0f7fa 0%, #f0f8ff 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            color: white;
            padding: 2rem 0;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .profile-container {
            max-width: 1000px;
            margin: -50px auto 30px;
            position: relative;
            z-index: 10;
        }
        
        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            color: white;
            font-size: 48px;
            font-weight: bold;
            border: 4px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        .profile-info {
            padding: 30px;
        }
        
        .info-item {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .info-label {
            font-weight: 600;
            min-width: 180px;
            color: #1a73e8;
        }
        
        .btn-edit-profile {
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(26, 115, 232, 0.3);
        }
        
        .btn-edit-profile:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(26, 115, 232, 0.4);
        }
        
        .edit-form {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
        }
        
        .form-control:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 0.2rem rgba(26, 115, 232, 0.25);
        }
        
        .alert-custom {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .alert-success-custom {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        
        .alert-danger-custom {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: #1a73e8;
            margin-bottom: 15px;
        }
        
        .feature-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #1a237e;
        }
        
        .profile-nav {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .nav-item {
            margin-bottom: 10px;
        }
        
        .nav-link {
            color: #4a5568;
            font-weight: 500;
            padding: 10px 15px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            background: #e3f2fd;
            color: #1a73e8;
        }
        
        @media (max-width: 768px) {
            .profile-container {
                margin-top: -30px;
            }
            
            .info-item {
                flex-direction: column;
            }
            
            .info-label {
                margin-bottom: 5px;
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Header avec le nom du patient -->
    <div class="profile-header text-center">
        <div class="container">
            <h1 class="mb-3">Mon Profil</h1>
            <p class="lead">Gérez vos informations personnelles et vos préférences</p>
        </div>
    </div>
    
    <!-- Contenu principal -->
    <div class="container profile-container">
        <div class="profile-card">
            <div class="row">
                <!-- Colonne de navigation -->
                <div class="col-lg-4">
                    <div class="profile-nav">
                        <div class="text-center mb-4">
                            <div class="profile-avatar">
                                <?php 
                                    $initials = substr($userPrenom, 0, 1) . substr($userName, 0, 1);
                                    echo $initials;
                                ?>
                            </div>
                            <h3 class="mt-3"><?php echo htmlspecialchars($userPrenom . ' ' . $userName); ?></h3>
                            <p class="text-muted">Patient chez ShafadMedCare</p>
                        </div>
                        
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link active" href="#"><i class="bi bi-person me-2"></i> Mon Profil</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="rendez_vous.php"><i class="bi bi-calendar me-2"></i> Mes Rendez-vous</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="Mes_medecins.php"><i class="bi bi-heart-pulse me-2"></i> Mes Médecins</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="ordonance.php"><i class="bi bi-file-medical me-2"></i> Mes Ordonnances</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="document.php"><i class="bi bi-file-earmark-text me-2"></i> Mes Résultats</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="messagerie.php"><i class="bi bi-chat-dots me-2"></i> Messagerie</a>
                            </li>
                            <li class="nav-item mt-4">
                                <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Déconnexion</a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h5 class="feature-title">Sécurité du compte</h5>
                        <p>Vos données médicales sont cryptées et protégées</p>
                    </div>
                </div>
                
                <!-- Colonne de contenu -->
                <div class="col-lg-8">
                    <div class="profile-info">
                        <h3 class="mb-4" style="color: #1a73e8;">Informations personnelles</h3>
                        
                        <?php if (!empty($successMessage)): ?>
                            <div class="alert alert-success-custom alert-custom">
                                <?php echo htmlspecialchars($successMessage); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($errorMessage)): ?>
                            <div class="alert alert-danger-custom alert-custom">
                                <?php echo htmlspecialchars($errorMessage); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div id="profilDisplay">
                            <div class="info-item">
                                <div class="info-label">Nom complet:</div>
                                <div><?php echo htmlspecialchars($userData['prenom'] . ' ' . $userData['nom']); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Date de naissance:</div>
                                <div>
                                    <?php 
                                        if (!empty($userData['date_de_naissance'])) {
                                            $date = new DateTime($userData['date_de_naissance']);
                                            echo $date->format('d/m/Y');
                                        } else {
                                            echo 'Non renseignée';
                                        }
                                    ?>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Email:</div>
                                <div><?php echo htmlspecialchars($userData['email'] ?? 'Non renseigné'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Téléphone:</div>
                                <div><?php echo htmlspecialchars($userData['telephone'] ?? 'Non renseigné'); ?></div>
                            </div>
                            
                            <button class="btn btn-edit-profile mt-3" id="editBtn">
                                <i class="bi bi-pencil me-2"></i>Modifier le profil
                            </button>
                        </div>
                        
                        <form id="editForm" class="edit-form" method="POST" action="profil.php" style="display: none;">
                            <h4 class="mb-4" style="color: #1a73e8;">Modifier mes informations</h4>
                            
                            <div class="mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" class="form-control" name="nom" value="<?php echo htmlspecialchars($userData['nom'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Prénom</label>
                                <input type="text" class="form-control" name="prenom" value="<?php echo htmlspecialchars($userData['prenom'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Date de Naissance</label>
                                <input type="date" class="form-control" name="naissance" value="<?php echo htmlspecialchars($userData['date_de_naissance'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" name="tel" value="<?php echo htmlspecialchars($userData['telephone'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="action-buttons">
                                <button type="submit" name="submit_profile" class="btn btn-edit-profile">
                                    <i class="bi bi-check-circle me-2"></i>Enregistrer
                                </button>
                                <button type="button" class="btn btn-secondary" id="cancelEditBtn">
                                    <i class="bi bi-x-circle me-2"></i>Annuler
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-5">
                            <h4 class="mb-4" style="color: #1a73e8;">Fonctionnalités du compte</h4>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="feature-card">
                                        <div class="feature-icon">
                                            <i class="bi bi-calendar-check"></i>
                                        </div>
                                        <h5 class="feature-title">Prendre un RDV</h5>
                                        <p>Prenez rendez-vous avec votre médecin en quelques clics</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div class="feature-card">
                                        <div class="feature-icon">
                                            <i class="bi bi-file-medical"></i>
                                        </div>
                                        <h5 class="feature-title">Documents médicaux</h5>
                                        <p>Accédez à tous vos documents médicaux en ligne</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div class="feature-card">
                                        <div class="feature-icon">
                                            <i class="bi bi-chat-dots"></i>
                                        </div>
                                        <h5 class="feature-title">Messagerie sécurisée</h5>
                                        <p>Échangez avec votre médecin en toute sécurité</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div class="feature-card">
                                        <div class="feature-icon">
                                            <i class="bi bi-bell"></i>
                                        </div>
                                        <h5 class="feature-title">Rappels</h5>
                                        <p>Recevez des rappels pour vos rendez-vous et traitements</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editBtn = document.getElementById('editBtn');
            const cancelEditBtn = document.getElementById('cancelEditBtn');
            const profilDisplay = document.getElementById('profilDisplay');
            const editForm = document.getElementById('editForm');
            
            if (editBtn) {
                editBtn.onclick = function() {
                    profilDisplay.style.display = 'none';
                    editForm.style.display = 'block';
                };
            }
            
            if (cancelEditBtn) {
                cancelEditBtn.onclick = function() {
                    editForm.style.display = 'none';
                    profilDisplay.style.display = 'block';
                };
            }
            
            // Masquer les messages après 5 secondes
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert-custom');
                alerts.forEach(alert => {
                    alert.style.display = 'none';
                });
            }, 5000);
        });
    </script>
</body>
</html>