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

        // Vérification que les données sont présentes
        if (
            !isset($data['utilisateur_id_passager']) || 
            !isset($data['covoiturage_id']) || 
            !isset($data['note'])
        ) {
            return new JsonResponse(
                ['error' => 'Les champs utilisateur_id_passager, covoiturage_id et note sont obligatoires.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Récupérer l'utilisateur passager depuis MariaDB
        $utilisateur = $this->utilisateurRepository->find($data['utilisateur_id_passager']);
        if (!$utilisateur) {
            return new JsonResponse(
                ['error' => 'Utilisateur non trouvé.'],
                Response::HTTP_NOT_FOUND
            );
        }

        // Récupérer le covoiturage depuis MariaDB
        $covoiturage = $this->covoiturageRepository->find($data['covoiturage_id']);
        if (!$covoiturage) {
            return new JsonResponse(
                ['error' => 'Covoiturage non trouvé.'],
                Response::HTTP_NOT_FOUND
            );
        }

        // Créer l'avis
        $avis = new Avis();
        $avis->setUtilisateurIdPassager($data['utilisateur_id_passager']);
        $avis->setPseudoPassager($utilisateur->getPseudo());
        $avis->setEmailPassager($utilisateur->getEmail());

        $avis->setCovoiturageId($data['covoiturage_id']);
        $avis->setPseudoConducteur($covoiturage->getUtilisateur()->getPseudo());
        $avis->setEmailConducteur($covoiturage->getUtilisateur()->getEmail());

        // Informations supplémentaires venant du covoiturage
        $avis->setDateDepart($covoiturage->getDateDepart());
        $avis->setDateArrivee($covoiturage->getDateArrivee());

        // Note et commentaire
        $avis->setNote($data['note']);
        $avis->setCommentaire($data['commentaire'] ?? '');

        // Signaler ou valider l'avis (dépend de la logique métier)
        $avis->setSignale($data['signale'] ?? false);
        $avis->setJustification($data['justification'] ?? '');
        $avis->setValidation($data['validation'] ?? true);

        // Sauvegarder l'avis dans MongoDB
        $this->documentManager->persist($avis);
        $this->documentManager->flush();

        return new JsonResponse(
            ['message' => 'Avis ajouté avec succès.', 'id' => $avis->getId()],
            Response::HTTP_CREATED
        );
    }

    // Récupérer tous les avis d'un covoiturage
    #[Route('/conducteur/{id}', name: 'api_avis_covoiturage', methods: ['GET'])]
    public function getAvisByCovoiturage(int $id): JsonResponse
    {
        $avisRepository = $this->documentManager->getRepository(Avis::class);

        $avisList = $avisRepository->findBy(['covoiturage_id' => $id]);

        $data = [];
        foreach ($avisList as $avis) {
            $data[] = [
                'id' => $avis->getId(),
                'utilisateur_id_passager' => $avis->getUtilisateurIdPassager(),
                'pseudo_passager' => $avis->getPseudoPassager(),
                'note' => $avis->getNote(),
                'commentaire' => $avis->getCommentaire(),
                'signale' => $avis->getSignale(),
                'validation' => $avis->getValidation(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }
}
