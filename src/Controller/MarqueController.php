<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Entity\Voiture;
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

        // Validation des données
        if (!isset($data['libelle']) || empty($data['libelle'])) {
            return new JsonResponse(['error' => 'Données manquantes ou libellé vide'], 400);
        }

        // Création d'une nouvelle marque
        $marque = new Marque();
        $marque->setLibelle($data['libelle']);

        try {
            $em->persist($marque);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de l\'ajout de la marque: ' . $e->getMessage()], 500);
        }

        return new JsonResponse(['message' => 'Marque ajoutée avec succès'], 201);
    }

    #[Route('/marque/{id}', methods: ['GET'])]
    public function getMarque(int $id, EntityManagerInterface $em): JsonResponse
    {
        $marque = $em->getRepository(Marque::class)->find($id);

        if (!$marque) {
            return new JsonResponse(['error' => 'Marque non trouvée'], 404);
        }
        // Récupérer les voitures associées à cette marque
        $voitures = array_map(function($voiture) {
            return [
                'voiture_id' => $voiture->getVoitureId(),
                'modele' => $voiture->getModele(),
                // autres informations
            ];
        }, $marque->getVoitures()->toArray());

        return new JsonResponse([
            'marque_id' => $marque->getMarqueId(),
            'libelle' => $marque->getLibelle(),
            'voitures' => $voitures
        ]);
    }
}

