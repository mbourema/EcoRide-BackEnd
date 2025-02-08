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

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCovoiturageId(): ?string
    {
        return $this->covoiturageId;
    }

    public function setCovoiturageId(string $covoiturageId): self
    {
        $this->covoiturageId = $covoiturageId;
        return $this;
    }

    public function getPassagerId(): ?string
    {
        return $this->passagerId;
    }

    public function setPassagerId(string $passagerId): self
    {
        $this->passagerId = $passagerId;
        return $this;
    }

    public function getConducteurId(): ?string
    {
        return $this->conducteurId;
    }

    public function setConducteurId(string $conducteurId): self
    {
        $this->conducteurId = $conducteurId;
        return $this;
    }

    public function getNote(): float
    {
        return $this->note;
    }

    public function setNote(float $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function getCommentaire(): string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getSignale(): bool
    {
        return $this->signale;
    }

    public function setSignale(bool $signale): self
    {
        $this->signale = $signale;
        return $this;
    }
}

