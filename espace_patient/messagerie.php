<?php
session_start();
require_once 'db_connect.php';

// Rediriger si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: page_de_login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_nom'] ?? 'Utilisateur';
$userPrenom = $_SESSION['user_prenom'] ?? '';

$discussions = [];
$currentDoctorId = $_GET['doctor_id'] ?? null; // ID du médecin si sélectionné via l'URL
$currentDoctorName = 'Médecin'; // Default
$currentDoctorAvatar = 'https://via.placeholder.com/50';

$messages = [];
$sendMessageError = '';
$sendMessageSuccess = '';

try {
    // Récupérer les discussions (liste des médecins avec qui l'utilisateur a échangé)
    // C'est une requête un peu plus complexe pour obtenir les derniers messages si vous voulez
    // Pour simplifier, nous allons juste lister les médecins et supposer une discussion avec eux.
    // Idéalement, vous feriez un SELECT DISTINCT sur les doctor_id depuis la table messages pour l'user_id
    $stmt = $pdo->prepare("
        SELECT DISTINCT d.id, d.nom, d.specialite, d.photo_url
        FROM doctors d
        LEFT JOIN appointments a ON d.id = a.doctor_id AND a.user_id = ?
        LEFT JOIN messages m ON (m.sender_id = d.id AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = d.id)
        WHERE a.user_id IS NOT NULL OR m.id IS NOT NULL
        ORDER BY d.nom
    ");
    $stmt->execute([$userId, $userId, $userId]);
    $discussions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si un doctor_id est spécifié, charger les messages de cette discussion
    if ($currentDoctorId) {
        // Vérifier que le doctor_id est bien associé à un médecin existant
        $doctorStmt = $pdo->prepare("SELECT id, nom, photo_url FROM doctors WHERE id = ?");
        $doctorStmt->execute([$currentDoctorId]);
        $doctorInfo = $doctorStmt->fetch(PDO::FETCH_ASSOC);

        if ($doctorInfo) {
            $currentDoctorName = $doctorInfo['nom'];
            $currentDoctorAvatar = $doctorInfo['photo_url'] ?: 'https://via.placeholder.com/50';

            // Récupérer les messages entre l'utilisateur et ce médecin
            $messageStmt = $pdo->prepare("
                SELECT message_text, sent_at,
                       CASE
                           WHEN sender_id = ? THEN 'self'
                           ELSE 'other'
                       END as from_who
                FROM messages
                WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
                ORDER BY sent_at ASC
            ");
            $messageStmt->execute([$userId, $userId, $currentDoctorId, $currentDoctorId, $userId]);
            $messages = $messageStmt->fetchAll(PDO::FETCH_ASSOC);

            // Marquer les messages reçus comme lus (si vous avez une colonne is_read)
            // $pdo->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0")
            //     ->execute([$currentDoctorId, $userId]);

        } else {
            $errorMessage = "Médecin non trouvé.";
            $currentDoctorId = null; // Invalide le doctor ID
        }
    }
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des discussions/messages: " . $e->getMessage());
    $errorMessage = "Impossible de charger les discussions.";
}

// Gérer l'envoi de message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiverId = $_POST['receiver_id'] ?? '';
    $messageText = htmlspecialchars(trim($_POST['message_text'] ?? ''));

    if (empty($receiverId) || empty($messageText)) {
        $sendMessageError = "Le message ne peut pas être vide.";
    } else {
        try {
            $insertMsgStmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)");
            $insertMsgStmt->execute([$userId, $receiverId, $messageText]);
            $sendMessageSuccess = "Message envoyé !";
            // Rediriger pour rafraîchir les messages après envoi
            header("Location: messagerie.php?doctor_id=" . htmlspecialchars($receiverId));
            exit();
        } catch (PDOException $e) {
            error_log("Erreur lors de l'envoi du message: " . $e->getMessage());
            $sendMessageError = "Erreur lors de l'envoi du message. Veuillez réessayer.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <title>Discussions & Notifications - Shafadmedcare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
    <style>
      body {
        min-height: 100vh;
        background: linear-gradient(120deg, #f8fafc 0%, #e0ecf7 100%);
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        position: relative;
        overflow-x: hidden;
      }
      /* Bulles décoratives animées */
      .bg-bubble {
        position: fixed;
        border-radius: 50%;
        opacity: 0.13;
        z-index: 0;
        animation: float 12s infinite alternate;
        pointer-events: none;
      }
      .bg-bubble.b1 { width: 180px; height: 180px; background: #2563eb; left: -60px; top: 80px; animation-delay: 0s;}
      .bg-bubble.b2 { width: 150px; height: 150px; background: #60a5fa; right: -50px; bottom: 100px; animation-delay: 2s;}
      .bg-bubble.b3 { width: 120px; height: 120px; background: #93c5fd; left: 10%; bottom: 20%; animation-delay: 4s;}
      .bg-bubble.b4 { width: 200px; height: 200px; background: #2563eb; right: 20%; top: 50px; animation-delay: 6s;}
      .bg-bubble.b5 { width: 100px; height: 100px; background: #60a5fa; left: 5%; top: 70%; animation-delay: 8s;}
      @keyframes float {
        0% { transform: translateY(0px) translateX(0px); }
        50% { transform: translateY(-15px) translateX(10px); }
        100% { transform: translateY(0px) translateX(0px); }
      }

      .navbar {
        background: #fff !important;
        border-bottom: 1px solid #e3eafc;
        box-shadow: 0 2px 8px 0 rgba(31,38,135,0.03);
        min-height: 56px;
      }
      .navbar .navbar-brand {
        color: #2563eb !important;
        font-weight: 700;
        font-size: 1.4rem;
        letter-spacing: 1px;
      }
      .navbar .nav-link {
        color: #4a5568 !important;
        font-weight: 500;
        margin-right: 15px;
      }
      .navbar .nav-link.active,
      .navbar .nav-link:hover {
        color: #2563eb !important;
      }
      .navbar .dropdown-menu {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      }
      .navbar .dropdown-item {
        color: #4a5568;
      }
      .navbar .dropdown-item:hover {
        background-color: #f0f4f8;
        color: #2563eb;
      }
      .header-hero {
        background: linear-gradient(45deg, #2563eb 0%, #60a5fa 100%);
        color: white;
        padding: 60px 0;
        text-align: center;
        border-radius: 0 0 15px 15px;
        margin-bottom: 30px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      }
      .header-hero .welcome-message {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        line-height: 1.2;
      }
      .header-hero .subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        max-width: 700px;
        margin: 0 auto;
      }
      .main-content {
        padding: 30px 0;
        position: relative;
        z-index: 10;
      }
      .chat-container {
        display: flex;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        overflow: hidden;
        min-height: 70vh;
      }
      .discussions-list {
        width: 35%;
        border-right: 1px solid #eee;
        padding: 20px;
        overflow-y: auto;
        background-color: #f8fafd;
      }
      .discussion-item {
        display: flex;
        align-items: center;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        background-color: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        cursor: pointer;
        transition: background-color 0.2s ease;
      }
      .discussion-item:hover, .discussion-item.active {
        background-color: #e0ecf7;
      }
      .discussion-item .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 15px;
      }
      .discussion-item .info {
        flex-grow: 1;
      }
      .discussion-item .name {
        font-weight: 600;
        color: #333;
      }
      .discussion-item .last-message {
        font-size: 0.9em;
        color: #777;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      .discussion-full {
        width: 65%;
        display: flex;
        flex-direction: column;
        display: <?php echo $currentDoctorId ? 'flex' : 'none'; ?>; /* Show only if a doctor is selected */
      }
      .discussion-header {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        background-color: #f8fafd;
      }
      .discussion-header .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
      }
      .discussion-header .discussion-name {
        font-weight: 600;
        color: #2563eb;
      }
      .discussion-header .close-btn {
        margin-left: auto;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #777;
      }
      .discussion-messages {
        flex-grow: 1;
        padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
      }
      .message {
        padding: 10px 15px;
        border-radius: 15px;
        margin-bottom: 10px;
        max-width: 75%;
        word-wrap: break-word;
      }
      .message.self {
        background-color: #2563eb;
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 3px;
      }
      .message.other {
        background-color: #e0ecf7;
        color: #333;
        align-self: flex-start;
        border-bottom-left-radius: 3px;
      }
      .message-date {
        font-size: 0.75em;
        color: rgba(255,255,255,0.7);
        margin-top: 5px;
        text-align: right;
      }
      .message.other .message-date {
        color: #666;
        text-align: left;
      }
      .message-input-area {
        border-top: 1px solid #eee;
        padding: 15px 20px;
        background-color: #f8fafd;
        display: flex;
      }
      .message-input-area input {
        flex-grow: 1;
        border-radius: 20px;
        border: 1px solid #ddd;
        padding: 10px 15px;
        margin-right: 10px;
      }
      .message-input-area button {
        background-color: #2563eb;
        color: white;
        border: none;
        border-radius: 20px;
        padding: 10px 20px;
        cursor: pointer;
      }
      .no-discussion-selected {
        flex-grow: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #777;
        font-size: 1.2rem;
        text-align: center;
      }

      @media (max-width: 768px) {
        .chat-container {
          flex-direction: column;
          min-height: 85vh; /* Adjust for smaller screens */
        }
        .discussions-list {
          width: 100%;
          border-right: none;
          border-bottom: 1px solid #eee;
          max-height: 40vh; /* Limit height for discussions list */
        }
        .discussion-full {
          width: 100%;
          height: 60vh; /* Remaining height for chat */
        }
        .discussion-full.hidden-on-mobile { /* For toggling visibility */
            display: none !important;
        }
        .discussions-list.hidden-on-mobile {
            display: none !important;
        }
      }
    </style>
  </head>
  <body>
    <div class="bg-bubble b1"></div>
    <div class="bg-bubble b2"></div>
    <div class="bg-bubble b3"></div>
    <div class="bg-bubble b4"></div>
    <div class="bg-bubble b5"></div>

    <nav class="navbar navbar-expand-lg bg-light sticky-top">
      <div class="container-fluid">
        <a class="navbar-brand" href="proto.php">Shafadmedcare</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link" href="proto.php">Accueil</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="rendez_vous.php">Mes Rendez-vous</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="Mes_medecins.php">Mes Médecins</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="ordonance.php">Mes Ordonnances</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="documents.php">Mes Résultats</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="messagerie.php">Messagerie</a>
            </li>
          </ul>
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-primary fw-semibold" href="#" id="patientDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Bonjour, <?php echo htmlspecialchars($userPrenom . ' ' . $userName); ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="profil.php">Mon profil</a></li>
                <li><a class="dropdown-item" href="logout.php">Déconnexion</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="header-hero">
      <div class="welcome-message">
        Ma Messagerie
      </div>
      <div class="subtitle">
        Communiquez directement avec vos médecins et recevez des notifications.
      </div>
    </div>

    <div class="main-content container">
      <div class="chat-container">
        <div class="discussions-list" id="discussionsSection" style="<?php echo $currentDoctorId ? 'display: none;' : 'display: block;'; ?>">
          <h5 class="mb-3 text-primary">Discussions</h5>
          <?php if (empty($discussions)): ?>
            <p class="text-muted text-center">Aucune discussion trouvée.</p>
          <?php else: ?>
            <?php foreach ($discussions as $discussion): ?>
              <a href="messagerie.php?doctor_id=<?php echo htmlspecialchars($discussion['id']); ?>" class="discussion-item <?php echo ($currentDoctorId == $discussion['id']) ? 'active' : ''; ?>">
                <img src="<?php echo htmlspecialchars($discussion['photo_url'] ?: 'https://via.placeholder.com/50'); ?>" class="avatar" alt="<?php echo htmlspecialchars($discussion['nom']); ?>" />
                <div class="info">
                  <div class="name">Dr. <?php echo htmlspecialchars($discussion['nom']); ?></div>
                  <div class="last-message">
                    Dernier message ici...
                  </div>
                </div>
              </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="discussion-full" id="discussionFull" style="<?php echo $currentDoctorId ? 'display: flex;' : 'display: none;'; ?>">
          <?php if ($currentDoctorId): ?>
            <div class="discussion-header">
              <button class="close-btn d-md-none" id="closeDiscussion" type="button"><i class="bi bi-arrow-left"></i></button>
              <img src="<?php echo htmlspecialchars($currentDoctorAvatar); ?>" class="avatar" alt="<?php echo htmlspecialchars($currentDoctorName); ?>" />
              <div>
                <div class="discussion-name">Dr. <?php echo htmlspecialchars($currentDoctorName); ?></div>
              </div>
            </div>
            <div class="discussion-messages" id="discussionMessages">
              <?php if (empty($messages)): ?>
                <div class="no-discussion-selected">Commencez une conversation avec Dr. <?php echo htmlspecialchars($currentDoctorName); ?></div>
              <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                  <div class="message <?php echo htmlspecialchars($msg['from_who']); ?>">
                    <div class="message-content"><?php echo htmlspecialchars($msg['message_text']); ?></div>
                    <div class="message-date"><?php echo htmlspecialchars((new DateTime($msg['sent_at']))->format('H:i - d/m/Y')); ?></div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
            <div class="message-input-area">
              <form method="POST" action="messagerie.php?doctor_id=<?php echo htmlspecialchars($currentDoctorId); ?>" style="display: flex; width: 100%;">
                <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($currentDoctorId); ?>">
                <input type="text" name="message_text" placeholder="Écrire un message..." required>
                <button type="submit" name="send_message"><i class="bi bi-send-fill"></i></button>
              </form>
            </div>
          <?php else: ?>
            <div class="no-discussion-selected">
                Sélectionnez une discussion pour commencer.
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const discussionsSection = document.getElementById('discussionsSection');
        const discussionFull = document.getElementById('discussionFull');
        const closeDiscussionBtn = document.getElementById('closeDiscussion');
        const discussionMessages = document.getElementById('discussionMessages');

        // Scroll to bottom of messages if discussion is open
        if (discussionFull.style.display === 'flex' && discussionMessages) {
          discussionMessages.scrollTop = discussionMessages.scrollHeight;
        }

        // Handle mobile view toggling
        if (window.innerWidth <= 768) {
          if (discussionFull.style.display === 'flex') {
            discussionsSection.classList.add('hidden-on-mobile');
          } else {
            discussionFull.classList.add('hidden-on-mobile');
          }
        }

        if (closeDiscussionBtn) {
            closeDiscussionBtn.addEventListener('click', function() {
                discussionFull.classList.add('hidden-on-mobile');
                discussionsSection.classList.remove('hidden-on-mobile');
                history.pushState(null, '', 'messagerie.php'); // Clean URL
            });
        }
      });
    </script>
  </body>
</html>