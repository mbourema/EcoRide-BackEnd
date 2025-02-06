<?php

namespace App\Controller;

use App\Document\Commande;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    #[Route('/commande/add', methods: ['POST'])]
    public function addCommande(Request $request, DocumentManager $dm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['conducteur_id'], $data['passager_id'], $data['covoiturage_id'], $data['lieu_depart'], $data['lieu_arrivee'], $data['date_depart'], $data['date_arrivee'], $data['statut'])) {
            return new JsonResponse(['error' => 'Données manquantes'], 400);
        }

        $commande = new Commande();
        $commande->setConducteurId($data['conducteur_id']);
        $commande->setPassagerId($data['passager_id']);
        $commande->setCovoiturageId($data['covoiturage_id']);
        $commande->setLieuDepart($data['lieu_depart']);
        $commande->setLieuArrivee($data['lieu_arrivee']);
        $commande->setDateDepart(new \DateTime($data['date_depart']));
        $commande->setDateArrivee(new \DateTime($data['date_arrivee']));
        $commande->setStatut($data['statut']);

        $dm->persist($commande);
        $dm->flush();

        return new JsonResponse(['message' => 'Commande ajoutée avec succès'], 201);
    }

    #[Route('/commande/list', methods: ['GET'])]
    public function listCommandes(DocumentManager $dm): JsonResponse
    {
        $commandes = $dm->getRepository(Commande::class)->findAll();

        $commandeArray = array_map(fn($commande) => [
            'id' => $commande->getId(),
            'conducteur_id' => $commande->getConducteurId(),
            'passager_id' => $commande->getPassagerId(),
            'covoiturage_id' => $commande->getCovoiturageId(),
            'lieu_depart' => $commande->getLieuDepart(),
            'lieu_arrivee' => $commande->getLieuArrivee(),
            'date_depart' => $commande->getDateDepart()->format('Y-m-d H:i:s'),
            'date_arrivee' => $commande->getDateArrivee()->format('Y-m-d H:i:s'),
            'statut' => $commande->getStatut()
        ], $commandes);

        return new JsonResponse($commandeArray);
    }

    #[Route('/commande/{id}', methods: ['GET'])]
    public function getCommande(string $id, DocumentManager $dm): JsonResponse
    {
        $commande = $dm->getRepository(Commande::class)->find($id);

        if (!$commande) {
            return new JsonResponse(['error' => 'Commande non trouvée'], 404);
        }

        return new JsonResponse([
            'id' => $commande->getId(),
            'conducteur_id' => $commande->getConducteurId(),
            'passager_id' => $commande->getPassagerId(),
            'covoiturage_id' => $commande->getCovoiturageId(),
            'lieu_depart' => $commande->getLieuDepart(),
            'lieu_arrivee' => $commande->getLieuArrivee(),
            'date_depart' => $commande->getDateDepart()->format('Y-m-d H:i:s'),
            'date_arrivee' => $commande->getDateArrivee()->format('Y-m-d H:i:s'),
            'statut' => $commande->getStatut()
        ]);
    }
}
