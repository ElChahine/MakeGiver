-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 05 fév. 2026 à 19:58
-- Version du serveur : 8.0.30
-- Version de PHP : 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `makegiver`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `CategorieID` int NOT NULL,
  `Nom_Categorie` varchar(50) DEFAULT NULL,
  `Type_Categorie` varchar(50) DEFAULT NULL,
  `Description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`CategorieID`, `Nom_Categorie`, `Type_Categorie`, `Description`) VALUES
(1, 'Préhension', 'Handicap', 'Aides pour saisir des objets'),
(2, 'Impression 3D', 'Technique', 'Fabrication additive'),
(3, 'Mobilité', 'Handicap', 'Aides aux déplacements');

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

CREATE TABLE `commentaires` (
  `CommentaireID` int NOT NULL,
  `Contenu_Texte` text,
  `Date_Post` datetime DEFAULT CURRENT_TIMESTAMP,
  `Est_Valide` tinyint(1) DEFAULT '1',
  `AuteurID` int DEFAULT NULL,
  `SolutionID` int DEFAULT NULL,
  `ProjetID` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commentaires`
--

INSERT INTO `commentaires` (`CommentaireID`, `Contenu_Texte`, `Date_Post`, `Est_Valide`, `AuteurID`, `SolutionID`, `ProjetID`) VALUES
(1, 'Superbe solution, très facile à imprimer et très utile au quotidien !', '2026-02-05 20:54:42', 1, 3, 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `evenements`
--

CREATE TABLE `evenements` (
  `EvenementID` int NOT NULL,
  `Titre_Event` varchar(100) DEFAULT NULL,
  `Description` text,
  `Date_Debut` datetime DEFAULT NULL,
  `Date_Fin` datetime DEFAULT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `Lien_Externe_Organisateur` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `evenements`
--

INSERT INTO `evenements` (`EvenementID`, `Titre_Event`, `Description`, `Date_Debut`, `Date_Fin`, `Type`, `Lien_Externe_Organisateur`) VALUES
(1, 'Webinaire WeNov', 'Présentation des projets de co-conception.', '2026-02-12 14:00:00', '2026-02-12 16:00:00', 'Webinaire', 'https://zoom.us/j/wenov');

-- --------------------------------------------------------

--
-- Structure de la table `fichiers`
--

CREATE TABLE `fichiers` (
  `FichierID` int NOT NULL,
  `Nom_Fichier` varchar(100) DEFAULT NULL,
  `Chemin_URL` varchar(255) DEFAULT NULL,
  `Type` varchar(20) DEFAULT NULL,
  `Taille_Mo` decimal(10,2) DEFAULT NULL,
  `SolutionID` int DEFAULT NULL,
  `ProjetID` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `fichiers`
--

INSERT INTO `fichiers` (`FichierID`, `Nom_Fichier`, `Chemin_URL`, `Type`, `Taille_Mo`, `SolutionID`, `ProjetID`) VALUES
(1, 'besoin_manette.jpg', '/uploads/images/manette.jpg', 'JPG', 1.20, NULL, 1),
(2, 'plan_cle.stl', '/uploads/files/plan_cle.stl', 'STL', 4.50, 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `lieux`
--

CREATE TABLE `lieux` (
  `LieuID` int NOT NULL,
  `Nom_Lieu` varchar(100) DEFAULT NULL,
  `Adresse_Complete` varchar(255) DEFAULT NULL,
  `Code_Postal` varchar(10) DEFAULT NULL,
  `Ville` varchar(100) DEFAULT NULL,
  `Coordonnees_GPS` varchar(100) DEFAULT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `Liste_Equipements` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `lieux`
--

INSERT INTO `lieux` (`LieuID`, `Nom_Lieu`, `Adresse_Complete`, `Code_Postal`, `Ville`, `Coordonnees_GPS`, `Type`, `Liste_Equipements`) VALUES
(1, 'FabLab Lille', '12 Rue des Arts', '59000', 'Lille', NULL, 'FabLab', 'Imprimante 3D, Découpe Laser, CNC'),
(2, 'Orthopédie Durand', '45 Avenue Foch', '75016', 'Paris', NULL, 'Commerce', 'Scanner 3D, Atelier cuir');

-- --------------------------------------------------------

--
-- Structure de la table `membresprojet`
--

CREATE TABLE `membresprojet` (
  `ProjetID` int NOT NULL,
  `UtilisateurID` int NOT NULL,
  `RoleSurProjet` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `projets`
--

CREATE TABLE `projets` (
  `ProjetID` int NOT NULL,
  `Titre_Besoin` varchar(100) DEFAULT NULL,
  `Description_Detaillee` text,
  `Statut` enum('Ouvert','En cours','Terminé') DEFAULT 'Ouvert',
  `Date_Creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `Date_MiseAJour` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `DemandeurID` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `projets`
--

INSERT INTO `projets` (`ProjetID`, `Titre_Besoin`, `Description_Detaillee`, `Statut`, `Date_Creation`, `Date_MiseAJour`, `DemandeurID`) VALUES
(1, 'Adaptateur Manette PS5', 'Cherche un système pour utiliser la manette d une seule main.', 'En cours', '2026-02-05 20:54:41', NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `signalements`
--

CREATE TABLE `signalements` (
  `SignalementID` int NOT NULL,
  `UtilisateurID` int DEFAULT NULL,
  `TypeContenu` varchar(50) DEFAULT NULL,
  `ContenuID` int DEFAULT NULL,
  `Raison` text,
  `Date_Signalement` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `solutions`
--

CREATE TABLE `solutions` (
  `SolutionID` int NOT NULL,
  `Titre_Solution` varchar(100) DEFAULT NULL,
  `Description_Technique` text,
  `Licence` varchar(50) DEFAULT NULL,
  `Materiel_Necessaire` text,
  `Difficulte_Fabrication` varchar(50) DEFAULT NULL,
  `Date_Publication` datetime DEFAULT CURRENT_TIMESTAMP,
  `Est_CoupDeCoeur` tinyint(1) DEFAULT '0',
  `CreateurID` int DEFAULT NULL,
  `CoAuteurID` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `solutions`
--

INSERT INTO `solutions` (`SolutionID`, `Titre_Solution`, `Description_Technique`, `Licence`, `Materiel_Necessaire`, `Difficulte_Fabrication`, `Date_Publication`, `Est_CoupDeCoeur`, `CreateurID`, `CoAuteurID`) VALUES
(1, 'Adaptateur Clé Ergonomique', 'Extension de clé en PLA imprimée en 3D.', 'Creative Commons BY-NC', 'Filament PLA, Vis M3', 'Facile', '2026-02-05 20:54:42', 1, 2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `UtilisateurID` int NOT NULL,
  `Nom` varchar(50) DEFAULT NULL,
  `Prenom` varchar(50) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Telephone` varchar(20) DEFAULT NULL,
  `MotDePasse_Hash` varchar(255) DEFAULT NULL,
  `Role` enum('Patient','Maker','Soignant','Admin') DEFAULT NULL,
  `Bio_Description` text,
  `Competences_Techniques` text,
  `Consentement_Public` tinyint(1) DEFAULT '0',
  `Date_Inscription` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`UtilisateurID`, `Nom`, `Prenom`, `Email`, `Telephone`, `MotDePasse_Hash`, `Role`, `Bio_Description`, `Competences_Techniques`, `Consentement_Public`, `Date_Inscription`) VALUES
(1, 'Dupont', 'Jean', 'jean.dupont@email.com', '0601020304', NULL, 'Patient', 'Patient expert cherchant des aides pour la préhension.', NULL, 1, '2026-02-05 20:54:41'),
(2, 'Martin', 'Alice', 'alice.maker@email.com', '0708091011', NULL, 'Maker', 'Ingénieure passionnée par la modélisation 3D et le low-tech.', NULL, 1, '2026-02-05 20:54:41'),
(3, 'Leroy', 'Marc', 'marc.soignant@email.com', '0611223344', NULL, 'Soignant', 'Ergothérapeute au centre de rééducation de Lille.', NULL, 0, '2026-02-05 20:54:41');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`CategorieID`);

--
-- Index pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD PRIMARY KEY (`CommentaireID`),
  ADD KEY `AuteurID` (`AuteurID`),
  ADD KEY `SolutionID` (`SolutionID`),
  ADD KEY `ProjetID` (`ProjetID`);

--
-- Index pour la table `evenements`
--
ALTER TABLE `evenements`
  ADD PRIMARY KEY (`EvenementID`);

--
-- Index pour la table `fichiers`
--
ALTER TABLE `fichiers`
  ADD PRIMARY KEY (`FichierID`),
  ADD KEY `SolutionID` (`SolutionID`),
  ADD KEY `ProjetID` (`ProjetID`);

--
-- Index pour la table `lieux`
--
ALTER TABLE `lieux`
  ADD PRIMARY KEY (`LieuID`);

--
-- Index pour la table `membresprojet`
--
ALTER TABLE `membresprojet`
  ADD PRIMARY KEY (`ProjetID`,`UtilisateurID`),
  ADD KEY `UtilisateurID` (`UtilisateurID`);

--
-- Index pour la table `projets`
--
ALTER TABLE `projets`
  ADD PRIMARY KEY (`ProjetID`),
  ADD KEY `DemandeurID` (`DemandeurID`);

--
-- Index pour la table `signalements`
--
ALTER TABLE `signalements`
  ADD PRIMARY KEY (`SignalementID`),
  ADD KEY `UtilisateurID` (`UtilisateurID`);

--
-- Index pour la table `solutions`
--
ALTER TABLE `solutions`
  ADD PRIMARY KEY (`SolutionID`),
  ADD KEY `CreateurID` (`CreateurID`),
  ADD KEY `CoAuteurID` (`CoAuteurID`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`UtilisateurID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `CategorieID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `commentaires`
--
ALTER TABLE `commentaires`
  MODIFY `CommentaireID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `evenements`
--
ALTER TABLE `evenements`
  MODIFY `EvenementID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `fichiers`
--
ALTER TABLE `fichiers`
  MODIFY `FichierID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `lieux`
--
ALTER TABLE `lieux`
  MODIFY `LieuID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `projets`
--
ALTER TABLE `projets`
  MODIFY `ProjetID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `signalements`
--
ALTER TABLE `signalements`
  MODIFY `SignalementID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `solutions`
--
ALTER TABLE `solutions`
  MODIFY `SolutionID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `UtilisateurID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD CONSTRAINT `commentaires_ibfk_1` FOREIGN KEY (`AuteurID`) REFERENCES `utilisateurs` (`UtilisateurID`),
  ADD CONSTRAINT `commentaires_ibfk_2` FOREIGN KEY (`SolutionID`) REFERENCES `solutions` (`SolutionID`),
  ADD CONSTRAINT `commentaires_ibfk_3` FOREIGN KEY (`ProjetID`) REFERENCES `projets` (`ProjetID`);

--
-- Contraintes pour la table `fichiers`
--
ALTER TABLE `fichiers`
  ADD CONSTRAINT `fichiers_ibfk_1` FOREIGN KEY (`SolutionID`) REFERENCES `solutions` (`SolutionID`),
  ADD CONSTRAINT `fichiers_ibfk_2` FOREIGN KEY (`ProjetID`) REFERENCES `projets` (`ProjetID`);

--
-- Contraintes pour la table `membresprojet`
--
ALTER TABLE `membresprojet`
  ADD CONSTRAINT `membresprojet_ibfk_1` FOREIGN KEY (`ProjetID`) REFERENCES `projets` (`ProjetID`),
  ADD CONSTRAINT `membresprojet_ibfk_2` FOREIGN KEY (`UtilisateurID`) REFERENCES `utilisateurs` (`UtilisateurID`);

--
-- Contraintes pour la table `projets`
--
ALTER TABLE `projets`
  ADD CONSTRAINT `projets_ibfk_1` FOREIGN KEY (`DemandeurID`) REFERENCES `utilisateurs` (`UtilisateurID`);

--
-- Contraintes pour la table `signalements`
--
ALTER TABLE `signalements`
  ADD CONSTRAINT `signalements_ibfk_1` FOREIGN KEY (`UtilisateurID`) REFERENCES `utilisateurs` (`UtilisateurID`);

--
-- Contraintes pour la table `solutions`
--
ALTER TABLE `solutions`
  ADD CONSTRAINT `solutions_ibfk_1` FOREIGN KEY (`CreateurID`) REFERENCES `utilisateurs` (`UtilisateurID`),
  ADD CONSTRAINT `solutions_ibfk_2` FOREIGN KEY (`CoAuteurID`) REFERENCES `utilisateurs` (`UtilisateurID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
