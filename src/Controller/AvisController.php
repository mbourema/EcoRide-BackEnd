<?php

namespace App\Controller;

use App\Document\Avis;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class AvisController extends AbstractController
{
    #[Route('/avis/add', methods: ['POST'])]
    public function addAvis(Request $request, DocumentManager $dm): JsonResponse
    {
        // Récupération des données du corps de la requête JSON
        $data = json_decode($request->getContent(), true);

        // Vérification des données requises
        if (!isset($data['covoiturage_id'], $data['passager_id'], $data['conducteur_id'], $data['note'], $data['commentaire'])) {
            return new JsonResponse(['error' => 'Donnees manquantes'], 400);
        }

        // Création d'un nouvel avis
        $avis = new Avis();
        $avis->setCovoiturageId($data['covoiturage_id']);
        $avis->setPassagerId($data['passager_id']);
        $avis->setConducteurId($data['conducteur_id']);
        $avis->setNote((float) $data['note']);
        $avis->setCommentaire($data['commentaire']);
        $avis->setDate(new \DateTime());

        // Sauvegarde dans MongoDB
        $dm->persist($avis);
        $dm->flush();

        return new JsonResponse(['message' => 'Avis ajouté avec succès'], 201);
    }

    #[Route('/avis/list', methods: ['GET'])]
    public function listAvis(DocumentManager $dm): JsonResponse
    {
        // Récupération de tous les avis stockés en base MongoDB
        $avisRepository = $dm->getRepository(Avis::class);
        $avisList = $avisRepository->findAll();

        // Conversion des objets en tableaux pour JSON
        $avisArray = array_map(fn($avis) => [
            'id' => $avis->getId(),
            'covoiturage_id' => $avis->getCovoiturageId(),
            'passager_id' => $avis->getPassagerId(),
            'conducteur_id' => $avis->getConducteurId(),
            'note' => $avis->getNote(),
            'commentaire' => $avis->getCommentaire(),
            'date' => $avis->getDate()->format('Y-m-d H:i:s'),
        ], $avisList);

        return new JsonResponse($avisArray);
    }
}