<?php

namespace App\Controller;

use App\Document\Preferences;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PreferencesController extends AbstractController
{
    #[Route('/preferences/add', methods: ['POST'])]
    public function addPreferences(Request $request, DocumentManager $dm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['conducteur_id'], $data['fumeur'], $data['animaux_acceptes'])) {
            return new JsonResponse(['error' => 'Données manquantes'], 400);
        }

        $preferences = new Preferences();
        $preferences->setConducteurId($data['conducteur_id']);
        $preferences->setFumeur((bool) $data['fumeur']);
        $preferences->setAnimauxAcceptes((bool) $data['animaux_acceptes']);
        $preferences->setPreferencesPerso($data['preferences_perso'] ?? null);

        $dm->persist($preferences);
        $dm->flush();

        return new JsonResponse(['message' => 'Préférences ajoutées avec succès'], 201);
    }

    #[Route('/preferences/{conducteurId}', methods: ['GET'])]
    public function getPreferences(string $conducteurId, DocumentManager $dm): JsonResponse
    {
        $preferences = $dm->getRepository(Preferences::class)->findOneBy(['conducteurId' => $conducteurId]);

        if (!$preferences) {
            return new JsonResponse(['error' => 'Préférences non trouvées'], 404);
        }

        return new JsonResponse([
            'id' => $preferences->getId(),
            'conducteur_id' => $preferences->getConducteurId(),
            'fumeur' => $preferences->isFumeur(),
            'animaux_acceptes' => $preferences->isAnimauxAcceptes(),
            'preferences_perso' => $preferences->getPreferencesPerso(),
        ]);
    }

    #[Route('/preferences/{conducteurId}/update', methods: ['PUT'])]
    public function updatePreferences(string $conducteurId, Request $request, DocumentManager $dm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $preferences = $dm->getRepository(Preferences::class)->findOneBy(['conducteurId' => $conducteurId]);

        if (!$preferences) {
            return new JsonResponse(['error' => 'Préférences non trouvées'], 404);
        }

        if (isset($data['fumeur'])) {
            $preferences->setFumeur((bool) $data['fumeur']);
        }
        if (isset($data['animaux_acceptes'])) {
            $preferences->setAnimauxAcceptes((bool) $data['animaux_acceptes']);
        }
        if (isset($data['preferences_perso'])) {
            $preferences->setPreferencesPerso($data['preferences_perso']);
        }

        $dm->flush();

        return new JsonResponse(['message' => 'Préférences mises à jour avec succès']);
    }
}
