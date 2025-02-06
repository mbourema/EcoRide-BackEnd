<?php
// src/Document/Avis.php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;


#[MongoDB\Document]
class Avis
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: "string")]
    private string $covoiturageId;

    #[MongoDB\Field(type: "string")]
    private string $passagerId;

    #[MongoDB\Field(type: "string")]
    private string $conducteurId;

    #[MongoDB\Field(type: "float")]
    private float $note;

    #[MongoDB\Field(type: "string")]
    private string $commentaire;

    #[MongoDB\Field(type: "date")]
    private \DateTime $date;

    #[MongoDB\Field(type: "bool")]
    private bool $signale = false;

    public function getCovoiturageId(): ?string
    {
        return $this->covoiturageId;
    }

    public function setCovoiturageId(string $covoiturageId): self
    {
        $this->covoiturageId = $covoiturageId;
        return $this;
    }

    public function setPassagerId(string $passagerId): self
    {
        $this->passagerId = $passagerId;
        return $this;
    }

    public function setConducteurId(string $conducteurId): self
    {
        $this->conducteurId = $conducteurId;
        return $this;
    }

    public function setNote(float $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }
}
