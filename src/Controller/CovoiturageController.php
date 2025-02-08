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
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/covoiturage')]
class CovoiturageController extends AbstractController
{
    #[Route('/add', name: 'covoiturage_add', methods: ['POST'])]
    public function addCovoiturage(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['voiture_id'], $data['conducteur_id'])) {
            return new JsonResponse(['error' => 'voiture_id ou conducteur_id manquant'], 400);
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
        $serializer->deserialize($request->getContent(), Covoiturage::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $covoiturage
        ]);

        $covoiturage->setVoiture($voiture);
        $covoiturage->setConducteur($conducteur);

        if (isset($data['photo'])) {
            $covoiturage->setPhoto(base64_decode($data['photo']));
        }

        $em->persist($covoiturage);
        $em->flush();

        return new JsonResponse($serializer->serialize($covoiturage, 'json', ['groups' => 'covoiturage']), 201, [], true);
    }

    #[Route('/list', name: 'covoiturage_list', methods: ['GET'])]
    public function listCovoiturages(CovoiturageRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $covoiturages = $repo->findAll();
        return new JsonResponse($serializer->serialize($covoiturages, 'json', ['groups' => 'covoiturage']), 200, [], true);
    }

    #[Route('/{id}', name: 'covoiturage_get', methods: ['GET'])]
    public function getCovoiturage(int $id, CovoiturageRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $covoiturage = $repo->find($id);
        if (!$covoiturage) {
            return new JsonResponse(['error' => 'Covoiturage non trouvé'], 404);
        }

        return new JsonResponse($serializer->serialize($covoiturage, 'json', ['groups' => 'covoiturage']), 200, [], true);
    }

    #[Route('/update/{id}', name: 'covoiturage_update', methods: ['PUT'])]
    public function updateCovoiturage(int $id, Request $request, EntityManagerInterface $em, CovoiturageRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $covoiturage = $repo->find($id);
        if (!$covoiturage) {
            return new JsonResponse(['error' => 'Covoiturage non trouvé'], 404);
        }

        $serializer->deserialize($request->getContent(), Covoiturage::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $covoiturage
        ]);

        $data = json_decode($request->getContent(), true);

        if (isset($data['voiture_id'])) {
            $voiture = $em->getRepository(Voiture::class)->find($data['voiture_id']);
            if ($voiture) {
                $covoiturage->setVoiture($voiture);
            }
        }

        if (isset($data['conducteur_id'])) {
            $conducteur = $em->getRepository(Utilisateur::class)->find($data['conducteur_id']);
            if ($conducteur) {
                $covoiturage->setConducteur($conducteur);
            }
        }

        if (isset($data['photo'])) {
            $covoiturage->setPhoto(base64_decode($data['photo']));
        }

        $em->flush();

        return new JsonResponse($serializer->serialize($covoiturage, 'json', ['groups' => 'covoiturage']), 200, [], true);
    }

    #[Route('/delete/{id}', name: 'covoiturage_delete', methods: ['DELETE'])]
    public function deleteCovoiturage(int $id, EntityManagerInterface $em, CovoiturageRepository $repo): JsonResponse
    {
        $covoiturage = $repo->find($id);
        if (!$covoiturage) {
            return new JsonResponse(['error' => 'Covoiturage non trouvé'], 404);
        }

        $em->remove($covoiturage);
        $em->flush();

        return new JsonResponse(['message' => 'Covoiturage supprimé avec succès']);
    }
}
