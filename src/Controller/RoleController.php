<?php

namespace App\Controller;

use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RoleController extends AbstractController
{
    #[Route('/role/{id}', methods: ['GET'])]
    public function getRole(int $id, EntityManagerInterface $em): JsonResponse
    {
        $role = $em->getRepository(Role::class)->find($id);

        if (!$role) {
            return new JsonResponse(['error' => 'Rôle non trouvé'], 404);
        }

        return new JsonResponse([
            'role_id' => $role->getRoleId(),
            'libelle' => $role->getLibelle(),
        ]);
    }

    #[Route('/role/list', methods: ['GET'])]
    public function listRoles(EntityManagerInterface $em): JsonResponse
    {
        $roles = $em->getRepository(Role::class)->findAll();

        $roleArray = array_map(fn($role) => [
            'role_id' => $role->getRoleId(),
            'libelle' => $role->getLibelle(),
        ], $roles);

        return new JsonResponse($roleArray);
    }
}
