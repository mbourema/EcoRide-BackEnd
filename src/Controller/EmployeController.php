<?php

namespace App\Controller;

use App\Entity\Employe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class EmployeController extends AbstractController
{
    #[Route('/employe/add', methods: ['POST'])]
    public function addEmploye(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['pseudo_employe'])) {
            return new JsonResponse(['error' => 'Données manquantes'], 400);
        }

        // Création d'un nouvel employé
        $employe = new Employe();
        $employe->setPseudoEmploye($data['pseudo_employe']);

        $em->persist($employe);
        $em->flush();

        return new JsonResponse(['message' => 'Employé ajouté avec succès'], 201);
    }

    #[Route('/employe/{id}', methods: ['GET'])]
    public function getEmploye(int $id, EntityManagerInterface $em): JsonResponse
    {
        $employe = $em->getRepository(Employe::class)->find($id);

        if (!$employe) {
            return new JsonResponse(['error' => 'Employé non trouvé'], 404);
        }

        return new JsonResponse([
            'employe_id' => $employe->getEmployeId(),
            'pseudo_employe' => $employe->getPseudoEmploye(),
        ]);
    }
}
