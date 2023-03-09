-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 09 mars 2023 à 19:01
-- Version du serveur : 8.0.27
-- Version de PHP : 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `chatbox_alpinejs`
--

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `MessageId` int NOT NULL AUTO_INCREMENT,
  `MessageExpediteur` int NOT NULL,
  `MessageDestinataire` int NOT NULL,
  `MessageContent` varchar(2056) COLLATE utf8_unicode_ci NOT NULL,
  `MessageDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `MessageLu` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`MessageId`),
  KEY `MessageExpediteur` (`MessageExpediteur`),
  KEY `MessageDestinataire` (`MessageDestinataire`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`MessageId`, `MessageExpediteur`, `MessageDestinataire`, `MessageContent`, `MessageDate`, `MessageLu`) VALUES
(1, 2, 1, 'coucou2', '2023-03-09 10:01:33', 1),
(2, 1, 2, 'coucou', '2023-03-07 10:01:33', 0),
(3, 1, 2, 'coucou3', '2023-03-07 10:01:33', 0),
(4, 1, 3, 'coucou', '2023-03-09 15:54:58', 0),
(5, 1, 3, 'cava ?\n', '2023-03-09 15:55:10', 0),
(26, 1, 3, 'test', '2023-03-09 16:19:25', 0),
(30, 1, 3, 'cava ? ', '2023-03-09 16:23:06', 0),
(31, 1, 2, 'test', '2023-03-09 17:56:14', 0),
(32, 2, 1, 'coucou2', '2023-03-09 19:01:33', 1),
(33, 3, 1, 'coucou2', '2023-03-09 19:01:33', 1),
(34, 1, 3, 'hello', '2023-03-09 19:47:53', 0),
(35, 1, 2, 'comment tu va ?', '2023-03-09 19:48:17', 0);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `UserId` int NOT NULL AUTO_INCREMENT,
  `UserLogin` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `UserPassword` varchar(2056) COLLATE utf8_unicode_ci NOT NULL,
  `UserNom` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `UserPrenom` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `UserImage` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `UserStatut` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`UserId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`UserId`, `UserLogin`, `UserPassword`, `UserNom`, `UserPrenom`, `UserImage`, `UserStatut`) VALUES
(1, 'arthurmdn', '$2y$10$zBLKVlE7HdwuLEj20ATdMer1CFqmvO.ZF39qAMuI4GDp2BBrfr0gC', 'mondon', 'arthur', 'public/elements/profile_pics/pic.png', 0),
(2, 'arthurmdn2', '$2y$10$zBLKVlE7HdwuLEj20ATdMer1CFqmvO.ZF39qAMuI4GDp2BBrfr0gC', 'mondon2', 'arthur2', 'public/elements/profile_pics/pic.png', 0),
(3, 'arthurmdn3', '$2y$10$zBLKVlE7HdwuLEj20ATdMer1CFqmvO.ZF39qAMuI4GDp2BBrfr0gC', 'mondon3', 'arthur3', 'public/elements/profile_pics/pic.png', 0);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `message_destinataire` FOREIGN KEY (`MessageDestinataire`) REFERENCES `users` (`UserId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `message_expediteur` FOREIGN KEY (`MessageExpediteur`) REFERENCES `users` (`UserId`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
