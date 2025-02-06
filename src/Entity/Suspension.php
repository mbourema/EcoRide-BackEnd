<?php

namespace App\Entity;

use App\Enum\Sanction;
use App\Repository\SuspensionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuspensionRepository::class)]
class Suspension
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $suspension_id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "utilisateur_id", referencedColumnName: "utilisateur_id", nullable: false)]
    private ?Utilisateur $utilisateur_id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "employe_id", referencedColumnName: "employe_id",nullable: false)]
    private ?Employe $employe_id = null;

    #[ORM\Column(length: 255)]
    private ?string $raison = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(enumType: Sanction::class)]
    private ?Sanction $sanction = Sanction::ACTIF;

    public function getSuspensionId(): ?int
    {
        return $this->suspension_id;
    }

    public function getUtilisateurId(): ?Utilisateur
    {
        return $this->utilisateur_id;
    }

    public function getEmployeId(): ?Employe
    {
        return $this->employe_id;
    }

    public function getRaison(): ?string
    {
        return $this->raison;
    }

    public function setRaison(string $raison): static
    {
        $this->raison = $raison;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(?\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getSanction(): ?Sanction
    {
        return $this->sanction;
    }

    public function setSanction(Sanction $sanction): static
    {
        $this->sanction = $sanction;

        return $this;
    }
}
