<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class Commande
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: "string")]
    private string $conducteurId;

    #[MongoDB\Field(type: "string")]
    private string $passagerId;

    #[MongoDB\Field(type: "string")]
    private string $covoiturageId;

    #[MongoDB\Field(type: "string")]
    private string $lieuDepart;

    #[MongoDB\Field(type: "string")]
    private string $lieuArrivee;

    #[MongoDB\Field(type: "date")]
    private \DateTime $dateDepart;

    #[MongoDB\Field(type: "date")]
    private \DateTime $dateArrivee;

    #[MongoDB\Field(type: "string")]
    private string $statut; // Ex: "en attente", "confirmé", "annulé"

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getConducteurId(): string
    {
        return $this->conducteurId;
    }

    public function setConducteurId(string $conducteurId): self
    {
        $this->conducteurId = $conducteurId;
        return $this;
    }

    public function getPassagerId(): string
    {
        return $this->passagerId;
    }

    public function setPassagerId(string $passagerId): self
    {
        $this->passagerId = $passagerId;
        return $this;
    }

    public function getCovoiturageId(): string
    {
        return $this->covoiturageId;
    }

    public function setCovoiturageId(string $covoiturageId): self
    {
        $this->covoiturageId = $covoiturageId;
        return $this;
    }

    public function getLieuDepart(): string
    {
        return $this->lieuDepart;
    }

    public function setLieuDepart(string $lieuDepart): self
    {
        $this->lieuDepart = $lieuDepart;
        return $this;
    }

    public function getLieuArrivee(): string
    {
        return $this->lieuArrivee;
    }

    public function setLieuArrivee(string $lieuArrivee): self
    {
        $this->lieuArrivee = $lieuArrivee;
        return $this;
    }

    public function getDateDepart(): \DateTime
    {
        return $this->dateDepart;
    }

    public function setDateDepart(\DateTime $dateDepart): self
    {
        $this->dateDepart = $dateDepart;
        return $this;
    }

    public function getDateArrivee(): \DateTime
    {
        return $this->dateArrivee;
    }

    public function setDateArrivee(\DateTime $dateArrivee): self
    {
        $this->dateArrivee = $dateArrivee;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }
}
