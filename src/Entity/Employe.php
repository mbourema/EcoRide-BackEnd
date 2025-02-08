<?php

namespace App\Entity;

use App\Repository\EmployeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: EmployeRepository::class)]
class Employe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(length: 255)]
    private ?string $pseudo_employe = null;

    #[ORM\OneToMany(mappedBy: "employe_id", targetEntity: Suspension::class, cascade: ["remove"])]
    private Collection $suspensions;

    public function __construct()
    {
    $this->suspensions = new ArrayCollection();
    }

    public function getEmployeId(): ?int
    {
        return $this->id;
    }

    public function getPseudoEmploye(): ?string
    {
        return $this->pseudo_employe;
    }

    public function setPseudoEmploye(string $pseudo_employe): static
    {
        $this->pseudo_employe=$pseudo_employe;
        return $this;
    }
}
