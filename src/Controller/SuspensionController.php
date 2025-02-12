<?php

namespace App\Controller;

use App\Entity\Suspension;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

class SuspensionController extends AbstractController
{
    #[Route('/suspension/add', methods: ['POST'])]
    public function addSuspension(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérification de la présence des données nécessaires
        if (!isset($data['utilisateur_id'], $data['raison'], $data['date_debut'], $data['sanction'])) {
            return new JsonResponse(['error' => 'Données manquantes'], 400);
        }

        // Récupérer l'utilisateur en fonction de l'ID
        $utilisateur = $em->getRepository(Utilisateur::class)->find($data['utilisateur_id']);
        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }

        // Création d'une nouvelle suspension
        $suspension = new Suspension();
        $suspension->setUtilisateurId($utilisateur);  // Associer l'objet Utilisateur
        $suspension->setRaison($data['raison']);
        $suspension->setDateDebut(new \DateTime($data['date_debut']));
        $suspension->setDateFin(isset($data['date_fin']) ? new \DateTime($data['date_fin']) : null);
        $suspension->setSanction($data['sanction']);

        // Sauvegarder la suspension
        $em->persist($suspension);
        $em->flush();

        return new JsonResponse(['message' => 'Suspension ajoutée avec succès'], 201);
    }

    #[Route('/suspension/{id}', methods: ['GET'])]
    public function getSuspension(int $id, EntityManagerInterface $em): JsonResponse
    {
        // Récupérer la suspension par ID
        $suspension = $em->getRepository(Suspension::class)->find($id);

        if (!$suspension) {
            return new JsonResponse(['error' => 'Suspension non trouvée'], 404);
        }

        // Retourner les données de la suspension
        return new JsonResponse([
            'suspension_id' => $suspension->getSuspensionId(),
            'utilisateur_id' => $suspension->getUtilisateurId()->getUtilisateurId(),  // Utilisation de l'objet Utilisateur
            'raison' => $suspension->getRaison(),
            'date_debut' => $suspension->getDateDebut()->format('Y-m-d H:i:s'),
            'date_fin' => $suspension->getDateFin() ? $suspension->getDateFin()->format('Y-m-d H:i:s') : null,
            'sanction' => $suspension->getSanction(),
        ]);
    }
}

