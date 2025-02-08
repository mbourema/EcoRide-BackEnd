<?php

namespace App\Entity;

use App\Repository\VoitureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoitureRepository::class)]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $voiture_id = null;

    #[ORM\Column(length: 50)]
    private ?string $modele = null;

    #[ORM\Column(length: 20, unique: true)]
    private ?string $immatriculation = null;

    #[ORM\Column(length: 20)]
    private ?string $energie = null;

    #[ORM\Column(length: 30)]
    private ?string $couleur = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeInterface $date_premiere_immatriculation = null;

    #[ORM\Column]
    private ?int $nb_places = null;

    #[ORM\ManyToOne(targetEntity: Marque::class, cascade: ["remove"])]
    #[ORM\JoinColumn(name: "marque_id", referencedColumnName: "marque_id", nullable: false)]
    private ?Marque $marque = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "utilisateur_id", referencedColumnName: "utilisateur_id", nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "covoiturage_id", referencedColumnName: "covoiturage_id", nullable: false)]
    private ?Covoiturage $covoiturage = null;

    public function getVoitureId(): ?int
    {
        return $this->voiture_id;
    } 

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): static
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getEnergie(): ?string
    {
        return $this->energie;
    }

    public function setEnergie(string $energie): static
    {
        $this->energie = $energie;

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(string $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getDatePremiereImmatriculation(): ?\DateTimeInterface
    {
        return $this->date_premiere_immatriculation;
    }

    public function setDatePremiereImmatriculation(\DateTimeInterface $date_premiere_immatriculation): static
    {
        $this->date_premiere_immatriculation = $date_premiere_immatriculation;

        return $this;
    }

    public function getNbPlaces(): ?int
    {
        return $this->nb_places;
    }

    public function setNbPlaces(int $nb_places): static
    {
        $this->nb_places = $nb_places;

        return $this;
    }

    public function setMarque(Marque $marque): static
    {
    $this->marque = $marque;
    return $this;
    }

    public function getMarque(): ?Marque
    {
    return $this->marque;
    }

    public function setUtilisateur(Utilisateur $utilisateur): static
    {
    $this->utilisateur = $utilisateur;
    return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
    return $this->utilisateur;
    }

    public function setCovoiturage(Covoiturage $covoiturage): static
    {
        $this->covoiturage = $covoiturage;
        return $this;
    }

    public function getCovoiturage(): ?Covoiturage
    {
        return $this->covoiturage;
    }
}
