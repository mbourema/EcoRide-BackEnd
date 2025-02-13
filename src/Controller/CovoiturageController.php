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
use Symfony\Component\HttpFoundation\Response;

#[Route('/covoiturage')]
class CovoiturageController extends AbstractController
{
    #[Route('/add', name: 'covoiturage_add', methods: ['POST'])]
    public function addCovoiturage(Request $request, EntityManagerInterface $em): JsonResponse
    {
    $data = json_decode($request->getContent(), true);

    if (!isset($data['voiture_id'], $data['conducteur_id'], $data['prix_personne'])) {
        return new JsonResponse(['error' => 'voiture_id, conducteur_id ou prix_personne manquant'], 400);
    }

    // Recherche de la voiture
    $voiture = $em->getRepository(Voiture::class)->find($data['voiture_id']);
    if (!$voiture) {
        return new JsonResponse(['error' => 'Voiture non trouvée'], 404);
    }

    // Recherche du conducteur par ID
    $conducteur = $em->getRepository(Utilisateur::class)->find($data['conducteur_id']);
    if (!$conducteur) {
        return new JsonResponse(['error' => 'Conducteur non trouvé'], 404);
    }

    // Recherche de l'utilisateur par pseudo et email
    $pseudoConducteur = $em->getRepository(Utilisateur::class)->findOneBy(['pseudo' => $data['pseudo_conducteur']]);
    $emailConducteur = $em->getRepository(Utilisateur::class)->findOneBy(['email' => $data['email_conducteur']]);

    if (!$pseudoConducteur || !$emailConducteur) {
        return new JsonResponse(['error' => 'Invalid pseudo_conducteur or email_conducteur'], 400);
    }

    if ($data['nb_places'] > 0){
        $statut = $data['statut'] ?? 'Disponible';
    }
    else {
        $statut = $data['statut'] ?? 'Indisponible';
    }
    
    // Création du covoiturage
    $covoiturage = new Covoiturage();
    $covoiturage->setVoiture($voiture);
    $covoiturage->setConducteur($conducteur);
    $covoiturage->setPseudo($pseudoConducteur);
    $covoiturage->setEmail($emailConducteur);
    $covoiturage->setLieuDepart($data['lieu_depart']);
    $covoiturage->setLieuArrivee($data['lieu_arrivee']);
    $covoiturage->setDateDepart(new DateTime($data['date_depart']));
    $covoiturage->setDateArrivee(new DateTime($data['date_arrivee']));
    $covoiturage->setNbPlaces($data['nb_places']);
    $covoiturage->setStatut($statut);
    $covoiturage->setPrixPersonne($data['prix_personne']);

    // Optionnel : Gestion de la photo (si présente)
    if (isset($data['photo'])) {
        $covoiturage->setPhoto(base64_decode($data['photo']));
    }

    // Persistance de l'objet Covoiturage dans la base de données
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
        'pseudo_conducteur' => $covoiturage->getPseudo()->getPseudo(),
        'email_conducteur' => $covoiturage->getEmail()->getEmail(),
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
                'pseudo_conducteur' => $covoiturage->getPseudo()->getPseudo(),
                'email_conducteur' => $covoiturage->getEmail()->getEmail(),
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
            'pseudo_conducteur' => $covoiturage->getPseudo()->getPseudo(),
            'email_conducteur' => $covoiturage->getEmail()->getEmail(),
        ], 200);
    }

    #[Route('/delete/{id}', name: 'covoiturage_delete', methods: ['DELETE'])]
    public function deleteCovoiturage(int $id, EntityManagerInterface $em): JsonResponse
    {
    // Recherche du covoiturage par ID
    $covoiturage = $em->getRepository(Covoiturage::class)->find($id);

    // Vérifie si le covoiturage existe
    if (!$covoiturage) {
        return new JsonResponse(['message' => 'Covoiturage introuvable'], Response::HTTP_NOT_FOUND);
    }

    // Suppression du covoiturage
    $em->remove($covoiturage);
    $em->flush();

    // Réponse après suppression réussie
    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}


