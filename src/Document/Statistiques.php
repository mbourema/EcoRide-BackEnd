<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class Statistiques
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: "date")]
    private \DateTime $date;

    #[MongoDB\Field(type: "int")]
    private int $nbCovoiturages;

    #[MongoDB\Field(type: "int")]
    private int $nbCreditsJour;

    #[MongoDB\Field(type: "int")]
    private int $nbCreditsTotal;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getNbCovoiturages(): int
    {
        return $this->nbCovoiturages;
    }

    public function setNbCovoiturages(int $nbCovoiturages): self
    {
        $this->nbCovoiturages = $nbCovoiturages;
        return $this;
    }

    public function getNbCreditsJour(): int
    {
        return $this->nbCreditsJour;
    }

    public function setNbCreditsJour(int $nbCreditsJour): self
    {
        $this->nbCreditsJour = $nbCreditsJour;
        return $this;
    }

    public function getNbCreditsTotal(): int
    {
        return $this->nbCreditsTotal;
    }

    public function setNbCreditsTotal(int $nbCreditsTotal): self
    {
        $this->nbCreditsTotal = $nbCreditsTotal;
        return $this;
    }
}
