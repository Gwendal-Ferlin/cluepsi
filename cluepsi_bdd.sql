-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : jeu. 19 sep. 2024 à 10:23
-- Version du serveur : 5.7.39
-- Version de PHP : 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `cluepsi_bdd`
--

-- --------------------------------------------------------

--
-- Structure de la table `carte`
--

CREATE TABLE `carte` (
  `id_carte` int(11) NOT NULL,
  `nom_carte` varchar(50) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `carte`
--

INSERT INTO `carte` (`id_carte`, `nom_carte`, `role`) VALUES
(1, 'base de donnee', 'matiere'),
(2, 'PHP', 'matiere'),
(3, 'reseau', 'matiere'),
(4, 'marketing', 'matiere'),
(5, 'math', 'matiere'),
(6, 'HTML', 'matiere'),
(7, 'anglais', 'matiere'),
(8, 'nahide', 'prof'),
(9, 'Yakari', 'prof'),
(10, 'Maxout', 'prof'),
(11, 'Windouze', 'prof'),
(12, 'Pioupiou', 'prof'),
(13, 'Ami', 'prof'),
(14, 'le Galet', 'prof'),
(15, 'jaune', 'salle'),
(16, 'rouge', 'salle'),
(17, 'vert', 'salle'),
(18, 'bleu', 'salle'),
(19, 'grise', 'salle'),
(20, 'magenta', 'salle'),
(21, 'orange', 'salle');

-- --------------------------------------------------------

--
-- Structure de la table `carte_mystere`
--

CREATE TABLE `carte_mystere` (
  `id_partie` int(11) NOT NULL,
  `id_carte` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `carte_mystere`
--

INSERT INTO `carte_mystere` (`id_partie`, `id_carte`) VALUES
(14, 1),
(17, 1),
(20, 1),
(13, 2),
(11, 3),
(21, 3),
(22, 3),
(10, 4),
(15, 4),
(19, 4),
(9, 5),
(18, 5),
(8, 6),
(12, 6),
(16, 7),
(13, 8),
(15, 9),
(11, 10),
(12, 10),
(22, 10),
(8, 12),
(9, 12),
(16, 12),
(17, 12),
(10, 13),
(14, 13),
(18, 13),
(19, 13),
(20, 14),
(21, 14),
(8, 15),
(14, 16),
(15, 16),
(17, 16),
(21, 16),
(10, 18),
(18, 18),
(13, 19),
(19, 19),
(20, 19),
(9, 21),
(11, 21),
(12, 21),
(16, 21),
(22, 21);

-- --------------------------------------------------------

--
-- Structure de la table `inventaire`
--

CREATE TABLE `inventaire` (
  `id_joueur` int(11) NOT NULL,
  `id_carte` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `inventaire`
--

INSERT INTO `inventaire` (`id_joueur`, `id_carte`) VALUES
(16, 1),
(20, 1),
(24, 1),
(26, 1),
(29, 1),
(40, 1),
(41, 1),
(53, 1),
(55, 1),
(64, 1),
(71, 1),
(15, 2),
(19, 2),
(22, 2),
(27, 2),
(34, 2),
(40, 2),
(42, 2),
(48, 2),
(51, 2),
(58, 2),
(59, 2),
(66, 2),
(70, 2),
(15, 3),
(21, 3),
(25, 3),
(30, 3),
(37, 3),
(38, 3),
(41, 3),
(49, 3),
(54, 3),
(57, 3),
(60, 3),
(17, 4),
(22, 4),
(26, 4),
(32, 4),
(36, 4),
(42, 4),
(47, 4),
(54, 4),
(61, 4),
(64, 4),
(72, 4),
(19, 5),
(24, 5),
(27, 5),
(32, 5),
(35, 5),
(39, 5),
(45, 5),
(47, 5),
(56, 5),
(63, 5),
(66, 5),
(71, 5),
(15, 6),
(21, 6),
(22, 6),
(32, 6),
(36, 6),
(38, 6),
(45, 6),
(50, 6),
(54, 6),
(57, 6),
(62, 6),
(68, 6),
(72, 6),
(17, 7),
(20, 7),
(22, 7),
(26, 7),
(33, 7),
(37, 7),
(39, 7),
(48, 7),
(53, 7),
(58, 7),
(60, 7),
(64, 7),
(69, 7),
(18, 8),
(20, 8),
(23, 8),
(28, 8),
(35, 8),
(38, 8),
(41, 8),
(49, 8),
(52, 8),
(57, 8),
(63, 8),
(68, 8),
(70, 8),
(15, 9),
(20, 9),
(23, 9),
(26, 9),
(30, 9),
(36, 9),
(42, 9),
(50, 9),
(51, 9),
(58, 9),
(61, 9),
(65, 9),
(69, 9),
(18, 10),
(20, 10),
(29, 10),
(35, 10),
(40, 10),
(43, 10),
(46, 10),
(52, 10),
(55, 10),
(60, 10),
(65, 10),
(18, 11),
(20, 11),
(22, 11),
(25, 11),
(33, 11),
(36, 11),
(40, 11),
(44, 11),
(48, 11),
(52, 11),
(56, 11),
(61, 11),
(66, 11),
(70, 11),
(21, 12),
(23, 12),
(27, 12),
(29, 12),
(35, 12),
(39, 12),
(51, 12),
(56, 12),
(62, 12),
(68, 12),
(72, 12),
(16, 13),
(23, 13),
(27, 13),
(30, 13),
(38, 13),
(45, 13),
(46, 13),
(61, 13),
(65, 13),
(70, 13),
(15, 14),
(19, 14),
(24, 14),
(28, 14),
(31, 14),
(34, 14),
(39, 14),
(43, 14),
(49, 14),
(52, 14),
(56, 14),
(69, 14),
(18, 15),
(21, 15),
(22, 15),
(25, 15),
(33, 15),
(35, 15),
(38, 15),
(41, 15),
(47, 15),
(53, 15),
(55, 15),
(62, 15),
(67, 15),
(69, 15),
(17, 16),
(19, 16),
(24, 16),
(28, 16),
(29, 16),
(42, 16),
(51, 16),
(55, 16),
(63, 16),
(71, 16),
(16, 17),
(19, 17),
(24, 17),
(28, 17),
(31, 17),
(34, 17),
(39, 17),
(44, 17),
(46, 17),
(54, 17),
(57, 17),
(60, 17),
(67, 17),
(69, 17),
(16, 18),
(23, 18),
(25, 18),
(31, 18),
(34, 18),
(40, 18),
(44, 18),
(48, 18),
(56, 18),
(59, 18),
(67, 18),
(72, 18),
(17, 19),
(21, 19),
(24, 19),
(25, 19),
(37, 19),
(39, 19),
(43, 19),
(47, 19),
(51, 19),
(64, 19),
(71, 19),
(16, 20),
(21, 20),
(23, 20),
(26, 20),
(30, 20),
(37, 20),
(40, 20),
(43, 20),
(50, 20),
(52, 20),
(58, 20),
(59, 20),
(66, 20),
(70, 20),
(19, 21),
(31, 21),
(34, 21),
(38, 21),
(46, 21),
(53, 21),
(55, 21),
(59, 21),
(65, 21);

-- --------------------------------------------------------

--
-- Structure de la table `joueur`
--

CREATE TABLE `joueur` (
  `id_joueur` int(11) NOT NULL,
  `numéro_joueur` int(11) DEFAULT NULL,
  `id_partie` int(11) NOT NULL,
  `dans_piece` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `joueur`
--

INSERT INTO `joueur` (`id_joueur`, `numéro_joueur`, `id_partie`, `dans_piece`) VALUES
(1, 1, 1, 1),
(2, 1, 4, 1),
(3, 2, 4, 1),
(4, 3, 4, 0),
(5, 4, 4, 0),
(6, 1, 5, 0),
(7, 2, 5, 0),
(8, 3, 5, 0),
(9, 1, 6, 0),
(10, 2, 6, 0),
(11, 3, 6, 0),
(12, 1, 7, 0),
(13, 2, 7, 0),
(14, 3, 7, 0),
(15, 1, 9, 1),
(16, 2, 9, 0),
(17, 3, 9, 0),
(18, 4, 9, 0),
(19, 1, 10, 0),
(20, 2, 10, 0),
(21, 3, 10, 0),
(22, 1, 11, 0),
(23, 2, 11, 0),
(24, 3, 11, 0),
(25, 1, 12, 0),
(26, 2, 12, 0),
(27, 3, 12, 0),
(28, 4, 12, 0),
(29, 1, 13, 0),
(30, 2, 13, 0),
(31, 3, 13, 0),
(32, 4, 13, 0),
(33, 5, 13, 0),
(34, 1, 14, 0),
(35, 2, 14, 0),
(36, 3, 14, 0),
(37, 4, 14, 0),
(38, 1, 15, 1),
(39, 2, 15, 0),
(40, 3, 15, 0),
(41, 1, 16, 0),
(42, 2, 16, 0),
(43, 3, 16, 0),
(44, 4, 16, 0),
(45, 5, 16, 0),
(46, 1, 17, 0),
(47, 2, 17, 0),
(48, 3, 17, 0),
(49, 4, 17, 0),
(50, 5, 17, 0),
(51, 1, 18, 0),
(52, 2, 18, 0),
(53, 3, 18, 0),
(54, 4, 18, 0),
(55, 1, 19, 0),
(56, 2, 19, 0),
(57, 3, 19, 0),
(58, 4, 19, 0),
(59, 1, 20, 0),
(60, 2, 20, 0),
(61, 3, 20, 0),
(62, 4, 20, 0),
(63, 5, 20, 0),
(64, 1, 21, 0),
(65, 2, 21, 0),
(66, 3, 21, 0),
(67, 4, 21, 0),
(68, 5, 21, 0),
(69, 1, 22, 0),
(70, 2, 22, 0),
(71, 3, 22, 0),
(72, 4, 22, 0);

-- --------------------------------------------------------

--
-- Structure de la table `partie`
--

CREATE TABLE `partie` (
  `id_partie` int(11) NOT NULL,
  `nombre_joueur` int(11) DEFAULT NULL,
  `si_finit` int(11) NOT NULL,
  `mode` varchar(50) NOT NULL DEFAULT 'locale'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `partie`
--

INSERT INTO `partie` (`id_partie`, `nombre_joueur`, `si_finit`, `mode`) VALUES
(1, 3, 0, 'locale'),
(2, 4, 0, ''),
(3, 3, 0, ''),
(4, 4, 0, ''),
(5, 3, 0, ''),
(6, 3, 0, ''),
(7, 3, 0, ''),
(8, 4, 0, ''),
(9, 4, 0, ''),
(10, 3, 0, ''),
(11, 3, 0, ''),
(12, 4, 0, ''),
(13, 5, 0, ''),
(14, 4, 0, 'locale'),
(15, 3, 0, 'locale'),
(16, 5, 0, 'locale'),
(17, 5, 0, 'locale'),
(18, 4, 0, 'locale'),
(19, 4, 1, 'locale'),
(20, 5, 1, 'locale'),
(21, 5, 0, 'locale'),
(22, 4, 1, 'locale');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `carte`
--
ALTER TABLE `carte`
  ADD PRIMARY KEY (`id_carte`);

--
-- Index pour la table `carte_mystere`
--
ALTER TABLE `carte_mystere`
  ADD PRIMARY KEY (`id_partie`,`id_carte`),
  ADD KEY `id_carte` (`id_carte`);

--
-- Index pour la table `inventaire`
--
ALTER TABLE `inventaire`
  ADD PRIMARY KEY (`id_joueur`,`id_carte`),
  ADD KEY `id_carte` (`id_carte`);

--
-- Index pour la table `joueur`
--
ALTER TABLE `joueur`
  ADD PRIMARY KEY (`id_joueur`),
  ADD KEY `id_partie` (`id_partie`);

--
-- Index pour la table `partie`
--
ALTER TABLE `partie`
  ADD PRIMARY KEY (`id_partie`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `carte`
--
ALTER TABLE `carte`
  MODIFY `id_carte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `joueur`
--
ALTER TABLE `joueur`
  MODIFY `id_joueur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT pour la table `partie`
--
ALTER TABLE `partie`
  MODIFY `id_partie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `carte_mystere`
--
ALTER TABLE `carte_mystere`
  ADD CONSTRAINT `carte_mystere_ibfk_1` FOREIGN KEY (`id_partie`) REFERENCES `partie` (`id_partie`),
  ADD CONSTRAINT `carte_mystere_ibfk_2` FOREIGN KEY (`id_carte`) REFERENCES `carte` (`id_carte`);

--
-- Contraintes pour la table `inventaire`
--
ALTER TABLE `inventaire`
  ADD CONSTRAINT `inventaire_ibfk_1` FOREIGN KEY (`id_joueur`) REFERENCES `joueur` (`id_joueur`),
  ADD CONSTRAINT `inventaire_ibfk_2` FOREIGN KEY (`id_carte`) REFERENCES `carte` (`id_carte`);

--
-- Contraintes pour la table `joueur`
--
ALTER TABLE `joueur`
  ADD CONSTRAINT `joueur_ibfk_1` FOREIGN KEY (`id_partie`) REFERENCES `partie` (`id_partie`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
