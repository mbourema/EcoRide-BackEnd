<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220173419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE covoiturage (covoiturage_id INT AUTO_INCREMENT NOT NULL, date_depart DATETIME NOT NULL, lieu_depart VARCHAR(100) NOT NULL, date_arrivee DATETIME NOT NULL, lieu_arrivee VARCHAR(100) NOT NULL, statut VARCHAR(50) NOT NULL, nb_places INT NOT NULL, prix_personne DOUBLE PRECISION NOT NULL, voiture_id INT NOT NULL, conducteur_id INT NOT NULL, pseudo_conducteur INT NOT NULL, email_conducteur INT NOT NULL, INDEX IDX_28C79E89181A8BA (voiture_id), INDEX IDX_28C79E89F16F4AC6 (conducteur_id), INDEX IDX_28C79E89FBA76D03 (pseudo_conducteur), INDEX IDX_28C79E898FD4839C (email_conducteur), PRIMARY KEY(covoiturage_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE marque (marque_id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_5A6F91CEA4D60759 (libelle), PRIMARY KEY(marque_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE paiement (paiement_id INT AUTO_INCREMENT NOT NULL, montant DOUBLE PRECISION NOT NULL, date_paiement DATETIME NOT NULL, avancement VARCHAR(255) NOT NULL, credit_total_plateforme INT NOT NULL, utilisateur_id INT NOT NULL, covoiturage_id INT NOT NULL, INDEX IDX_B1DC7A1EFB88E14F (utilisateur_id), INDEX IDX_B1DC7A1E62671590 (covoiturage_id), PRIMARY KEY(paiement_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE role (role_id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_57698A6AA4D60759 (libelle), PRIMARY KEY(role_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE suspension (suspension_id INT AUTO_INCREMENT NOT NULL, raison VARCHAR(255) NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME DEFAULT NULL, sanction VARCHAR(255) NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_82AF0500FB88E14F (utilisateur_id), PRIMARY KEY(suspension_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateur (utilisateur_id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, nb_credit DOUBLE PRECISION DEFAULT 20 NOT NULL, mdp VARCHAR(255) NOT NULL, telephone VARCHAR(20) NOT NULL, adresse VARCHAR(100) NOT NULL, date_naissance DATE NOT NULL, pseudo VARCHAR(50) NOT NULL, photo VARCHAR(255) DEFAULT NULL, fumeur TINYINT(1) DEFAULT 0 NOT NULL, animal TINYINT(1) DEFAULT 0 NOT NULL, preference VARCHAR(100) NOT NULL, api_token VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), UNIQUE INDEX UNIQ_1D1C63B386CC499D (pseudo), PRIMARY KEY(utilisateur_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateur_role (utilisateur_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_9EE8E650FB88E14F (utilisateur_id), INDEX IDX_9EE8E650D60322AC (role_id), PRIMARY KEY(utilisateur_id, role_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE voiture (voiture_id INT AUTO_INCREMENT NOT NULL, modele VARCHAR(50) NOT NULL, immatriculation VARCHAR(20) NOT NULL, energie VARCHAR(20) NOT NULL, couleur VARCHAR(30) NOT NULL, date_premiere_immatriculation DATE NOT NULL, nb_places INT NOT NULL, marque_id INT NOT NULL, utilisateur_id INT NOT NULL, UNIQUE INDEX UNIQ_E9E2810FBE73422E (immatriculation), INDEX IDX_E9E2810F4827B9B2 (marque_id), INDEX IDX_E9E2810FFB88E14F (utilisateur_id), PRIMARY KEY(voiture_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE covoiturage ADD CONSTRAINT FK_28C79E89181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (voiture_id)');
        $this->addSql('ALTER TABLE covoiturage ADD CONSTRAINT FK_28C79E89F16F4AC6 FOREIGN KEY (conducteur_id) REFERENCES utilisateur (utilisateur_id)');
        $this->addSql('ALTER TABLE covoiturage ADD CONSTRAINT FK_28C79E89FBA76D03 FOREIGN KEY (pseudo_conducteur) REFERENCES utilisateur (utilisateur_id)');
        $this->addSql('ALTER TABLE covoiturage ADD CONSTRAINT FK_28C79E898FD4839C FOREIGN KEY (email_conducteur) REFERENCES utilisateur (utilisateur_id)');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1EFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (utilisateur_id)');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1E62671590 FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (covoiturage_id)');
        $this->addSql('ALTER TABLE suspension ADD CONSTRAINT FK_82AF0500FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (utilisateur_id)');
        $this->addSql('ALTER TABLE utilisateur_role ADD CONSTRAINT FK_9EE8E650FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (utilisateur_id)');
        $this->addSql('ALTER TABLE utilisateur_role ADD CONSTRAINT FK_9EE8E650D60322AC FOREIGN KEY (role_id) REFERENCES role (role_id)');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810F4827B9B2 FOREIGN KEY (marque_id) REFERENCES marque (marque_id)');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810FFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE covoiturage DROP FOREIGN KEY FK_28C79E89181A8BA');
        $this->addSql('ALTER TABLE covoiturage DROP FOREIGN KEY FK_28C79E89F16F4AC6');
        $this->addSql('ALTER TABLE covoiturage DROP FOREIGN KEY FK_28C79E89FBA76D03');
        $this->addSql('ALTER TABLE covoiturage DROP FOREIGN KEY FK_28C79E898FD4839C');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1EFB88E14F');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1E62671590');
        $this->addSql('ALTER TABLE suspension DROP FOREIGN KEY FK_82AF0500FB88E14F');
        $this->addSql('ALTER TABLE utilisateur_role DROP FOREIGN KEY FK_9EE8E650FB88E14F');
        $this->addSql('ALTER TABLE utilisateur_role DROP FOREIGN KEY FK_9EE8E650D60322AC');
        $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810F4827B9B2');
        $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810FFB88E14F');
        $this->addSql('DROP TABLE covoiturage');
        $this->addSql('DROP TABLE marque');
        $this->addSql('DROP TABLE paiement');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE suspension');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE utilisateur_role');
        $this->addSql('DROP TABLE voiture');
    }
}
