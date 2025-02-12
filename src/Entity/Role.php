<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Utilisateur;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $role_id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $libelle = null;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, mappedBy: 'roles')]
    private Collection $utilisateurs;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection(); 
    }

    public function getRoleId(): ?int
    {
        return $this->role_id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        if ($this->role_id) {
            // Si le role_id est déjà défini, on ne peut plus modifier le libellé
            throw new \LogicException('Le libellé ne peut pas être modifié après la création');
        }

        $this->libelle = $libelle;

        return $this;
    }

    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }
}

