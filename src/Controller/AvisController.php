<?php

// src/Controller/AvisController.php
namespace App\Controller;

use App\Document\Avis;
use App\Entity\Utilisateur;
use App\Entity\Covoiturage;
use App\Repository\UtilisateurRepository;
use App\Repository\CovoiturageRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/avis')]
class AvisController extends AbstractController
{
    private DocumentManager $documentManager;
    private UtilisateurRepository $utilisateurRepository;
    private CovoiturageRepository $covoiturageRepository;

    public function __construct(
        DocumentManager $documentManager,
        UtilisateurRepository $utilisateurRepository,
        CovoiturageRepository $covoiturageRepository
    ) {
        $this->documentManager = $documentManager;
        $this->utilisateurRepository = $utilisateurRepository;
        $this->covoiturageRepository = $covoiturageRepository;
    }

    // Ajouter un avis
    #[Route('/add', name: 'api_avis_ajouter', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
    $data = json_decode($request->getContent(), true);

    if (!isset($data['utilisateur_id_passager'], $data['covoiturage_id'], $data['note'])) {
        return new JsonResponse(['error' => 'Des champs obligatoires sont manquants'], Response::HTTP_BAD_REQUEST);
    }

    // Récupérer l'utilisateur passager
    $passager = $this->utilisateurRepository->find($data['utilisateur_id_passager']);
    if (!$passager) {
        return new JsonResponse(['error' => 'Utilisateur passager non trouvé.'], Response::HTTP_NOT_FOUND);
    }

    // Récupérer le covoiturage
    $covoiturage = $this->covoiturageRepository->find($data['covoiturage_id']);
    if (!$covoiturage) {
        return new JsonResponse(['error' => 'Covoiturage non trouvé.'], Response::HTTP_NOT_FOUND);
    }

    // Récupérer l'ID du conducteur
    $conducteurId = $covoiturage->getConducteur();
    if (!$conducteurId) {
        return new JsonResponse(['error' => 'Conducteur non trouvé.'], Response::HTTP_NOT_FOUND);
    }

    // Récupérer le conducteur dans la table utilisateur
    $conducteur = $this->utilisateurRepository->find($conducteurId);
    if (!$conducteur) {
        return new JsonResponse(['error' => 'Conducteur introuvable dans la base utilisateur.'], Response::HTTP_NOT_FOUND);
    }

    // Créer l'avis
    $avis = new Avis();
    $avis->setUtilisateurIdPassager($passager->getUtilisateurId());
    $avis->setPseudoPassager($passager->getPseudo());
    $avis->setEmailPassager($passager->getEmail());

    $avis->setCovoiturageId($covoiturage->getCovoiturageId());
    $avis->setPseudoConducteur($conducteur->getPseudo());
    $avis->setEmailConducteur($conducteur->getEmail());  

    $avis->setDateDepart($covoiturage->getDateDepart());
    $avis->setDateArrivee($covoiturage->getDateArrivee());

    $avis->setNote($data['note']);
    $avis->setCommentaire($data['commentaire'] ?? '');

    $avis->setSignale($data['signale'] ?? false);
    $avis->setJustification($data['justification'] ?? '');
    $avis->setValidation($data['validation'] ?? false);

    // Sauvegarde dans MongoDB
    $this->documentManager->persist($avis);
    $this->documentManager->flush();

    return new JsonResponse(['message' => 'Avis ajouté avec succès.', 'id' => $avis->getId()], Response::HTTP_CREATED);
    }

    // Modifier la validation d'un avis à partir de son ID
    #[Route('/update/{id}', name: 'api_avis_modifier', methods: ['PATCH'])]
    public function updateValidation(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['validation'])) {
            return new JsonResponse(
                ['error' => 'Le champ validation est manquant.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $avis = $this->documentManager->getRepository(Avis::class)->find($id);

        if (!$avis) {
            return new JsonResponse(
                ['error' => 'Avis non trouvé.'],
                Response::HTTP_NOT_FOUND
            );
        }

        $avis->setValidation((bool) $data['validation']);
        $this->documentManager->flush();

        return new JsonResponse(
            ['message' => 'Validation de l\'avis mise à jour avec succès.'],
            Response::HTTP_OK
        );
    }

    // Récupérer tous les avis d'un conducteur via son pseudo
    #[Route('/list/conducteur/{pseudo}', name: 'api_avis_conducteur_liste', methods: ['GET'])]
    public function getAvisByConducteur(string $pseudo): JsonResponse
    {
    $avisList = $this->documentManager->getRepository(Avis::class)
        ->findBy(['pseudo_conducteur' => $pseudo]);

    if (!$avisList) {
        return new JsonResponse(
            ['error' => 'Aucun avis trouvé pour ce conducteur.'],
            Response::HTTP_NOT_FOUND
        );
    }

    $avisArray = array_map(function (Avis $avis) {
        // Crée d'abord le tableau avec les données de base de l'avis
        $avisData = [
            'id' => $avis->getId(),
            'pseudo_passager' => $avis->getPseudoPassager(),
            'covoiturage_id' => $avis->getCovoiturageId(),
            'pseudo_conducteur' => $avis->getPseudoConducteur(),
            'email_conducteur' => $avis->getEmailConducteur(),
            'email_passager' => $avis->getEmailPassager(),
            'date_depart' => $avis->getDateDepart()->format('Y-m-d H:i:s'),
            'date_arrivee' => $avis->getDateArrivee()->format('Y-m-d H:i:s'),
            'note' => $avis->getNote(),
        ];

        // Si le commentaire existe, on l'ajoute au tableau
        $commentaire = $avis->getCommentaire();
        if (!empty($commentaire)) {
            $avisData['commentaire'] = $commentaire;
        }

        return $avisData;
    }, $avisList);

    return new JsonResponse($avisArray, Response::HTTP_OK);
    }
}

