<?php

namespace App\Controller;

use App\Entity\Marque;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MarqueController extends AbstractController
{
    #[Route('/marque/add', methods: ['POST'])]
    public function addMarque(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['libelle'])) {
            return new JsonResponse(['error' => 'Données manquantes'], 400);
        }

        // Création d'une nouvelle marque
        $marque = new Marque();
        $marque->setLibelle($data['libelle']);

        $em->persist($marque);
        $em->flush();

        return new JsonResponse(['message' => 'Marque ajoutée avec succès'], 201);
    }

    #[Route('/marque/{id}', methods: ['GET'])]
    public function getMarque(int $id, EntityManagerInterface $em): JsonResponse
    {
        $marque = $em->getRepository(Marque::class)->find($id);

        if (!$marque) {
            return new JsonResponse(['error' => 'Marque non trouvée'], 404);
        }

        return new JsonResponse([
            'marque_id' => $marque->getMarqueId(),
            'libelle' => $marque->getLibelle(),
        ]);
    }

    #[Route('/marque/list', methods: ['GET'])]
    public function listMarques(EntityManagerInterface $em): JsonResponse
    {
        $marques = $em->getRepository(Marque::class)->findAll();

        $marqueArray = array_map(fn($marque) => [
            'marque_id' => $marque->getMarqueId(),
            'libelle' => $marque->getLibelle(),
        ], $marques);

        return new JsonResponse($marqueArray);
    }
}
