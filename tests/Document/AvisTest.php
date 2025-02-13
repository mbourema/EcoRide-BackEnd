<?php

namespace App\Tests\Document;

use App\Document\Avis;
use PHPUnit\Framework\TestCase;

class AvisTest extends TestCase
{
    private Avis $avis;

    protected function setUp(): void
    {
        // Initialisation de l'entité Avis
        $this->avis = new Avis();
    }

    public function testSetSignaleTrue(): void
    {
        // Test de la mise à jour lorsque 'signale' est vrai
        $this->avis->setSignale(true);
        $this->avis->setJustification('Comportement inapproprié');
        
        // Vérification que 'signale' est vrai et qu'il y a une justification
        $this->assertTrue($this->avis->getSignale());
        $this->assertEquals('Comportement inapproprié', $this->avis->getJustification());
    }

    public function testSetSignaleFalse(): void
    {
        // Test de la mise à jour lorsque 'signale' est faux
        $this->avis->setSignale(false);
        $this->avis->setJustification('');
        
        // Vérification que 'signale' est faux et qu'il n'y a pas de justification
        $this->assertFalse($this->avis->getSignale());
        $this->assertEquals('', $this->avis->getJustification());
    }

    public function testValidationLogic(): void
    {
        // Test de la mise à jour de la validation
        $this->avis->setValidation(true);
        $this->assertTrue($this->avis->getValidation());

        $this->avis->setValidation(false);
        $this->assertFalse($this->avis->getValidation());
    }
}
