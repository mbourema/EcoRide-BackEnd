<?php

namespace App\Controller;

use App\Entity\Paiement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaiementController extends AbstractController
{
    #[Route('/paiement/add', methods: ['POST'])]
    public function addPaiement(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['utilisateur_id'], $data['montant'], $data['date_paiement'], $data['avancement'])) {
            return new JsonResponse(['error' => 'Données manquantes'], 400);
        }

        // Création d'un nouveau paiement
        $paiement = new Paiement();
        $paiement->setUtilisateurId($data['utilisateur_id']);
        $paiement->setMontant((float) $data['montant']);
        $paiement->setDatePaiement(new \DateTime($data['date_paiement']));
        $paiement->setAvancement($data['avancement']);

        $em->persist($paiement);
        $em->flush();

        return new JsonResponse(['message' => 'Paiement ajouté avec succès'], 201);
    }

    #[Route('/paiement/{id}', methods: ['GET'])]
    public function getPaiement(int $id, EntityManagerInterface $em): JsonResponse
    {
        $paiement = $em->getRepository(Paiement::class)->find($id);

        if (!$paiement) {
            return new JsonResponse(['error' => 'Paiement non trouvé'], 404);
        }

        return new JsonResponse([
            'paiement_id' => $paiement->getPaiementId(),
            'utilisateur_id' => $paiement->getUtilisateurId()->getUtilisateurId(),
            'montant' => $paiement->getMontant(),
            'date_paiement' => $paiement->getDatePaiement()->format('Y-m-d H:i:s'),
            'avancement' => $paiement->getAvancement(),
        ]);
    }

    #[Route('/paiement/list', methods: ['GET'])]
    public function listPaiements(EntityManagerInterface $em): JsonResponse
    {
        $paiements = $em->getRepository(Paiement::class)->findAll();

        $paiementArray = array_map(fn($paiement) => [
            'paiement_id' => $paiement->getPaiementId(),
            'utilisateur_id' => $paiement->getUtilisateurId()->getUtilisateurId(),
            'montant' => $paiement->getMontant(),
            'date_paiement' => $paiement->getDatePaiement()->format('Y-m-d H:i:s'),
            'avancement' => $paiement->getAvancement(),
        ], $paiements);

        return new JsonResponse($paiementArray);
    }
}
