-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 22 nov. 2024 à 09:38
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ultimatemanagerbdd`
--

-- --------------------------------------------------------

--
-- Structure de la table `joueur`
--

DROP TABLE IF EXISTS `joueur`;
CREATE TABLE IF NOT EXISTS `joueur` (
  `Id_joueur` int NOT NULL AUTO_INCREMENT,
  `Numéro_de_licence` char(10) NOT NULL,
  `Nom` varchar(50) NOT NULL,
  `Prénom` varchar(50) NOT NULL,
  `Date_de_naissance` date NOT NULL,
  `Taille` double NOT NULL,
  `Poid` double NOT NULL,
  `Commentaire` text,
  `Statut` varchar(10) NOT NULL,
  PRIMARY KEY (`Id_joueur`),
  UNIQUE KEY `Numéro_de_licence` (`Numéro_de_licence`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `joueur`
--

INSERT INTO `joueur` (`Id_joueur`, `Numéro_de_licence`, `Nom`, `Prénom`, `Date_de_naissance`, `Taille`, `Poid`, `Commentaire`, `Statut`) VALUES
(1, 'LIC1234567', 'Dupont', 'Pierre', '1990-05-12', 1.8, 75, 'Capitaine de l\'équipe', 'Actif'),
(2, 'LIC2345678', 'Martin', 'Jean', '1992-07-23', 1.75, 68, NULL, 'Blessé'),
(3, 'LIC3456789', 'Lemoine', 'Sophie', '1995-09-10', 1.65, 55, 'Joueuse rapide', 'Suspendu'),
(4, 'LIC4567890', 'Bernard', 'Luc', '1988-01-15', 1.85, 80, NULL, 'Actif'),
(5, 'LIC5678901', 'Petit', 'Marie', '1993-11-02', 1.7, 60, 'Bonne défenseuse', 'Blessé'),
(6, 'LIC6789012', 'Durand', 'Nicolas', '1991-03-30', 1.78, 72, NULL, 'Suspendu'),
(7, 'LIC7890123', 'Moreau', 'Alice', '1994-06-18', 1.66, 54, 'Excellente passeuse', 'Actif'),
(8, 'LIC8901234', 'Fournier', 'Paul', '1989-12-20', 1.82, 77, 'Très bonne vision du jeu', 'Blessé'),
(9, 'LIC9012345', 'Blanc', 'Julie', '1996-08-25', 1.68, 58, 'Débute en attaque', 'Actif'),
(10, 'LIC0123456', 'Renard', 'Thomas', '1990-02-14', 1.74, 70, NULL, 'Suspendu'),
(11, 'LIC1122334', 'Leroy', 'Maxime', '1991-04-08', 1.79, 73, 'Stratège défensif', 'Actif'),
(12, 'LIC2233445', 'Garnier', 'Laura', '1993-10-29', 1.67, 56, 'Attaquante puissante', 'Suspendu'),
(13, 'LIC3344556', 'Rousseau', 'Antoine', '1987-12-17', 1.83, 78, 'Leader sur le terrain', 'Absent'),
(14, 'LIC4455667', 'Mercier', 'Camille', '1992-09-01', 1.64, 57, 'Très rapide', 'Actif'),
(15, 'LIC5566778', 'Perrot', 'Damien', '1990-05-15', 1.9, 85, 'Excellent en passes', 'Actif'),
(16, 'LIC6677889', 'Lefevre', 'Chloé', '1995-06-11', 1.62, 52, 'Grande agilité', 'Actif'),
(17, 'LIC7788990', 'Giraud', 'Matthieu', '1989-03-20', 1.76, 71, 'Précis dans les tirs', 'Absent'),
(18, 'LIC8899001', 'Renault', 'Lucie', '1996-02-07', 1.69, 59, 'Endurante', 'Actif'),
(19, 'LIC9900112', 'Picard', 'Julien', '1994-07-30', 1.8, 76, 'Bon gestionnaire du jeu', 'Actif'),
(20, 'LIC1011123', 'Benoit', 'Sabrina', '1993-11-15', 1.73, 60, 'Capacité d\'anticipation', 'Actif');

-- --------------------------------------------------------

--
-- Structure de la table `participer`
--

DROP TABLE IF EXISTS `participer`;
CREATE TABLE IF NOT EXISTS `participer` (
  `Id_joueur` int NOT NULL,
  `Id_Match` int NOT NULL,
  `Note` tinyint DEFAULT NULL,
  `T_R` char(1) NOT NULL,
  `Poste` varchar(15) NOT NULL,
  PRIMARY KEY (`Id_joueur`,`Id_Match`),
  KEY `Id_Match` (`Id_Match`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `participer`
--

INSERT INTO `participer` (`Id_joueur`, `Id_Match`, `Note`, `T_R`, `Poste`) VALUES
(1, 3, NULL, 'T', 'Handler'),
(7, 3, NULL, 'T', 'Cutter'),
(9, 3, NULL, 'T', 'Handler'),
(11, 3, NULL, 'T', 'Cutter'),
(14, 3, NULL, 'T', 'Handler'),
(15, 3, NULL, 'T', 'Cutter'),
(18, 3, NULL, 'T', 'Handler'),
(19, 3, NULL, 'R', 'Cutter'),
(1, 4, NULL, 'T', 'Handler'),
(7, 4, NULL, 'T', 'Cutter'),
(9, 4, NULL, 'T', 'Handler'),
(11, 4, NULL, 'T', 'Cutter'),
(14, 4, NULL, 'T', 'Handler'),
(15, 4, NULL, 'T', 'Cutter'),
(18, 4, NULL, 'T', 'Handler'),
(19, 4, NULL, 'R', 'Cutter');

-- --------------------------------------------------------

--
-- Structure de la table `rencontre`
--

DROP TABLE IF EXISTS `rencontre`;
CREATE TABLE IF NOT EXISTS `rencontre` (
  `Id_Match` int NOT NULL AUTO_INCREMENT,
  `Date_Heure` datetime NOT NULL,
  `Nom_adversaire` varchar(50) NOT NULL,
  `Lieu` varchar(9) NOT NULL,
  `Résultat` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`Id_Match`),
  UNIQUE KEY `Date_Heure` (`Date_Heure`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `rencontre`
--

INSERT INTO `rencontre` (`Id_Match`, `Date_Heure`, `Nom_adversaire`, `Lieu`, `Résultat`) VALUES
(3, '2024-11-15 15:00:00', 'Olympique Sud', 'domicile', NULL),
(4, '2024-11-22 18:30:00', 'Racing Club de l\'Ouest', 'extérieur', NULL),
(5, '2024-11-29 14:00:00', 'Union Sportive des Marais', 'domicile', NULL),
(6, '2024-12-05 17:00:00', 'AS Ville Haute', 'extérieur', NULL),
(7, '2024-12-12 16:00:00', 'Stade Maritime', 'domicile', NULL),
(8, '2024-12-20 18:00:00', 'Club Athlétique Central', 'extérieur', NULL),
(9, '2025-01-03 19:30:00', 'Étoile Bleue', 'domicile', NULL),
(10, '2025-01-10 15:30:00', 'Sporting Club de la Côte', 'extérieur', NULL),
(11, '2025-01-17 16:30:00', 'Amicale Sportive du Lac', 'domicile', NULL),
(12, '2025-01-24 14:30:00', 'Association Sportive de la Rivière', 'extérieur', NULL),
(13, '2024-10-05 15:00:00', 'Les Aigles de la Montagne', 'domicile', NULL),
(14, '2024-10-12 18:30:00', 'Tigres de la Vallée', 'extérieur', NULL),
(15, '2024-10-19 14:00:00', 'Olympique des Plaines', 'domicile', NULL),
(16, '2024-10-26 17:00:00', 'Dragons Rouges', 'extérieur', NULL),
(17, '2024-11-02 16:00:00', 'Lions Maritimes', 'domicile', NULL),
(18, '2024-11-09 18:00:00', 'Panthères Noires', 'extérieur', NULL),
(19, '2024-11-16 19:30:00', 'Requins du Sud', 'domicile', NULL),
(20, '2024-11-23 15:30:00', 'Équipe des Braves', 'extérieur', NULL),
(21, '2024-11-30 16:30:00', 'Faucons de l\'Ouest', 'domicile', NULL),
(22, '2024-12-07 14:30:00', 'Chevaliers de la Forêt', 'extérieur', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `login` varchar(50) NOT NULL,
  `mdp` varchar(50) NOT NULL,
  PRIMARY KEY (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
