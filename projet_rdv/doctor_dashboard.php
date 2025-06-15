 <?php
require_once 'config.php';
?>

 
 <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Médecin - Cabinet Médical</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar bg-primary text-white">
            <div class="sidebar-header text-center py-4">
<a href="../acceuil_rdv/index.php">
  <img src="assets/logo.jpg" alt="Logo Cabinet" class="logo img-fluid">
</a>
                <h4>Dr. [Nom]</h4>
                <p class="text-muted">[Spécialité]</p>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-target="dashboard">
                        <i class="fas fa-calendar-alt me-2"></i> Agenda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-target="availability">
                        <i class="fas fa-clock me-2"></i> Disponibilités
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-target="patients">
                        <i class="fas fa-users me-2"></i> Patients
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#stats-section" id="stats-link">
                        <i class="fas fa-chart-bar me-2"></i> Statistiques
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-target="settings">
                        <i class="fas fa-cog me-2"></i> Paramètres
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <header class="bg-light py-3">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 id="page-title">Tableau de bord</h2>
                        <div>
                            <button class="btn btn-outline-primary me-2" id="home-btn">
                                Accueil
                            </button>
                            <button class="btn btn-outline-danger me-2" id="emergency-btn">
                                <i class="fas fa-exclamation-triangle"></i> Urgence
                            </button>
                            <div class="dropdown d-inline">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="profile.php">Profil</a></li>
                                    <li><a class="dropdown-item" href="inscription_medecin.php">Déconnexion</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content-container p-4">
                <!-- Sections originales -->
                <div id="dashboard-content">
                    <section class="mb-5">
                        <div class="d-flex justify-content-between mb-3">
                            <h3>Agenda des rendez-vous</h3>
                            <div>
                               
                                <button class="btn btn-primary me-2" id="prev-week">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <span id="current-week" class="fw-bold">Semaine du 12 au 18 juin 2025</span>
                                <button class="btn btn-primary ms-2" id="next-week">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="appointments-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Heure</th>
                                        <th>Lundi</th>
                                        <th>Mardi</th>
                                        <th>Mercredi</th>
                                        <th>Jeudi</th>
                                        <th>Vendredi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <!-- Contenu dynamique -->
                                       <!-- Exemple de ligne avec rendez-vous -->
              
<tbody>
<?php
// Cette partie est possible grâce à config.php que tu as inclus en haut du fichier
try {
    $stmt = $pdo->query("
        SELECT r.Heure, r.Date_RDV, p.Nom_patient, p.Prenom_patient, r.Motif, r.Statut
        FROM rendez_vous r
        JOIN patient p ON r.ID_patient = p.ID_patient
        WHERE WEEK(r.Date_RDV) = WEEK(NOW())
        ORDER BY r.Heure
    ");

    // Organiser les RDV par heure et jour
    $rdvs = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $heure = substr($row['Heure'], 0, 5); // ex : "09:00"
        $jour = date('l', strtotime($row['Date_RDV'])); // ex : "Monday"
        $rdvs[$heure][$jour] = $row;
    }

    foreach ($rdvs as $heure => $jours) {
        echo "<tr>";
        echo "<td>$heure</td>";

        $jours_semaine = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        foreach ($jours_semaine as $j) {
            if (isset($jours[$j])) {
                $data = $jours[$j];
                $nom = $data['Nom_patient'] . ' ' . $data['Prenom_patient'];
                $motif = $data['Motif'];
                $statut = $data['Statut'];
                $badge = match($statut) {
                    'confirmé' => 'bg-success',
                    'en attente' => 'bg-warning text-dark',
                    'urgent' => 'bg-danger',
                    'annulé' => 'bg-secondary',
                    'terminé' => 'bg-info',
                    default => 'bg-light'
                };
                echo "<td>
                    <div class='appointment-slot'>
                        <strong>$nom</strong><br>
                        <small>$motif</small><br>
                        <span class='badge $badge'>$statut</span>
                    </div>
                </td>";
            } else {
                echo "<td>Disponible</td>";
            }
        }

        echo "</tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='6'>Erreur de chargement : " . $e->getMessage() . "</td></tr>";
}
?>
</tbody>


                <!-- Autres lignes... -->
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="mb-5" id="today-appointments">
                        <h3 class="mb-3">Rendez-vous aujourd'hui</h3>
                        <div class="row" id="today-list">
                            <!-- Contenu dynamique -->
                        </div>
                    </section>

                    <section id="stats-section">
                        <h3 class="mb-3">Statistiques</h3>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Taux d'occupation</h5>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 75%">75%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">RDV annulés</h5>
                                        <p class="display-6">12%</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Durée moyenne</h5>
                                        <p class="display-6">22 min</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Sections des boutons cliquables (initialement cachées) -->
                <div id="availability-content" class="d-none">
                    <div class="card">
                        <div class="card-body">
                            <h3>Gestion des disponibilités</h3>
                            <div class="mb-3">
                                <label class="form-label">Heure d'ouverture</label>
                                <input type="time" class="form-control" value="08:00">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Heure de fermeture</label>
                                <input type="time" class="form-control" value="18:00">
                            </div>
                            <button class="btn btn-primary">Enregistrer</button>
                        </div>
                    </div>
                </div>

                <div id="patients-content" class="d-none">
                    <div class="card">
                        <div class="card-body">
                            <h3>Gestion des patients</h3>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Rechercher un patient...">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <!-- Contenu dynamique -->
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="settings-content" class="d-none">
                    <div class="card">
                        <div class="card-body">
                            <h3>Paramètres du compte</h3>
                            <div class="mb-3">
                                <label class="form-label">Langue</label>
                                <select class="form-select">
                                    <option value="fr">Français</option>
                                    <option value="ar">العربية</option>
                                </select>
                            </div>
                            <button class="btn btn-primary">Enregistrer</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="appointmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Détails du rendez-vous</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="appointment-form">
                        <div class="mb-3">
                            <label class="form-label">Patient</label>
                            <input type="text" class="form-control" id="patient-name" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date/Heure</label>
                            <input type="text" class="form-control" id="appointment-time" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Motif</label>
                            <textarea class="form-control" id="appointment-reason" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" id="appointment-status">
                                <option value="confirmed">Confirmé</option>
                                <option value="cancelled">Annulé</option>
                                <option value="completed">Terminé</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" id="save-appointment">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/doctor.js" type="module"></script>
    <script src="js/modules/navigation.js" type="module"></script>
    <script src="js/modules/agenda.js" type="module"></script>
    <script src="js/modules/availability.js" type="module"></script>
    <script src="js/modules/patients.js" type="module"></script>
    <script src="js/modules/settings.js" type="module"></script>
</body>
</html>