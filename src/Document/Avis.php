<?php
// src/Document/Avis.php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class Avis
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: "integer")] // A recuperer dans la classe Utilisateur.php (utilisateur_id)
    private int $utilisateur_id_passager;

    #[MongoDB\Field(type: "string")] // A recuperer dans la classe Utilisateur.php (pseudo)
    private string $pseudo_passager;

    #[MongoDB\Field(type: "integer")] // A recuperer dans la classe Covoiturage.php (covoiturage_id)
    private int $covoiturage_id;

    #[MongoDB\Field(type: "string")] // A recuperer dans la classe Utilisateur.php après avoir récupéré l'id dans Covoiturage.php (pseudo)
    private string $pseudo_conducteur;

    #[MongoDB\Field(type: "string")] // A recuperer dans la classe Utilisateur.php
    private string $email_passager;

    #[MongoDB\Field(type: "string")] // A recuperer dans la classe Utilisateur.php après avoir récupéré l'id dans Covoiturage.php
    private string $email_conducteur;

    #[MongoDB\Field(type: "date")] // A recuperer dans la classe Covoiturage.php
    private \DateTime $date_depart;

    #[MongoDB\Field(type: "date")] // A recuperer dans la classe Covoiturage.php
    private \DateTime $date_arrivee;

    #[MongoDB\Field(type: "float")] // Sur 5 
    private float $note;

    #[MongoDB\Field(type: "string")] // Facultatif
    private string $commentaire;

    #[MongoDB\Field(type: "bool")]
    private bool $signale = false;

    #[MongoDB\Field(type: "string")] // Si $signale == true
    private string $justification;

    #[MongoDB\Field(type: "bool")]
    private bool $validation = false;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUtilisateurIdPassager(): ?int
    {
        return $this->utilisateur_id_passager;
    }

    public function setUtilisateurIdPassager(int $utilisateur_id_passager): self
    {
        $this->utilisateur_id_passager = $utilisateur_id_passager;
        return $this;
    }

    public function getPseudoPassager(): ?string
    {
        return $this->pseudo_passager;
    }

    public function setPseudoPassager(string $pseudo_passager): self
    {
        $this->pseudo_passager = $pseudo_passager;
        return $this;
    }

    public function getCovoiturageId(): ?int
    {
        return $this->covoiturage_id;
    }

    public function setCovoiturageId(int $covoiturage_id): self
    {
        $this->covoiturage_id = $covoiturage_id;
        return $this;
    }

    public function getPseudoConducteur(): ?string
    {
        return $this->pseudo_conducteur;
    }

    public function setPseudoConducteur(string $pseudo_conducteur): self
    {
        $this->pseudo_conducteur = $pseudo_conducteur;
        return $this;
    }

    public function getEmailPassager(): ?string
    {
        return $this->email_passager;
    }

    public function setEmailPassager(string $email_passager): self
    {
        $this->email_passager = $email_passager;
        return $this;
    }

    public function getEmailConducteur(): ?string
    {
        return $this->email_conducteur;
    }

    public function setEmailConducteur(string $email_conducteur): self
    {
        $this->email_conducteur = $email_conducteur;
        return $this;
    }

    public function getDateDepart(): \DateTime
    {
        return $this->date_depart;
    }

    public function setDateDepart(\DateTime $date_depart): self
    {
        $this->date_depart = $date_depart;
        return $this;
    }

    public function getDateArrivee(): \DateTime
    {
        return $this->date_arrivee;
    }

    public function setDateArrivee(\DateTime $date_arrivee): self
    {
        $this->date_arrivee = $date_arrivee;
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

    public function getSignale(): bool
    {
        return $this->signale;
    }

    public function setSignale(bool $signale): self
    {
        $this->signale = $signale;
        return $this;
    }

    public function getJustification(): string
    {
        return $this->justification;
    }

    public function setJustification(string $justification): self
    {
        $this->justification = $justification;
        return $this;
    }

    public function getValidation(): bool
    {
        return $this->validation;
    }

    public function setValidation(bool $validation): self
    {
        $this->validation = $validation;
        return $this;
    }
}

