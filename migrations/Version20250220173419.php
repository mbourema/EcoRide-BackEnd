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
        // Suppression du charset MySQL et correction des valeurs par défaut
        $this->addSql('CREATE TABLE covoiturage (
            covoiturage_id SERIAL PRIMARY KEY,
            date_depart TIMESTAMP NOT NULL,
            lieu_depart VARCHAR(100) NOT NULL,
            date_arrivee TIMESTAMP NOT NULL,
            lieu_arrivee VARCHAR(100) NOT NULL,
            statut VARCHAR(50) NOT NULL,
            nb_places INT NOT NULL,
            prix_personne DOUBLE PRECISION NOT NULL,
            voiture_id INT NOT NULL,
            conducteur_id INT NOT NULL,
            pseudo_conducteur INT NOT NULL,
            email_conducteur INT NOT NULL,
            FOREIGN KEY (voiture_id) REFERENCES voiture (voiture_id),
            FOREIGN KEY (conducteur_id) REFERENCES utilisateur (utilisateur_id),
            FOREIGN KEY (pseudo_conducteur) REFERENCES utilisateur (utilisateur_id),
            FOREIGN KEY (email_conducteur) REFERENCES utilisateur (utilisateur_id)
        )');

        $this->addSql('CREATE TABLE marque (
            marque_id SERIAL PRIMARY KEY,
            libelle VARCHAR(50) NOT NULL,
            UNIQUE (libelle)
        )');

        $this->addSql('CREATE TABLE paiement (
            paiement_id SERIAL PRIMARY KEY,
            montant DOUBLE PRECISION NOT NULL,
            date_paiement TIMESTAMP NOT NULL,
            avancement VARCHAR(255) NOT NULL,
            credit_total_plateforme INT NOT NULL,
            utilisateur_id INT NOT NULL,
            covoiturage_id INT NOT NULL,
            FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (utilisateur_id),
            FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (covoiturage_id)
        )');

        $this->addSql('CREATE TABLE role (
            role_id SERIAL PRIMARY KEY,
            libelle VARCHAR(50) NOT NULL,
            UNIQUE (libelle)
        )');

        $this->addSql('CREATE TABLE suspension (
            suspension_id SERIAL PRIMARY KEY,
            raison VARCHAR(255) NOT NULL,
            date_debut TIMESTAMP NOT NULL,
            date_fin TIMESTAMP NULL,
            sanction VARCHAR(255) NOT NULL,
            utilisateur_id INT NOT NULL,
            FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (utilisateur_id)
        )');

        $this->addSql('CREATE TABLE utilisateur (
            utilisateur_id SERIAL PRIMARY KEY,
            nom VARCHAR(50) NOT NULL,
            prenom VARCHAR(50) NOT NULL,
            email VARCHAR(50) NOT NULL,
            nb_credit DOUBLE PRECISION DEFAULT 20 NOT NULL,
            mdp VARCHAR(255) NOT NULL,
            telephone VARCHAR(20) NOT NULL,
            adresse VARCHAR(100) NOT NULL,
            date_naissance DATE NOT NULL,
            pseudo VARCHAR(50) NOT NULL,
            photo VARCHAR(255) NULL,
            fumeur BOOLEAN DEFAULT FALSE NOT NULL,
            animal BOOLEAN DEFAULT FALSE NOT NULL,
            preference VARCHAR(100) NOT NULL,
            api_token VARCHAR(255) NOT NULL,
            UNIQUE (email),
            UNIQUE (pseudo)
        )');

        $this->addSql('CREATE TABLE utilisateur_role (
            utilisateur_id INT NOT NULL,
            role_id INT NOT NULL,
            PRIMARY KEY (utilisateur_id, role_id),
            FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (utilisateur_id),
            FOREIGN KEY (role_id) REFERENCES role (role_id)
        )');

        $this->addSql('CREATE TABLE voiture (
            voiture_id SERIAL PRIMARY KEY,
            modele VARCHAR(50) NOT NULL,
            immatriculation VARCHAR(20) NOT NULL,
            energie VARCHAR(20) NOT NULL,
            couleur VARCHAR(30) NOT NULL,
            date_premiere_immatriculation DATE NOT NULL,
            nb_places INT NOT NULL,
            marque_id INT NOT NULL,
            utilisateur_id INT NOT NULL,
            UNIQUE (immatriculation),
            FOREIGN KEY (marque_id) REFERENCES marque (marque_id),
            FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (utilisateur_id)
        )');
    }

    public function down(Schema $schema): void
    {
        // Rollback logic if needed
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

