<?php

namespace App\Controller;

use App\Entity\Paiement;
use App\Entity\Utilisateur;
use App\Entity\Covoiturage;
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

        // Vérification des données requises
        if (!isset($data['utilisateur_id'], $data['covoiturage_id'], $data['montant'], $data['date_paiement'], $data['avancement'])) {
            return new JsonResponse(['error' => 'Données manquantes'], 400);
        }

        // Récupération de l'utilisateur
        $utilisateur = $em->getRepository(Utilisateur::class)->find($data['utilisateur_id']);
        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }

        $covoiturage = $em->getRepository(Covoiturage::class)->find($data['covoiturage_id']);
        if (!$covoiturage) {
            return new JsonResponse(['error' => 'Covoiturage non trouvé'], 404);
        }

        //Récupération du covoiturage

        // Création du paiement
        $paiement = new Paiement();
        $paiement->setUtilisateurId($utilisateur);
        $paiement->setCovoiturageId($covoiturage);
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
            'covoiturage_id' => $paiement->getCovoiturageId()->getCovoiturageId(),
            'montant' => $paiement->getMontant(),
            'date_paiement' => $paiement->getDatePaiement()->format('Y-m-d H:i:s'),
            'avancement' => $paiement->getAvancement(),
        ]);
    }

    // Route PUT pour modifier un paiement
    #[Route('/paiement/{id}', methods: ['PUT'])]
    public function updatePaiement(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Récupération du paiement existant
        $paiement = $em->getRepository(Paiement::class)->find($id);

        if (!$paiement) {
            return new JsonResponse(['error' => 'Paiement non trouvé'], 404);
        }

        // Récupération des données envoyées dans la requête
        $data = json_decode($request->getContent(), true);

        // Mise à jour des données du paiement
        if (isset($data['montant'])) {
            $paiement->setMontant((float) $data['montant']);
        }
        if (isset($data['avancement'])) {
            $paiement->setAvancement($data['avancement']);
        }

        // Sauvegarde des modifications en base de données
        $em->flush();

        // Retour des données mises à jour
        return new JsonResponse([
            'message' => 'Paiement mis à jour avec succès',
            'paiement' => [
                'paiement_id' => $paiement->getPaiementId(),
                'utilisateur_id' => $paiement->getUtilisateurId()->getUtilisateurId(),
                'covoiturage_id' => $paiement->getCovoiturageId()->getCovoiturageId(),
                'montant' => $paiement->getMontant(),
                'date_paiement' => $paiement->getDatePaiement()->format('Y-m-d H:i:s'),
                'avancement' => $paiement->getAvancement(),
            ]
        ], 200);
    }
}

