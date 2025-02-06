<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class Preferences
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: "string")]
    private string $conducteurId;

    #[MongoDB\Field(type: "bool")]
    private bool $fumeur;

    #[MongoDB\Field(type: "bool")]
    private bool $animauxAcceptes;

    #[MongoDB\Field(type: "string")]
    private ?string $preferencesPerso = null;

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

    public function isFumeur(): bool
    {
        return $this->fumeur;
    }

    public function setFumeur(bool $fumeur): self
    {
        $this->fumeur = $fumeur;
        return $this;
    }

    public function isAnimauxAcceptes(): bool
    {
        return $this->animauxAcceptes;
    }

    public function setAnimauxAcceptes(bool $animauxAcceptes): self
    {
        $this->animauxAcceptes = $animauxAcceptes;
        return $this;
    }

    public function getPreferencesPerso(): ?string
    {
        return $this->preferencesPerso;
    }

    public function setPreferencesPerso(?string $preferencesPerso): self
    {
        $this->preferencesPerso = $preferencesPerso;
        return $this;
    }
}
