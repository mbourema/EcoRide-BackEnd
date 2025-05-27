<?php

// src/Controller/AvisController.php
namespace App\Controller;

use App\Document\Avis;
use App\Entity\Utilisateur;
use App\Entity\Covoiturage;
use App\Entity\Paiement;
use App\Repository\UtilisateurRepository;
use App\Repository\CovoiturageRepository;
use App\Repository\PaiementRepository;
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
    private PaiementRepository $paiementRepository;

    public function __construct(
        DocumentManager $documentManager,
        UtilisateurRepository $utilisateurRepository,
        CovoiturageRepository $covoiturageRepository,
        PaiementRepository $paiementRepository
    ) {
        $this->documentManager = $documentManager;
        $this->utilisateurRepository = $utilisateurRepository;
        $this->covoiturageRepository = $covoiturageRepository;
        $this->paiementRepository = $paiementRepository;
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

    // Récupérer le paiement associé au covoiturage
    $paiement = $this->paiementRepository->findOneBy([
    'covoiturage_id' => $covoiturage->getCovoiturageId(),
    'utilisateur_id' => $passager->getUtilisateurId()
    ]);

    if (!$paiement) {
        return new JsonResponse(['error' => 'Paiement non trouvé pour ce covoiturage.'], Response::HTTP_NOT_FOUND);
    }

    // Créer l'avis
    $avis = new Avis();
    $avis->setPaiementId($paiement->getPaiementId());
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
    $avis->setAvancement($paiement->getAvancement());

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

    // Récupérer les avis complets à partir de la base de donnée
    #[Route('/fulllist/conducteur/{pseudo}', name: 'api_avis_conducteur_liste_full', methods: ['GET'])]
    public function getFullAvisByConducteur(string $pseudo): JsonResponse
    {
    // Récupérer les avis du conducteur
    $avisList2 = $this->documentManager->getRepository(Avis::class)
        ->findBy(['pseudo_conducteur' => $pseudo]);

    // Vérifier si des avis ont été trouvés
    if (!$avisList2) {
        return new JsonResponse([], Response::HTTP_OK);
    }

        
        $avisArray2 = array_map(function (Avis $avis) {
        
        $avisData2 = [
            'id' => $avis->getId(),
            'paiement_id' => $avis->getPaiementId(),
            'pseudo_passager' => $avis->getPseudoPassager(),
            'covoiturage_id' => $avis->getCovoiturageId(),
            'pseudo_conducteur' => $avis->getPseudoConducteur(),
            'email_conducteur' => $avis->getEmailConducteur(),
            'email_passager' => $avis->getEmailPassager(),
            'date_depart' => $avis->getDateDepart()->format('Y-m-d H:i:s'),
            'date_arrivee' => $avis->getDateArrivee()->format('Y-m-d H:i:s'),
            'note' => $avis->getNote(),
            'validation' => $avis->getValidation(),
            'signale' => $avis->getSignale(),
            'avancement' => $avis->getAvancement(),
        ];

        // Si le commentaire existe, on l'ajoute au tableau
        $commentaire = $avis->getCommentaire();
        if (!empty($commentaire)) {
            $avisData2['commentaire'] = $commentaire;
        }

        // Si le champ justification existe, on l'ajoute au tableau
        $justification = $avis->getJustification();
        if (!empty($justification)) {
            $avisData2['justification'] = $justification;
        }

        return $avisData2;
    }, $avisList2);

    // Retourne la réponse avec les données des avis
    return new JsonResponse($avisArray2, Response::HTTP_OK);
}


    // Récupérer tous les avis d'un conducteur via son pseudo
    #[Route('/fulllist', name: 'api_avis_fullist', methods: ['GET'])]
    public function getAllAvis(): JsonResponse
    {
    $avisList = $this->documentManager->getRepository(Avis::class)->findAll();

    if (!$avisList) {
        return new JsonResponse(
            ['error' => 'Aucun avis trouvé.'],
            Response::HTTP_NOT_FOUND
        );
    }

    $avisArray = array_map(function (Avis $avis) {
        // Crée d'abord le tableau avec les données de base de l'avis
        $avisData = [
            'id' => $avis->getId(),
            'paiement_id' => $avis->getPaiementId(),
            'pseudo_passager' => $avis->getPseudoPassager(),
            'covoiturage_id' => $avis->getCovoiturageId(),
            'pseudo_conducteur' => $avis->getPseudoConducteur(),
            'email_conducteur' => $avis->getEmailConducteur(),
            'email_passager' => $avis->getEmailPassager(),
            'date_depart' => $avis->getDateDepart()->format('Y-m-d H:i:s'),
            'date_arrivee' => $avis->getDateArrivee()->format('Y-m-d H:i:s'),
            'note' => $avis->getNote(),
            'signalement' => $avis->getSignale()
        ];

        // Si le commentaire existe, on l'ajoute au tableau
        $commentaire = $avis->getCommentaire();
        if (!empty($commentaire)) {
            $avisData['commentaire'] = $commentaire;
        }
        $justification = $avis->getJustification();
        // Si le champ justification existe, on l'ajoute au tableau
        if (!empty($justification)) {
            $avisData['justification'] = $justification;
        }

        return $avisData;
    }, $avisList);

    return new JsonResponse($avisArray, Response::HTTP_OK);
    }
}


