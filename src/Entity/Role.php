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
    #[ORM\GeneratedValue(strategy: 'NONE')] // Désactive l'auto-incrémentation pour role_id
    #[ORM\Column(type: 'integer')]
    private ?int $role_id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $libelle = null;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, mappedBy: 'roles')]
    private Collection $utilisateurs;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection(); // Initialiser la collection
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

    // Méthode pour pré-initialiser les rôles en base de données
    public static function initializeRoles(EntityManagerInterface $em): void
    {
        // Récupérer les rôles existants, s'il y en a
        $existingRoles = $em->getRepository(Role::class)->findAll();

        // Si les rôles sont déjà initialisés, ne rien faire
        if (count($existingRoles) > 0) {
            return;
        }

        // Initialiser les rôles fixes
        $rolesData = [
            ['role_id' => 1, 'libelle' => 'Admin'],
            ['role_id' => 2, 'libelle' => 'Employe'],
            ['role_id' => 3, 'libelle' => 'Conducteur'],
            ['role_id' => 4, 'libelle' => 'Passager'],
        ];

        foreach ($rolesData as $data) {
            $role = new Role();
            $role->role_id = $data['role_id'];
            $role->libelle = $data['libelle'];

            $em->persist($role);
        }

        // Sauvegarder en base de données
        $em->flush();
    }
}

