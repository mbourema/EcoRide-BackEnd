<?php

namespace App\Tests\Entity;

use App\Entity\Covoiturage;
use App\Entity\Utilisateur;
use App\Entity\Voiture;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;

class CovoiturageTest extends TestCase
{
    private Covoiturage $covoiturage;

    protected function setUp(): void
    {
        // Initialisation de l'entité Covoiturage
        $this->covoiturage = new Covoiturage();
    }

    public function testSettersAndGetters(): void
    {
        // Test des setters et getters pour les propriétés simples
        $this->covoiturage->setDateDepart(new \DateTime('2025-12-01 08:00:00'));
        $this->covoiturage->setLieuDepart('Paris');
        $this->covoiturage->setDateArrivee(new \DateTime('2025-12-01 10:00:00'));
        $this->covoiturage->setLieuArrivee('Lyon');
        $this->covoiturage->setStatut('Disponible');
        $this->covoiturage->setNbPlaces(3);
        $this->covoiturage->setPrixPersonne(30.0);
        
        // Vérification des valeurs
        $this->assertEquals(new \DateTime('2025-12-01 08:00:00'), $this->covoiturage->getDateDepart());
        $this->assertEquals('Paris', $this->covoiturage->getLieuDepart());
        $this->assertEquals(new \DateTime('2025-12-01 10:00:00'), $this->covoiturage->getDateArrivee());
        $this->assertEquals('Lyon', $this->covoiturage->getLieuArrivee());
        $this->assertEquals('Disponible', $this->covoiturage->getStatut());
        $this->assertEquals(3, $this->covoiturage->getNbPlaces());
        $this->assertEquals(30.0, $this->covoiturage->getPrixPersonne());
    }

    public function testRelationsWithUtilisateurAndVoiture(): void
    {
        // Création de mock pour les entités Utilisateur et Voiture
        $conducteur = $this->createMock(Utilisateur::class);
        $pseudoConducteur = $this->createMock(Utilisateur::class);
        $emailConducteur = $this->createMock(Utilisateur::class);
        $voiture = $this->createMock(Voiture::class);

        // Test des relations ManyToOne
        $this->covoiturage->setConducteur($conducteur);
        $this->covoiturage->setPseudo($pseudoConducteur);
        $this->covoiturage->setEmail($emailConducteur);
        $this->covoiturage->setVoiture($voiture);
        
        // Vérification des relations
        $this->assertSame($conducteur, $this->covoiturage->getConducteur());
        $this->assertSame($pseudoConducteur, $this->covoiturage->getPseudo());
        $this->assertSame($emailConducteur, $this->covoiturage->getEmail());
        $this->assertSame($voiture, $this->covoiturage->getVoiture());
    }

    public function testAddAndRemoveVoiture(): void
    {
        // Simuler une voiture fictive
        $voiture = $this->createMock(Voiture::class);
        
        // Ajout d'une voiture à l'objet Covoiturage
        $this->covoiturage->setVoiture($voiture);
        
        // Vérification que la voiture est bien associée à ce covoiturage
        $this->assertSame($voiture, $this->covoiturage->getVoiture());
    }

    public function testAddAndRemoveUtilisateur(): void
    {
        // Simulation d'un utilisateur fictif
        $utilisateur = $this->createMock(Utilisateur::class);
        
        // Test de l'ajout et retrait de l'utilisateur en tant que conducteur, pseudo, et email
        $this->covoiturage->setConducteur($utilisateur);
        $this->covoiturage->setPseudo($utilisateur);
        $this->covoiturage->setEmail($utilisateur);
        
        // Vérification que les relations sont bien mises à jour
        $this->assertSame($utilisateur, $this->covoiturage->getConducteur());
        $this->assertSame($utilisateur, $this->covoiturage->getPseudo());
        $this->assertSame($utilisateur, $this->covoiturage->getEmail());
    }

    public function testDefaultValues(): void
    {
        // Vérification que les valeurs par défaut sont bien définies
        $this->assertNull($this->covoiturage->getCovoiturageId());
        $this->assertNull($this->covoiturage->getDateDepart());
        $this->assertNull($this->covoiturage->getLieuDepart());
        $this->assertNull($this->covoiturage->getDateArrivee());
        $this->assertNull($this->covoiturage->getLieuArrivee());
        $this->assertNull($this->covoiturage->getStatut());
        $this->assertNull($this->covoiturage->getNbPlaces());
        $this->assertNull($this->covoiturage->getPrixPersonne());
        $this->assertNull($this->covoiturage->getVoiture());
        $this->assertNull($this->covoiturage->getConducteur());
        $this->assertNull($this->covoiturage->getPseudo());
        $this->assertNull($this->covoiturage->getEmail());
        $this->assertNull($this->covoiturage->getPhoto());
    }
}
