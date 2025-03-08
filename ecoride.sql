-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: ecoride
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `covoiturage`
--

DROP TABLE IF EXISTS `covoiturage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `covoiturage` (
  `covoiturage_id` int(11) NOT NULL AUTO_INCREMENT,
  `date_depart` datetime NOT NULL,
  `lieu_depart` varchar(100) NOT NULL,
  `date_arrivee` datetime NOT NULL,
  `lieu_arrivee` varchar(100) NOT NULL,
  `statut` varchar(50) NOT NULL,
  `nb_places` int(11) NOT NULL,
  `prix_personne` double NOT NULL,
  `voiture_id` int(11) NOT NULL,
  `conducteur_id` int(11) NOT NULL,
  `pseudo_conducteur` int(11) NOT NULL,
  `email_conducteur` int(11) NOT NULL,
  PRIMARY KEY (`covoiturage_id`),
  KEY `IDX_28C79E89181A8BA` (`voiture_id`),
  KEY `IDX_28C79E89F16F4AC6` (`conducteur_id`),
  KEY `IDX_28C79E89FBA76D03` (`pseudo_conducteur`),
  KEY `IDX_28C79E898FD4839C` (`email_conducteur`),
  CONSTRAINT `FK_28C79E89181A8BA` FOREIGN KEY (`voiture_id`) REFERENCES `voiture` (`voiture_id`),
  CONSTRAINT `FK_28C79E898FD4839C` FOREIGN KEY (`email_conducteur`) REFERENCES `utilisateur` (`utilisateur_id`),
  CONSTRAINT `FK_28C79E89F16F4AC6` FOREIGN KEY (`conducteur_id`) REFERENCES `utilisateur` (`utilisateur_id`),
  CONSTRAINT `FK_28C79E89FBA76D03` FOREIGN KEY (`pseudo_conducteur`) REFERENCES `utilisateur` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `covoiturage`
--

LOCK TABLES `covoiturage` WRITE;
/*!40000 ALTER TABLE `covoiturage` DISABLE KEYS */;
/*!40000 ALTER TABLE `covoiturage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marque`
--

DROP TABLE IF EXISTS `marque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marque` (
  `marque_id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) NOT NULL,
  PRIMARY KEY (`marque_id`),
  UNIQUE KEY `UNIQ_5A6F91CEA4D60759` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marque`
--

LOCK TABLES `marque` WRITE;
/*!40000 ALTER TABLE `marque` DISABLE KEYS */;
INSERT INTO `marque` VALUES (1,'Alfa Romeo'),(2,'Audi'),(3,'BMW'),(4,'Dacia'),(5,'Fiat'),(10,'Ford'),(9,'Mercedes'),(11,'Nissan'),(12,'Opel'),(6,'Peugeot'),(7,'Renault'),(8,'Volkswagen'),(13,'Volvo');
/*!40000 ALTER TABLE `marque` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paiement`
--

DROP TABLE IF EXISTS `paiement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paiement` (
  `paiement_id` int(11) NOT NULL AUTO_INCREMENT,
  `montant` double NOT NULL,
  `date_paiement` datetime NOT NULL,
  `avancement` varchar(255) NOT NULL,
  `credit_total_plateforme` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `covoiturage_id` int(11) NOT NULL,
  PRIMARY KEY (`paiement_id`),
  KEY `IDX_B1DC7A1EFB88E14F` (`utilisateur_id`),
  KEY `IDX_B1DC7A1E62671590` (`covoiturage_id`),
  CONSTRAINT `FK_B1DC7A1E62671590` FOREIGN KEY (`covoiturage_id`) REFERENCES `covoiturage` (`covoiturage_id`),
  CONSTRAINT `FK_B1DC7A1EFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paiement`
--

LOCK TABLES `paiement` WRITE;
/*!40000 ALTER TABLE `paiement` DISABLE KEYS */;
/*!40000 ALTER TABLE `paiement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) NOT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `UNIQ_57698A6AA4D60759` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'ROLE_ADMIN'),(3,'ROLE_CONDUCTEUR'),(2,'ROLE_EMPLOYE'),(4,'ROLE_PASSAGER');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suspension`
--

DROP TABLE IF EXISTS `suspension`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suspension` (
  `suspension_id` int(11) NOT NULL AUTO_INCREMENT,
  `raison` varchar(255) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime DEFAULT NULL,
  `sanction` varchar(255) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  PRIMARY KEY (`suspension_id`),
  KEY `IDX_82AF0500FB88E14F` (`utilisateur_id`),
  CONSTRAINT `FK_82AF0500FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suspension`
--

LOCK TABLES `suspension` WRITE;
/*!40000 ALTER TABLE `suspension` DISABLE KEYS */;
/*!40000 ALTER TABLE `suspension` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateur` (
  `utilisateur_id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `nb_credit` double NOT NULL DEFAULT 20,
  `mdp` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `adresse` varchar(100) NOT NULL,
  `date_naissance` date NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `fumeur` tinyint(1) NOT NULL DEFAULT 0,
  `animal` tinyint(1) NOT NULL DEFAULT 0,
  `preference` varchar(100) NOT NULL,
  `api_token` varchar(255) NOT NULL,
  `reset_password_token` varchar(255) DEFAULT NULL,
  `reset_password_token_expiration` date DEFAULT NULL,
  PRIMARY KEY (`utilisateur_id`),
  UNIQUE KEY `UNIQ_1D1C63B3E7927C74` (`email`),
  UNIQUE KEY `UNIQ_1D1C63B386CC499D` (`pseudo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateur`
--

LOCK TABLES `utilisateur` WRITE;
/*!40000 ALTER TABLE `utilisateur` DISABLE KEYS */;
INSERT INTO `utilisateur` VALUES (1,'Bourema','Mehdi','mehdibourema@outlook.fr',20,'$2y$10$Tdltoi72zfTnUXEGutw73OOQnBhI6lx4LCePDdPBHwCtmVRvvSjbm','0612345678','10 rue de Paris, 75000 Paris','1990-05-15','JeanD','https://example.com/photo.jpg',0,0,'','f33ea250d5e19ca62cf51a908c617db0851f209f','e2e9b1e4502be46d44de00c70391cf2c59753e3d','2025-03-07');
/*!40000 ALTER TABLE `utilisateur` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateur_role`
--

DROP TABLE IF EXISTS `utilisateur_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateur_role` (
  `utilisateur_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`utilisateur_id`,`role_id`),
  KEY `IDX_9EE8E650FB88E14F` (`utilisateur_id`),
  KEY `IDX_9EE8E650D60322AC` (`role_id`),
  CONSTRAINT `FK_9EE8E650D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`),
  CONSTRAINT `FK_9EE8E650FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateur_role`
--

LOCK TABLES `utilisateur_role` WRITE;
/*!40000 ALTER TABLE `utilisateur_role` DISABLE KEYS */;
INSERT INTO `utilisateur_role` VALUES (1,3);
/*!40000 ALTER TABLE `utilisateur_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voiture`
--

DROP TABLE IF EXISTS `voiture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `voiture` (
  `voiture_id` int(11) NOT NULL AUTO_INCREMENT,
  `modele` varchar(50) NOT NULL,
  `immatriculation` varchar(20) NOT NULL,
  `energie` varchar(20) NOT NULL,
  `couleur` varchar(30) NOT NULL,
  `date_premiere_immatriculation` date NOT NULL,
  `nb_places` int(11) NOT NULL,
  `marque_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  PRIMARY KEY (`voiture_id`),
  UNIQUE KEY `UNIQ_E9E2810FBE73422E` (`immatriculation`),
  KEY `IDX_E9E2810F4827B9B2` (`marque_id`),
  KEY `IDX_E9E2810FFB88E14F` (`utilisateur_id`),
  CONSTRAINT `FK_E9E2810F4827B9B2` FOREIGN KEY (`marque_id`) REFERENCES `marque` (`marque_id`),
  CONSTRAINT `FK_E9E2810FFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voiture`
--

LOCK TABLES `voiture` WRITE;
/*!40000 ALTER TABLE `voiture` DISABLE KEYS */;
/*!40000 ALTER TABLE `voiture` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-07 23:26:37
