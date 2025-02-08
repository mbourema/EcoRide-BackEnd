<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Entity\Marque;
use App\Repository\VoitureRepository;
use App\Form\VoitureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/voitures')]
class VoitureController extends AbstractController
{
    private $voitureRepository;
    private $entityManager;
    private $serializer;

    public function __construct(VoitureRepository $voitureRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->voitureRepository = $voitureRepository;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    // Affichage de la liste des voitures
    #[Route('/liste', name: 'api_voitures_liste', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $voitures = $this->voitureRepository->findAll();

        // Sérialiser la liste des voitures en JSON
        $data = $this->serializer->normalize($voitures, null, ['groups' => 'voiture:read']);
        
        return new JsonResponse($data, Response::HTTP_OK);
    }

    // Affichage des détails d'une voiture
    #[Route('/details/{id}', name: 'api_voitures_details', methods: ['GET'])]
    public function show(Voiture $voiture): JsonResponse
    {
        // Sérialiser la voiture en JSON
        $data = $this->serializer->normalize($voiture, null, ['groups' => 'voiture:read']);
        
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

        // Créer la voiture
        $voiture = new Voiture();
        $voiture->setModele($data['modele']);
        $voiture->setImmatriculation($data['immatriculation']);
        $voiture->setEnergie($data['energie']);
        $voiture->setCouleur($data['couleur']);
        $voiture->setDatePremiereImmatriculation(new \DateTimeImmutable($data['date_premiere_immatriculation']));
        $voiture->setNbPlaces($data['nb_places']);

        // Associer la marque à la voiture
        $voiture->setMarque($marque);

        // Si l'utilisateur est connecté, l'associer
        $utilisateur = $this->getUser();
        if ($utilisateur) {
            $voiture->setUtilisateur($utilisateur);
        }

        // Sauvegarder la voiture dans la base de données
        $this->entityManager->persist($voiture);
        $this->entityManager->flush();

        // Retourner une réponse JSON avec la voiture créée
        return new JsonResponse(
            $this->serializer->normalize($voiture, null, ['groups' => 'voiture:read']),
            Response::HTTP_CREATED
        );
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
        $voiture->setDatePremiereImmatriculation(new \DateTime($data['date_premiere_immatriculation']));
        $voiture->setNbPlaces($data['nb_places']);

        $this->entityManager->flush();

        return new JsonResponse(
            $this->serializer->normalize($voiture, null, ['groups' => 'voiture:read']),
            Response::HTTP_OK
        );
    }

    // Suppression d'une voiture
    #[Route('/supprimer/{id}', name: 'api_voitures_supprimer', methods: ['DELETE'])]
    public function delete(Voiture $voiture): JsonResponse
    {
        $this->entityManager->remove($voiture);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

