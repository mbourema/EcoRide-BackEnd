<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Role;
use App\Entity\Covoiturage;
use App\Entity\Voiture;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $utilisateur_id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'integer', options: ["default" => 20])]
    private int $nbCredit = 20;

    #[ORM\Column(length: 255)]
    private ?string $mdp = null;

    #[ORM\Column(length: 20)]
    private ?string $telephone = null;

    #[ORM\Column(length: 100)]
    private ?string $adresse = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_naissance = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $pseudo = null;

    #[ORM\Column(type: 'blob', nullable: true)]
    private $photo;

    #[ORM\Column(type: 'boolean', options: ["default" => false])]
    private ?bool $fumeur = false;

    #[ORM\Column(type: 'boolean', options: ["default" => false])]
    private ?bool $animal = false;

    #[ORM\Column(length: 100)]
    private ?string $preference = '';

    // Relation avec les covoiturages en tant que conducteur
    #[ORM\OneToMany(mappedBy: 'conducteur', targetEntity: Covoiturage::class)]
    private Collection $covoituragesAsConducteur;

    // Relation avec les covoiturages par pseudo
    #[ORM\OneToMany(mappedBy: 'pseudo', targetEntity: Covoiturage::class)]
    private Collection $covoituragesAsPseudo;

    //Relation avec les covoiturages par email
    #[ORM\OneToMany(mappedBy: 'email', targetEntity: Covoiturage::class)]
    private Collection $covoituragesAsEmail;

    // Relation avec les rôles
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'utilisateurs')]
    #[ORM\JoinTable(name: 'utilisateur_role', joinColumns: [new ORM\JoinColumn(name: 'utilisateur_id', referencedColumnName: 'utilisateur_id')], inverseJoinColumns: [new ORM\JoinColumn(name: 'role_id', referencedColumnName: 'role_id')])]
    private Collection $roles;

    // Relation avec les voitures
    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Voiture::class)]
    private Collection $voitures;

    // Relation avec les suspensions
    #[ORM\OneToMany(mappedBy: 'utilisateur_id', targetEntity: Suspension::class)]
    private Collection $suspensions;

    // Relation avec les paiements
    #[ORM\OneToMany(mappedBy: 'utilisateur_id', targetEntity: Paiement::class)]
    private Collection $paiements;


    public function __construct()
    {
        $this->covoituragesAsConducteur = new ArrayCollection();
        $this->covoituragesAsPseudo = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->voitures = new ArrayCollection();
        $this->suspensions = new ArrayCollection();
        $this->paiements = new ArrayCollection();
        $this->fumeur = false;
        $this->animal = false;
        $this->preference = "";
    }

    // Getters et setters pour chaque propriété

    public function getUtilisateurId(): ?int
    {
        return $this->utilisateur_id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getNbCredit(): int
    {
        return $this->nbCredit;
    }

    public function setNbCredit(int $nbCredit): static
    {
        $this->nbCredit = $nbCredit;
        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): static
    {
        $this->mdp = $mdp;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): static
    {
        $this->date_naissance = $date_naissance;
        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo): static
    {
        $this->photo = $photo;
        return $this;
    }

    public function getFumeur(): ?bool
    {
        return $this->fumeur;
    }

    public function setFumeur(bool $fumeur): static
    {
        $this->fumeur = $fumeur;
        return $this;
    }
    
    public function getAnimal(): ?bool
    {
        return $this->animal;
    }

    public function setAnimal(bool $animal): static
    {
        $this->animal = $animal;
        return $this;
    }
    
    public function getPreference(): string
    {
        return $this->preference ?? '';
    }

    public function setPreference(string $preference): static
    {
        $this->preference = $preference;
        return $this;
    }

    // Getter pour les covoiturages en tant que conducteur
    public function getCovoituragesAsConducteur(): Collection
    {
        return $this->covoituragesAsConducteur;
    }

    // Gestion des rôles

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }
        return $this;
    }

    public function removeRole(Role $role): static
    {
        $this->roles->removeElement($role);
        return $this;
    }

    // Gestion des voitures

    public function getVoitures(): Collection
    {
        return $this->voitures;
    }

    public function addVoiture(Voiture $voiture): static
    {
    if (!$this->voitures->contains($voiture)) {
        $this->voitures[] = $voiture;
        $voiture->setUtilisateur($this);  // Associe l'utilisateur à cette voiture
    }
    return $this;
    }

    public function removeVoiture(Voiture $voiture): static
    {
        if ($this->voitures->removeElement($voiture)) {
            // Définit l'utilisateur de la voiture à null si nécessaire
            if ($voiture->getUtilisateur() === $this) {
                $voiture->setUtilisateur(null);
            }
        }
        return $this;
    }

    // Gestion des suspensions

    public function getSuspensions(): Collection
    {
        return $this->suspensions;
    }

    public function addSuspension(Suspension $suspension): static
    {
    if (!$this->suspensions->contains($suspension)) {
        $this->suspensions[] = $suspension;
        $suspension->setUtilisateur($this);  // Associe l'utilisateur à cette suspension
    }
    return $this;
    }

    public function removeSuspension(Suspension $suspension): static
    {
        if ($this->suspensions->removeElement($suspension)) {
            if ($suspension->getUtilisateur() === $this) {
                $suspension->setUtilisateur(null);
            }
        }
        return $this;
    }

    // Gestion des paiements

    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): static
    {
    if (!$this->paiements->contains($paiement)) {
        $this->paiements[] = $paiement;
        $paiement->setUtilisateur($this);  // Associe l'utilisateur à ce paiement
    }
    return $this;
    }

    public function removePaiement(Paiement $paiement): static
    {
        if ($this->paiements->removeElement($paiement)) {
            if ($paiement->getUtilisateur() === $this) {
                $paiement->setUtilisateur(null);
            }
        }
        return $this;
    }
}

