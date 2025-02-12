<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Entity\Marque;
use App\Entity\Utilisateur;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/voitures')]
class VoitureController extends AbstractController
{
    private $voitureRepository;
    private $entityManager;

    public function __construct(VoitureRepository $voitureRepository, EntityManagerInterface $entityManager)
    {
        $this->voitureRepository = $voitureRepository;
        $this->entityManager = $entityManager;
    }

    // Affichage de la liste des voitures
    #[Route('/liste', name: 'api_voitures_liste', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $voitures = $this->voitureRepository->findAll();

        
        $data = [];
        foreach ($voitures as $voiture) {
            $data[] = $this->transformVoitureToArray($voiture);
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    // Affichage des détails d'une voiture
    #[Route('/details/{id}', name: 'api_voitures_details', methods: ['GET'])]
    public function show(Voiture $voiture): JsonResponse
    {
        $data = $this->transformVoitureToArray($voiture);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    // Création d'une nouvelle voiture
    #[Route('/ajouter', name: 'api_voitures_ajouter', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Récupérer la marque par son ID
        $marque = $this->entityManager->getRepository(Marque::class)->find($data['marque_id']);

        if (!$marque) {
            return new JsonResponse(['error' => 'Marque non trouvée'], Response::HTTP_NOT_FOUND);
        }

        // Récupérer l'utilisateur par son ID
        $utilisateur = $this->entityManager->getRepository(Utilisateur::class)->find($data['utilisateur_id']);

        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        
        $voiture = new Voiture();
        $voiture->setModele($data['modele']);
        $voiture->setImmatriculation($data['immatriculation']);
        $voiture->setEnergie($data['energie']);
        $voiture->setCouleur($data['couleur']);
        $voiture->setDatePremiereImmatriculation(new \DateTimeImmutable($data['date_premiere_immatriculation']));
        $voiture->setNbPlaces($data['nb_places']);
        $voiture->setMarque($marque);
        $voiture->setUtilisateur($utilisateur);

        
        $this->entityManager->persist($voiture);
        $this->entityManager->flush();

        return new JsonResponse($this->transformVoitureToArray($voiture), Response::HTTP_CREATED);
    }

    // Modification d'une voiture existante
    #[Route('/modifier/{id}', name: 'api_voitures_modifier', methods: ['PUT'])]
    public function update(Request $request, Voiture $voiture): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $voiture->setModele($data['modele']);
        $voiture->setImmatriculation($data['immatriculation']);
        $voiture->setEnergie($data['energie']);
        $voiture->setCouleur($data['couleur']);
        $voiture->setNbPlaces($data['nb_places']);

        $this->entityManager->flush();

        return new JsonResponse($this->transformVoitureToArray($voiture), Response::HTTP_OK);
    }

    // Suppression d'une voiture
    #[Route('/supprimer/{id}', name: 'api_voitures_supprimer', methods: ['DELETE'])]
    public function delete(Voiture $voiture): JsonResponse
    {
        $this->entityManager->remove($voiture);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    // Fonction pour transformer une voiture en tableau associatif
    private function transformVoitureToArray(Voiture $voiture): array
    {
        return [
            'id' => $voiture->getVoitureId(), 
            'modele' => $voiture->getModele(),
            'immatriculation' => $voiture->getImmatriculation(),
            'energie' => $voiture->getEnergie(),
            'couleur' => $voiture->getCouleur(),
            'date_premiere_immatriculation' => $voiture->getDatePremiereImmatriculation()->format('Y-m-d'),
            'nb_places' => $voiture->getNbPlaces(),
            'marque' => [
                'id' => $voiture->getMarque()->getMarqueId(), 
                'nom' => $voiture->getMarque()->getLibelle(),
            ],
            'utilisateur' => [
                'id' => $voiture->getUtilisateur()->getUtilisateurId(), 
                'nom' => $voiture->getUtilisateur()->getNom(),
                'email' => $voiture->getUtilisateur()->getEmail(),
            ],
            'covoiturages' => array_map(fn($covoiturage) => [
                'id' => $covoiturage->getCovoiturageId(),
                'date_depart' => $covoiturage->getDateDepart()->format('Y-m-d H:i:s'),
                'lieu_depart' => $covoiturage->getLieuDepart(),
                'lieu_arrivee' => $covoiturage->getLieuArrivee(),
                'places_disponibles' => $covoiturage->getNbPlaces(),
            ], $voiture->getCovoiturages()->toArray()), // Ajout des covoiturages
        ];
    }
}


