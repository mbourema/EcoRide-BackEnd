<?php

namespace App\Entity;

use App\Repository\MarqueRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: MarqueRepository::class)]
class Marque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $marque_id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: "marque", targetEntity: Voiture::class, cascade: ["remove"])]
    private Collection $voitures;

    public function __construct()
    {
    $this->voitures = new ArrayCollection();
    }

    public function getMarqueId(): ?int
    {
        return $this->marque_id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    // Ajout de la méthode getVoitures pour récupérer les voitures associées à cette marque
    public function getVoitures(): Collection
    {
        return $this->voitures;
    }
}
