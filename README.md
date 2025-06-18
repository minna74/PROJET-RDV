ShafadMedCare - Système de Gestion de Rendez-vous Médicaux
Table des matières
Introduction
Fonctionnalités
Espace Patient
Espace Médecin (en développement / à venir)
Espace Administrateur (en développement / à venir)
Technologies Utilisées
Structure du Projet
Installation et Démarrage
Prérequis
Base de Données
Configuration
Lancement
Utilisation
Contribuer
Licence
Contact
1. Introduction
ShafadMedCare est une application web innovante conçue pour moderniser la gestion des rendez-vous au sein d'un cabinet médical. Elle vise à offrir une expérience fluide aux patients pour la prise et le suivi de leurs rendez-vous, tout en préparant des espaces dédiés aux médecins et aux administrateurs pour une gestion optimisée de leur pratique et du système.

Ce projet a pour objectif de dématérialiser les processus administratifs, de centraliser les informations cruciales (rendez-vous, dossiers médicaux, communications) et de renforcer l'efficacité des interactions entre les patients, les professionnels de santé et le personnel administratif.

2. Fonctionnalités
2.1. Espace Patient
Inscription & Connexion :
Page de connexion intuitive (page de login.html).
Processus d'inscription patient sécurisé et multi-étapes (inscription.html pour le front-end, inscription.php pour le traitement back-end).
Gestion de la réinitialisation de mot de passe (mot de passe oublier.html).
Tableau de Bord :
Page d'accueil (proto.html) offrant une recherche de médecins et un aperçu rapide.
Gestion des Rendez-vous :
Consultation et suivi des rendez-vous à venir et passés (rendez vous.html pour la maquette statique, mes_rendez_vous.php pour la version dynamique).
Prise de nouveaux rendez-vous via une interface de recherche de médecins et de créneaux disponibles (Page de prise de rendez vous.html).
Fonctionnalités de modification (modifier_rdv.php) et d'annulation (annuler_rdv.php) pour les rendez-vous à venir.
Profil Patient :
Consultation et mise à jour des informations personnelles du patient (profil.html).
Accès aux Documents Médicaux :
Visualisation et téléchargement des ordonnances (ordonance.html).
Visualisation et téléchargement des résultats d'analyses et examens (documents.html).
Messagerie :
Interface de communication simplifiée avec les médecins (messagerie.html).
Médecins Consultés :
Liste des professionnels de santé avec lesquels le patient a déjà eu des rendez-vous (Mes medecins.html).
2.2. Espace Médecin (en développement / à venir)
Cette section est en cours de conception et sera ajoutée dans les futures phases du projet. Les fonctionnalités envisagées incluent :

Connexion sécurisée et tableau de bord personnalisé.
Gestion de l'emploi du temps et des créneaux de consultation.
Accès aux dossiers médicaux des patients.
Envoi d'ordonnances et de documents aux patients.
Système de messagerie bidirectionnel avec les patients.
2.3. Espace Administrateur (en développement / à venir)
Cet espace est destiné à offrir des outils de supervision et de gestion globale du système. Les fonctionnalités prévues incluent :

Connexion sécurisée pour les administrateurs.
Gestion des utilisateurs : création, modification, suppression des comptes patients, médecins et autres administrateurs.
Supervision des rendez-vous : consultation, modification ou annulation de tout rendez-vous.
Gestion des médecins : ajout de nouveaux médecins, mise à jour de leurs spécialités, tarifs et horaires.
Rapports et statistiques sur l'activité du cabinet (nombre de rendez-vous, patients actifs, etc.).
Gestion des paramètres globaux du système.
3. Technologies Utilisées
Frontend :

HTML5 : Structure des pages web.
CSS3 : Mise en forme et styles personnalisés (style de page de login.css, styles intégrés dans les autres HTML).
Bootstrap 5.3.3 : Framework CSS pour un design réactif et moderne.
Bootstrap Icons 1.11.3 : Bibliothèque d'icônes.
JavaScript : Interactivité côté client (validation des formulaires, recherche dynamique, transitions multi-étapes pour l'inscription, gestion des modales, etc.).
Backend :

PHP : Langage de script côté serveur pour la logique métier, la gestion des sessions, l'interaction avec la base de données (ex: inscription.php, db_connect.php, mes_rendez_vous.php, modifier_rdv.php, annuler_rdv.php).
Base de Données :

MySQL / MariaDB : Système de gestion de base de données relationnelle (gestion_rdv_medical). Le schéma et les données initiales sont fournis dans bdd.txt.
Serveur Web :

Apache / Nginx : Serveur HTTP (généralement via WAMP/XAMPP/MAMP pour le développement local).
4. Structure du Projet espace patient
.
├── css/
│   └── style de page de login.css   # Styles spécifiques à la page de connexion/inscription
├── assets/                          # Dossier pour les images, avatars, etc.
│   └── ...
├── db_connect.php                   # Script PHP de connexion à la base de données
├── proto.html                       # Tableau de bord / Page d'accueil du patient après connexion
├── page de login.html               # Page de connexion
├── inscription.html                 # Formulaire d'inscription patient (front-end multi-étapes)
├── inscription.php                  # Traitement PHP de l'inscription patient (inclut HTML/CSS)
├── Page de prise de rendez-vous.html # Formulaire de prise de rendez-vous (recherche médecin/créneau)
├── mes_rendez_vous.php              # Affichage dynamique des rendez-vous du patient
├── modifier_rdv.php                 # Formulaire et script de modification d'un rendez-vous
├── annuler_rdv.php                  # Script d'annulation d'un rendez-vous
├── ordonance.html                   # Page d'affichage des ordonnances
├── documents.html                   # Page d'affichage des résultats médicaux
├── Mes medecins.html                # Liste des médecins consultés par le patient
├── messagerie.html                  # Interface de messagerie patient
├── profil.html                      # Page de gestion du profil patient
├── mot de passe oublier.html        # Page de réinitialisation de mot de passe
└── README.md                        # Ce fichier

Note : Plusieurs fichiers HTML sont actuellement des maquettes statiques avec du JavaScript pour simuler le comportement. La logique PHP et l'interaction complète avec la base de données sont en cours d'intégration pour les rendre dynamiques.

5. Installation et Démarrage
Suivez ces instructions pour installer et exécuter ShafadMedCare sur votre environnement de développement local.

5.1. Prérequis
Serveur Web avec PHP : Un environnement comme XAMPP, WAMP, MAMP est fortement recommandé pour un démarrage rapide sur Windows, macOS ou Linux. Assurez-vous que PHP et Apache/Nginx sont opérationnels.
Base de données MySQL/MariaDB : Fournie par XAMPP/WAMP/MAMP.
Git : Pour cloner le dépôt. (Voir Comment cloner avec GitHub si vous rencontrez des problèmes).
Un navigateur web moderne.
5.2. Base de Données
Créez la base de données :

Accédez à votre outil de gestion de base de données (par exemple, phpMyAdmin, accessible via http://localhost/phpmyadmin/ si vous utilisez XAMPP/WAMP).
Créez une nouvelle base de données nommée gestion_rdv_medical.
Importez le schéma et les données initiales :

Ouvrez le fichier bdd.txt situé à la racine du projet.
Copiez l'intégralité du contenu de ce fichier.
Dans phpMyAdmin, sélectionnez la base de données gestion_rdv_medical que vous venez de créer.
Allez dans l'onglet "SQL" (ou "Import" pour les fichiers .sql) et collez le contenu copié.
Exécutez la requête. Cela créera les tables nécessaires (administrateur, medecin, patient, rendez_vous) et insérera des données de test (y compris pour l'administrateur, si présent dans bdd.txt).
5.3. Configuration
Clonez le projet :

Ouvrez votre terminal (Git Bash, CMD, PowerShell).
Naviguez jusqu'au répertoire htdocs de votre installation XAMPP (par exemple, cd C:\xampp\htdocs\).
Clonez le dépôt GitHub :
Bash

git clone https://github.com/votre_nom_utilisateur/ShafadMedCare.git # Remplacez par l'URL réelle du dépôt
Un nouveau dossier ShafadMedCare sera créé contenant tous les fichiers du projet.
Configurez la connexion à la base de données :

Ouvrez le fichier db_connect.php (situé à la racine du dossier ShafadMedCare).
Assurez-vous que les informations de connexion ($servername, $username, $password, $dbname) correspondent à votre configuration MySQL/MariaDB locale. Pour les installations par défaut de XAMPP/WAMP, cela ressemble souvent à ceci :
PHP

<?php
$servername = "localhost";
$username = "root";
$password = ""; // Mot de passe vide par défaut pour root sur XAMPP/WAMP
$dbname = "gestion_rdv_medical";

// ... le reste du code ...
?>
5.4. Lancement
Démarrez les services Apache (ou Nginx) et MySQL via le panneau de contrôle de votre XAMPP/WAMP.
Ouvrez votre navigateur web.
Naviguez vers l'URL du projet :
http://localhost/ShafadMedCare/page%20de%20login.html pour la page de connexion.
Ou, http://localhost/ShafadMedCare/tu%20est%20passion%20ou%20doctor.html pour choisir le type d'utilisateur et s'inscrire (patients).
Prévisionnel pour l'admin : une URL spécifique comme http://localhost/ShafadMedCare/admin/login.php .
6. Utilisation
Inscription : Cliquez sur "INSCRIPTION" depuis la page de connexion, puis suivez les étapes.
Connexion : Utilisez les identifiants d'un patient créé via l'inscription ou ceux disponibles dans bdd.txt pour les tests.
Navigation Patient :
Accueil (proto.html) : Permet de rechercher des médecins et d'accéder aux fonctionnalités principales.
Mes Rendez-vous (mes_rendez_vous.php) : Visualisez, modifiez ou annulez vos rendez-vous.
Prendre un rendez-vous (Page de prise de rendez-vous.html) : Sélectionnez un médecin et un créneau.
Mon Profil (profil.html) : Mettez à jour vos informations personnelles.
Mes Ordonnances (ordonance.html) & Mes résultats (documents.html) : Accédez à vos documents médicaux.
Mes Médecins (Mes medecins.html) : Retrouvez les informations de vos médecins.
Messagerie (messagerie.html) : Communiquez avec vos médecins.
Accès Administrateur (à venir) : Une fois développé, un accès dédié permettra la gestion du système.
7. Contribuer
Les contributions sont grandement appréciées pour améliorer et étendre ce projet ! Si vous souhaitez apporter votre aide :

"Fork" (cloner) ce dépôt sur votre compte GitHub.
Créez une nouvelle branche pour votre fonctionnalité ou correction de bug (git checkout -b feature/nom-de-ma-fonctionnalite).
Effectuez vos modifications et testez-les.
Commitez vos changements avec un message clair (git commit -m 'feat: Ajout de la fonctionnalité X').
Poussez votre branche sur votre dépôt forké (git push origin feature/nom-de-ma-fonctionnalite).
Ouvrez une "Pull Request" (demande de tirage) sur le dépôt original, en expliquant vos modifications.
8. Licence
Ce projet est distribué sous la licence MIT License. Voir le fichier LICENSE (si vous l'ajoutez) ou le lien pour plus de détails.

9. Contact
Pour toute question, suggestion ou problème, veuillez ouvrir une "issue" sur le dépôt GitHub du projet.
