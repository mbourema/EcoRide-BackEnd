<?php

namespace App\Controller;

use App\Document\Statistiques;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StatistiquesController extends AbstractController
{
    #[Route('/statistiques/add', methods: ['POST'])]
    public function addStatistiques(Request $request, DocumentManager $dm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nb_covoiturages'], $data['nb_credits_jour'], $data['nb_credits_total'])) {
            return new JsonResponse(['error' => 'Données manquantes'], 400);
        }

        $statistiques = new Statistiques();
        $statistiques->setDate(new \DateTime());
        $statistiques->setNbCovoiturages((int) $data['nb_covoiturages']);
        $statistiques->setNbCreditsJour((int) $data['nb_credits_jour']);
        $statistiques->setNbCreditsTotal((int) $data['nb_credits_total']);

        $dm->persist($statistiques);
        $dm->flush();

        return new JsonResponse(['message' => 'Statistiques ajoutées avec succès'], 201);
    }

    #[Route('/statistiques/latest', methods: ['GET'])]
    public function getLatestStatistiques(DocumentManager $dm): JsonResponse
    {
        $statistiques = $dm->getRepository(Statistiques::class)->findBy([], ['date' => 'DESC'], 1);

        if (!$statistiques) {
            return new JsonResponse(['error' => 'Aucune statistique trouvée'], 404);
        }

        $stats = $statistiques[0];

        return new JsonResponse([
            'id' => $stats->getId(),
            'date' => $stats->getDate()->format('Y-m-d'),
            'nb_covoiturages' => $stats->getNbCovoiturages(),
            'nb_credits_jour' => $stats->getNbCreditsJour(),
            'nb_credits_total' => $stats->getNbCreditsTotal(),
        ]);
    }

    #[Route('/statistiques/update', methods: ['PUT'])]
    public function updateStatistiques(Request $request, DocumentManager $dm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $statistiques = $dm->getRepository(Statistiques::class)->findBy([], ['date' => 'DESC'], 1);

        if (!$statistiques) {
            return new JsonResponse(['error' => 'Aucune statistique trouvée'], 404);
        }

        $stats = $statistiques[0];

        if (isset($data['nb_covoiturages'])) {
            $stats->setNbCovoiturages((int) $data['nb_covoiturages']);
        }
        if (isset($data['nb_credits_jour'])) {
            $stats->setNbCreditsJour((int) $data['nb_credits_jour']);
        }
        if (isset($data['nb_credits_total'])) {
            $stats->setNbCreditsTotal((int) $data['nb_credits_total']);
        }

        $dm->flush();

        return new JsonResponse(['message' => 'Statistiques mises à jour avec succès']);
    }
}
