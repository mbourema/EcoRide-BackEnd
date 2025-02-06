<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Entity\Utilisateur;
use App\Entity\Marque;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VoitureController extends AbstractController
{
    #[Route('/voiture/add', methods: ['POST'])]
    public function addVoiture(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['modele'], $data['immatriculation'], $data['energie'], $data['couleur'], $data['date_premiere_immatriculation'], $data['nb_places'], $data['utilisateur_id'], $data['marque_id'])) {
            return new JsonResponse(['error' => 'Données manquantes'], 400);
        }

        $utilisateur = $em->getRepository(Utilisateur::class)->find($data['utilisateur_id']);
        $marque = $em->getRepository(Marque::class)->find($data['marque_id']);

        if (!$utilisateur || !$marque) {
            return new JsonResponse(['error' => 'Utilisateur ou marque non trouvée'], 404);
        }

        $voiture = new Voiture();
        $voiture->setModele($data['modele']);
        $voiture->setImmatriculation($data['immatriculation']);
        $voiture->setEnergie($data['energie']);
        $voiture->setCouleur($data['couleur']);
        $voiture->setDatePremiereImmatriculation(new \DateTime($data['date_premiere_immatriculation']));
        $voiture->setNbPlaces($data['nb_places']);
        $voiture->utilisateur_id = $utilisateur;
        $voiture->marque_id = $marque;

        $em->persist($voiture);
        $em->flush();

        return new JsonResponse(['message' => 'Voiture ajoutée avec succès'], 201);
    }

    #[Route('/voiture/{id}', methods: ['GET'])]
    public function getVoiture(int $id, EntityManagerInterface $em): JsonResponse
    {
        $voiture = $em->getRepository(Voiture::class)->find($id);

        if (!$voiture) {
            return new JsonResponse(['error' => 'Voiture non trouvée'], 404);
        }

        return new JsonResponse([
            'voiture_id' => $voiture->getVoitureId(),
            'modele' => $voiture->getModele(),
            'immatriculation' => $voiture->getImmatriculation(),
            'energie' => $voiture->getEnergie(),
            'couleur' => $voiture->getCouleur(),
            'date_premiere_immatriculation' => $voiture->getDatePremiereImmatriculation()->format('Y-m-d'),
            'nb_places' => $voiture->getNbPlaces(),
            'utilisateur_id' => $voiture->getUtilisateurId()->getUtilisateurId(),
            'marque_id' => $voiture->getMarqueId()->getMarqueId(),
        ]);
    }
}
