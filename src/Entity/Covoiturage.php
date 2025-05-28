<?php

namespace App\Entity;

use App\Repository\CovoiturageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Utilisateur;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CovoiturageRepository::class)]
class Covoiturage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $covoiturage_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_depart = null;

    #[ORM\Column(length: 100)]
    private ?string $lieu_depart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_arrivee = null;

    #[ORM\Column(length: 100)]
    private ?string $lieu_arrivee = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?int $nb_places = null;

    #[ORM\Column]
    private ?float $prix_personne = null;

    #[ORM\ManyToOne(targetEntity: Voiture::class, inversedBy: "covoiturages")]
    #[ORM\JoinColumn(name: "voiture_id", referencedColumnName: "voiture_id", nullable: false)]
    private ?Voiture $voiture = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'covoituragesAsConducteur')]
    #[ORM\JoinColumn(name: 'conducteur_id', referencedColumnName: 'utilisateur_id', nullable: false)]
    private ?Utilisateur $conducteur = null;
    
    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'covoituragesAsPseudo')]
    #[ORM\JoinColumn(name: 'pseudo_conducteur', referencedColumnName: 'utilisateur_id', nullable: false)]
    public ?Utilisateur $pseudo = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'covoituragesAsEmail')]
    #[ORM\JoinColumn(name: 'email_conducteur', referencedColumnName: 'utilisateur_id', nullable: false)]
    public ?Utilisateur $email = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created_at = null;


    public function getCovoiturageId(): ?int
    {
        return $this->covoiturage_id;
    }

    public function getDateDepart(): ?\DateTimeInterface
    {
        return $this->date_depart;
    }

    public function setDateDepart(\DateTimeInterface $date_depart): static
    {
        $this->date_depart = $date_depart;
        return $this;
    }

    public function getLieuDepart(): ?string
    {
        return $this->lieu_depart;
    }

    public function setLieuDepart(string $lieu_depart): static
    {
        $this->lieu_depart = $lieu_depart;
        return $this;
    }

    public function getDateArrivee(): ?\DateTimeInterface
    {
        return $this->date_arrivee;
    }

    public function setDateArrivee(\DateTimeInterface $date_arrivee): static
    {
        $this->date_arrivee = $date_arrivee;
        return $this;
    }

    public function getLieuArrivee(): ?string
    {
        return $this->lieu_arrivee;
    }

    public function setLieuArrivee(string $lieu_arrivee): static
    {
        $this->lieu_arrivee = $lieu_arrivee;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
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

    public function getPrixPersonne(): ?float
    {
        return $this->prix_personne;
    }

    public function setPrixPersonne(float $prix_personne): static
    {
        $this->prix_personne = $prix_personne;
        return $this;
    }

    public function getVoiture(): ?Voiture
    {
        return $this->voiture;
    }

    public function setVoiture(Voiture $voiture): static
    {
        $this->voiture = $voiture;
        return $this;
    }

    public function getConducteur(): ?Utilisateur
    {
        return $this->conducteur;
    }

    public function setConducteur(Utilisateur $conducteur): static
    {
        $this->conducteur = $conducteur;
        return $this;
    }

    public function getPseudo(): ?Utilisateur
    {
        return $this->pseudo;
    }

    public function setPseudo(Utilisateur $pseudo): static
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function getEmail(): ?Utilisateur
    {
        return $this->email;
    }

    public function setEmail(Utilisateur $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPhoto(): ?string
    {
    return $this->conducteur ? $this->conducteur->getPhoto() : null;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
    return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
    $this->created_at = $created_at;
    return $this;
    }

}

