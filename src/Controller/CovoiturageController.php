<?php

namespace App\Controller;

use App\Entity\Covoiturage;
use App\Entity\Voiture;
use App\Entity\Utilisateur;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

#[Route('/covoiturage')]
class CovoiturageController extends AbstractController
{
    #[Route('/add', name: 'covoiturage_add', methods: ['POST'])]
    public function addCovoiturage(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['voiture_id'], $data['conducteur_id'], $data['statut'], $data['prix_personne'])) {
            return new JsonResponse(['error' => 'voiture_id, conducteur_id, statut ou prix_personne manquant'], 400);
        }

        $voiture = $em->getRepository(Voiture::class)->find($data['voiture_id']);
        if (!$voiture) {
            return new JsonResponse(['error' => 'Voiture non trouvée'], 404);
        }

        $conducteur = $em->getRepository(Utilisateur::class)->find($data['conducteur_id']);
        if (!$conducteur) {
            return new JsonResponse(['error' => 'Conducteur non trouvé'], 404);
        }

        $covoiturage = new Covoiturage();
        $covoiturage->setVoiture($voiture);
        $covoiturage->setConducteur($conducteur);
        $covoiturage->setLieuDepart($data['lieu_depart']);
        $covoiturage->setLieuArrivee($data['lieu_arrivee']);
        $covoiturage->setDateDepart(new DateTime($data['date_depart']));
        $covoiturage->setDateArrivee(new DateTime($data['date_arrivee']));
        $covoiturage->setNbPlaces($data['nb_places']);
        $covoiturage->setStatut($data['statut']);
        $covoiturage->setPrixPersonne($data['prix_personne']);

        if (isset($data['photo'])) {
            $covoiturage->setPhoto(base64_decode($data['photo']));
        }

        $em->persist($covoiturage);
        $em->flush();

        return new JsonResponse([
            'id' => $covoiturage->getCovoiturageId(),
            'lieu_depart' => $covoiturage->getLieuDepart(),
            'lieu_arrivee' => $covoiturage->getLieuArrivee(),
            'date_depart' => $covoiturage->getDateDepart()->format('Y-m-d H:i:s'),
            'date_arrivee' => $covoiturage->getDateArrivee()->format('Y-m-d H:i:s'),
            'nb_places' => $covoiturage->getNbPlaces(),
            'statut' => $covoiturage->getStatut(),
            'prix_personne' => $covoiturage->getPrixPersonne(),
            'voiture_id' => $covoiturage->getVoiture()->getVoitureId(),
            'conducteur_id' => $covoiturage->getConducteur()->getUtilisateurId(),
        ], 201);
    }

    #[Route('/list', name: 'covoiturage_list', methods: ['GET'])]
    public function listCovoiturages(CovoiturageRepository $repo): JsonResponse
    {
        $covoiturages = $repo->findAll();
        $data = [];

        foreach ($covoiturages as $covoiturage) {
            $data[] = [
                'id' => $covoiturage->getCovoiturageId(),
                'lieu_depart' => $covoiturage->getLieuDepart(),
                'lieu_arrivee' => $covoiturage->getLieuArrivee(),
                'date_depart' => $covoiturage->getDateDepart()->format('Y-m-d H:i:s'),
                'date_arrivee' => $covoiturage->getDateArrivee()->format('Y-m-d H:i:s'),
                'nb_places' => $covoiturage->getNbPlaces(),
                'statut' => $covoiturage->getStatut(),
                'prix_personne' => $covoiturage->getPrixPersonne(),
                'voiture_id' => $covoiturage->getVoiture()->getVoitureId(),
                'conducteur_id' => $covoiturage->getConducteur()->getUtilisateurId(),
            ];
        }

        return new JsonResponse($data, 200);
    }

    #[Route('/{id}', name: 'covoiturage_get', methods: ['GET'])]
    public function getCovoiturage(int $id, CovoiturageRepository $repo): JsonResponse
    {
        $covoiturage = $repo->find($id);
        if (!$covoiturage) {
            return new JsonResponse(['error' => 'Covoiturage non trouvé'], 404);
        }

        return new JsonResponse([
            'id' => $covoiturage->getCovoiturageId(),
            'lieu_depart' => $covoiturage->getLieuDepart(),
            'lieu_arrivee' => $covoiturage->getLieuArrivee(),
            'date_depart' => $covoiturage->getDateDepart()->format('Y-m-d H:i:s'),
            'date_arrivee' => $covoiturage->getDateArrivee()->format('Y-m-d H:i:s'),
            'nb_places' => $covoiturage->getNbPlaces(),
            'statut' => $covoiturage->getStatut(),
            'prix_personne' => $covoiturage->getPrixPersonne(),
            'voiture_id' => $covoiturage->getVoiture()->getVoitureId(),
            'conducteur_id' => $covoiturage->getConducteur()->getUtilisateurId(),
        ], 200);
    }

    #[Route('/update/{id}', name: 'covoiturage_update', methods: ['PUT'])]
    public function updateCovoiturage(int $id, Request $request, EntityManagerInterface $em, CovoiturageRepository $repo): JsonResponse
    {
        $covoiturage = $repo->find($id);
        if (!$covoiturage) {
            return new JsonResponse(['error' => 'Covoiturage non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['statut'])) {
            $covoiturage->setStatut($data['statut']);
        }
        if (isset($data['prix_personne'])) {
            $covoiturage->setPrixPersonne($data['prix_personne']);
        }

        $em->flush();

        return new JsonResponse(['message' => 'Covoiturage mis à jour avec succès'], 200);
    }
}


