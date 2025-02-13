<?php

namespace App\Tests\Entity;

use App\Entity\Utilisateur;
use App\Entity\Role;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class UtilisateurTest extends TestCase
{
    private Utilisateur $utilisateur;

    protected function setUp(): void
    {
        // Initialisation de l'entité Utilisateur
        $this->utilisateur = new Utilisateur();
    }

    public function testSettersAndGetters(): void
    {
        // Test des setters et getters
        $this->utilisateur->setNom('Dupont');
        $this->utilisateur->setPrenom('Jean');
        $this->utilisateur->setEmail('jean.dupont@example.com');
        $this->utilisateur->setMdp('securepassword');
        $this->utilisateur->setTelephone('0123456789');
        $this->utilisateur->setAdresse('123 Rue de Paris');
        $this->utilisateur->setDateNaissance(new \DateTime('1980-01-01'));
        $this->utilisateur->setPseudo('jdupont');
        
        // Vérification des valeurs des getters
        $this->assertEquals('Dupont', $this->utilisateur->getNom());
        $this->assertEquals('Jean', $this->utilisateur->getPrenom());
        $this->assertEquals('jean.dupont@example.com', $this->utilisateur->getEmail());
        $this->assertEquals('securepassword', $this->utilisateur->getMdp());
        $this->assertEquals('0123456789', $this->utilisateur->getTelephone());
        $this->assertEquals('123 Rue de Paris', $this->utilisateur->getAdresse());
        $this->assertEquals(new \DateTime('1980-01-01'), $this->utilisateur->getDateNaissance());
        $this->assertEquals('jdupont', $this->utilisateur->getPseudo());
    }

    public function testDefaultFumeurAndAnimal(): void
    {
        // Vérification des valeurs par défaut
        $this->assertFalse($this->utilisateur->getFumeur());
        $this->assertFalse($this->utilisateur->getAnimal());
    }
}
