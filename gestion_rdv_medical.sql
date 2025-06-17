

CREATE DATABASE IF NOT EXISTS gestion_rdv_medical;
USE gestion_rdv_medical;

--
-- Base de données : `gestion_rdv_medical`
--

-- --------------------------------------------------------

--
-- Structure de la table `administrateur`
--

CREATE TABLE `administrateur` (
  `ID_admin` int(11) NOT NULL,
  `Nom_admin` varchar(50) NOT NULL,
  `Prenom_admin` varchar(50) NOT NULL,
  `Email_admin` varchar(100) NOT NULL,
  `Numtel_admin` varchar(20) DEFAULT NULL,
  `Mot_de_passep` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `changer_l_etat`
--

CREATE TABLE `changer_l_etat` (
  `ID_changement` int(11) NOT NULL,
  `ID_rendez_vous` int(11) NOT NULL,
  `ID_admin` int(11) DEFAULT NULL,
  `Nouvel_etat` enum('confirmé','annulé','en attente','terminé','urgent') NOT NULL,
  `Date_changement` datetime DEFAULT current_timestamp(),
  `Raison` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dossier`
--

CREATE TABLE `dossier` (
  `ID_dossier` int(11) NOT NULL,
  `ID_patient` int(11) NOT NULL,
  `Historique_rdv` text DEFAULT NULL,
  `Ordonnances` text DEFAULT NULL,
  `Observations` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `dossier`
--

INSERT INTO `dossier` (`ID_dossier`, `ID_patient`, `Historique_rdv`, `Ordonnances`, `Observations`) VALUES
(1, 1, 'Consultation cardiologie 2023-05-10', 'Médicament X 1 comprimé par jour', 'A surveiller tension artérielle'),
(2, 2, 'Consultation pédiatrie 2023-06-15', 'Vitamine D 800UI/jour', 'Croissance normale');

-- --------------------------------------------------------

--
-- Structure de la table `medecin`
--

CREATE TABLE `medecin` (
  `ID_medecin` int(11) NOT NULL,
  `Nom_med` varchar(50) NOT NULL,
  `Prenom_med` varchar(50) NOT NULL,
  `Specialite` varchar(100) NOT NULL,
  `email_med` varchar(100) NOT NULL,
  `Numtel_med` varchar(20) DEFAULT NULL,
  `Mot_de_passe` varchar(255) NOT NULL,
  `Tarif` decimal(10,2) DEFAULT NULL,
  `Horaires_disponible` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `medecin`
--

INSERT INTO `medecin` (`ID_medecin`, `Nom_med`, `Prenom_med`, `Specialite`, `email_med`, `Numtel_med`, `Mot_de_passe`, `Tarif`, `Horaires_disponible`) VALUES
(1, 'Dupont', 'Jean', 'Cardiologie', 'j.dupont@cabinet.com', '0612345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 70.00, 'Lundi: 8h-12h, 14h-18h; Mardi: 8h-12h; Mercredi: 9h-17h'),
(2, 'Martin', 'Sophie', 'Pédiatrie', 's.martin@cabinet.com', '0698765432', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 60.00, 'Lundi: 9h-12h; Mardi: 8h-18h; Jeudi: 10h-17h'),
(3, 'ASSEID', 'SIDDICK', 'docteur', 'minasiddickadam@gmail.com', '+212 644248703', '$2y$10$4s8sAy7w3sti4TW/xPwmf.Wo1xeqm5Jcg1ZQkiQciTSWnJJMb/05i', 40000.00, ''),
(6, 'ASSEID ', 'SIDDICK', 'cardio', 'adam.asseidsiddick.ensao@ump.ac.ma', '+212 644248703', '$2y$10$Rm0W5lXrXQZSpQolcBupu.2axXDe4BDZsA9ypi2HYgM5Uk90PJcNe', 40000.00, '');

-- --------------------------------------------------------

--
-- Structure de la table `modifier`
--

CREATE TABLE `modifier` (
  `ID_modification` int(11) NOT NULL,
  `ID_admin` int(11) DEFAULT NULL,
  `ID_dossier` int(11) DEFAULT NULL,
  `ID_medecin` int(11) DEFAULT NULL,
  `Date_modification` datetime DEFAULT current_timestamp(),
  `Description_modification` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `parcourir`
--

CREATE TABLE `parcourir` (
  `ID_parcours` int(11) NOT NULL,
  `ID_medecin` int(11) NOT NULL,
  `ID_dossier` int(11) NOT NULL,
  `ID_admin` int(11) DEFAULT NULL,
  `Date_acces` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `patient`
--

CREATE TABLE `patient` (
  `ID_patient` int(11) NOT NULL,
  `Nom_patient` varchar(50) NOT NULL,
  `Prenom_patient` varchar(50) NOT NULL,
  `Date_naiss` date DEFAULT NULL,
  `email_patient` varchar(100) NOT NULL,
  `Numtel` varchar(20) DEFAULT NULL,
  `Mot_de_passep` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `patient`
--

INSERT INTO `patient` (`ID_patient`, `Nom_patient`, `Prenom_patient`, `Date_naiss`, `email_patient`, `Numtel`, `Mot_de_passep`) VALUES
(1, 'Durand', 'Pierre', '1980-05-15', 'p.durand@mail.com', '0712345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(2, 'Leclerc', 'Marie', '1992-11-22', 'm.leclerc@mail.com', '0798765432', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(3, 'ASSEID', 'SIDDICK', '1990-01-01', 'adam.asseidsiddick.ensao@ump.ac.ma', '+212 644248703', '$2y$10$V.LVYZRMtXuDDlTZcZlT9uYooEBRbShCyJi8M779B9kBXThJyvxWO');

-- --------------------------------------------------------

--
-- Structure de la table `rendez_vous`
--

CREATE TABLE `rendez_vous` (
  `ID_rendez_vous` int(11) NOT NULL,
  `Date_RDV` date NOT NULL,
  `Heure` time NOT NULL,
  `Statut` enum('confirmé','annulé','en attente','terminé','urgent') DEFAULT 'en attente',
  `Specialite` varchar(100) NOT NULL,
  `ID_patient` int(11) NOT NULL,
  `ID_medecin` int(11) NOT NULL,
  `Motif` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `rendez_vous`
--

INSERT INTO `rendez_vous` (`ID_rendez_vous`, `Date_RDV`, `Heure`, `Statut`, `Specialite`, `ID_patient`, `ID_medecin`, `Motif`) VALUES
(1, '2023-07-10', '09:00:00', 'confirmé', 'Cardiologie', 1, 1, 'Consultation de suivi'),
(2, '2023-07-12', '11:30:00', 'en attente', 'Pédiatrie', 2, 2, 'Vaccination'),
(3, '2023-07-15', '14:00:00', 'urgent', 'Cardiologie', 1, 1, 'Douleurs thoraciques');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `administrateur`
--
ALTER TABLE `administrateur`
  ADD PRIMARY KEY (`ID_admin`),
  ADD UNIQUE KEY `Email_admin` (`Email_admin`);

--
-- Index pour la table `changer_l_etat`
--
ALTER TABLE `changer_l_etat`
  ADD PRIMARY KEY (`ID_changement`),
  ADD KEY `ID_rendez_vous` (`ID_rendez_vous`),
  ADD KEY `ID_admin` (`ID_admin`);

--
-- Index pour la table `dossier`
--
ALTER TABLE `dossier`
  ADD PRIMARY KEY (`ID_dossier`),
  ADD KEY `ID_patient` (`ID_patient`);

--
-- Index pour la table `medecin`
--
ALTER TABLE `medecin`
  ADD PRIMARY KEY (`ID_medecin`),
  ADD UNIQUE KEY `email_med` (`email_med`);

--
-- Index pour la table `modifier`
--
ALTER TABLE `modifier`
  ADD PRIMARY KEY (`ID_modification`),
  ADD KEY `ID_admin` (`ID_admin`),
  ADD KEY `ID_dossier` (`ID_dossier`),
  ADD KEY `ID_medecin` (`ID_medecin`);

--
-- Index pour la table `parcourir`
--
ALTER TABLE `parcourir`
  ADD PRIMARY KEY (`ID_parcours`),
  ADD KEY `ID_medecin` (`ID_medecin`),
  ADD KEY `ID_dossier` (`ID_dossier`),
  ADD KEY `ID_admin` (`ID_admin`);

--
-- Index pour la table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`ID_patient`),
  ADD UNIQUE KEY `email_patient` (`email_patient`);

--
-- Index pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD PRIMARY KEY (`ID_rendez_vous`),
  ADD KEY `ID_patient` (`ID_patient`),
  ADD KEY `ID_medecin` (`ID_medecin`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `administrateur`
--
ALTER TABLE `administrateur`
  MODIFY `ID_admin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `changer_l_etat`
--
ALTER TABLE `changer_l_etat`
  MODIFY `ID_changement` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `dossier`
--
ALTER TABLE `dossier`
  MODIFY `ID_dossier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `medecin`
--
ALTER TABLE `medecin`
  MODIFY `ID_medecin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `modifier`
--
ALTER TABLE `modifier`
  MODIFY `ID_modification` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `parcourir`
--
ALTER TABLE `parcourir`
  MODIFY `ID_parcours` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `patient`
--
ALTER TABLE `patient`
  MODIFY `ID_patient` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  MODIFY `ID_rendez_vous` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `changer_l_etat`
--
ALTER TABLE `changer_l_etat`
  ADD CONSTRAINT `changer_l_etat_ibfk_1` FOREIGN KEY (`ID_rendez_vous`) REFERENCES `rendez_vous` (`ID_rendez_vous`),
  ADD CONSTRAINT `changer_l_etat_ibfk_2` FOREIGN KEY (`ID_admin`) REFERENCES `administrateur` (`ID_admin`);

--
-- Contraintes pour la table `dossier`
--
ALTER TABLE `dossier`
  ADD CONSTRAINT `dossier_ibfk_1` FOREIGN KEY (`ID_patient`) REFERENCES `patient` (`ID_patient`);

--
-- Contraintes pour la table `modifier`
--
ALTER TABLE `modifier`
  ADD CONSTRAINT `modifier_ibfk_1` FOREIGN KEY (`ID_admin`) REFERENCES `administrateur` (`ID_admin`),
  ADD CONSTRAINT `modifier_ibfk_2` FOREIGN KEY (`ID_dossier`) REFERENCES `dossier` (`ID_dossier`),
  ADD CONSTRAINT `modifier_ibfk_3` FOREIGN KEY (`ID_medecin`) REFERENCES `medecin` (`ID_medecin`);

--
-- Contraintes pour la table `parcourir`
--
ALTER TABLE `parcourir`
  ADD CONSTRAINT `parcourir_ibfk_1` FOREIGN KEY (`ID_medecin`) REFERENCES `medecin` (`ID_medecin`),
  ADD CONSTRAINT `parcourir_ibfk_2` FOREIGN KEY (`ID_dossier`) REFERENCES `dossier` (`ID_dossier`),
  ADD CONSTRAINT `parcourir_ibfk_3` FOREIGN KEY (`ID_admin`) REFERENCES `administrateur` (`ID_admin`);

--
-- Contraintes pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD CONSTRAINT `rendez_vous_ibfk_1` FOREIGN KEY (`ID_patient`) REFERENCES `patient` (`ID_patient`),
  ADD CONSTRAINT `rendez_vous_ibfk_2` FOREIGN KEY (`ID_medecin`) REFERENCES `medecin` (`ID_medecin`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
