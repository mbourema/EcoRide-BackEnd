<?php

namespace App\Controller;

use App\Entity\Suspension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SuspensionController extends AbstractController
{
    #[Route('/suspension/add', methods: ['POST'])]
    public function addSuspension(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['utilisateur_id'], $data['employe_id'], $data['raison'], $data['date_debut'], $data['sanction'])) {
            return new JsonResponse(['error' => 'Données manquantes'], 400);
        }

        // Création d'une nouvelle suspension
        $suspension = new Suspension();
        $suspension->setUtilisateurId($data['utilisateur_id']);
        $suspension->setEmployeId($data['employe_id']);
        $suspension->setRaison($data['raison']);
        $suspension->setDateDebut(new \DateTime($data['date_debut']));
        $suspension->setDateFin(isset($data['date_fin']) ? new \DateTime($data['date_fin']) : null);
        $suspension->setSanction($data['sanction']);

        $em->persist($suspension);
        $em->flush();

        return new JsonResponse(['message' => 'Suspension ajoutée avec succès'], 201);
    }

    #[Route('/suspension/{id}', methods: ['GET'])]
    public function getSuspension(int $id, EntityManagerInterface $em): JsonResponse
    {
        $suspension = $em->getRepository(Suspension::class)->find($id);

        if (!$suspension) {
            return new JsonResponse(['error' => 'Suspension non trouvée'], 404);
        }

        return new JsonResponse([
            'suspension_id' => $suspension->getSuspensionId(),
            'utilisateur_id' => $suspension->getUtilisateurId(),
            'employe_id' => $suspension->getEmployeId(),
            'raison' => $suspension->getRaison(),
            'date_debut' => $suspension->getDateDebut()->format('Y-m-d H:i:s'),
            'date_fin' => $suspension->getDateFin() ? $suspension->getDateFin()->format('Y-m-d H:i:s') : null,
            'sanction' => $suspension->getSanction(),
        ]);
    }

    #[Route('/suspension/list', methods: ['GET'])]
    public function listSuspensions(EntityManagerInterface $em): JsonResponse
    {
        $suspensions = $em->getRepository(Suspension::class)->findAll();

        $suspensionArray = array_map(fn($suspension) => [
            'suspension_id' => $suspension->getSuspensionId(),
            'utilisateur_id' => $suspension->getUtilisateurId(),
            'employe_id' => $suspension->getEmployeId(),
            'raison' => $suspension->getRaison(),
            'date_debut' => $suspension->getDateDebut()->format('Y-m-d H:i:s'),
            'date_fin' => $suspension->getDateFin() ? $suspension->getDateFin()->format('Y-m-d H:i:s') : null,
            'sanction' => $suspension->getSanction(),
        ], $suspensions);

        return new JsonResponse($suspensionArray);
    }
}
