<?php

namespace App\Controller;

use App\Entity\Paiement;
use App\Entity\Utilisateur;
use App\Entity\Covoiturage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;

class PaiementController extends AbstractController
{
    #[Route('/paiement/add', methods: ['POST'])]
    public function addPaiement(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérification des données requises
        if (!isset($data['utilisateur_id'], $data['covoiturage_id'])) {
            return new JsonResponse(['error' => 'Données manquantes'], 400);
        }

        // Récupération de l'utilisateur
        $utilisateur = $em->getRepository(Utilisateur::class)->find($data['utilisateur_id']);
        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }

        //Récupération du covoiturage
        $covoiturage = $em->getRepository(Covoiturage::class)->find($data['covoiturage_id']);
        if (!$covoiturage) {
            return new JsonResponse(['error' => 'Covoiturage non trouvé'], 404);
        } 

        // Définir le montant, la date de paiement et l'avancement
        $montant = $covoiturage->getPrixPersonne();
        $date = new \DateTime();  // Utilisation de la date actuelle
        $avancement = "En cours";

        if ($utilisateur->getNbCredit() < $montant){
            return new JsonResponse(['error' => 'Nombre de crédits insuffisant'], 404);
        }
        else{
            $utilisateur->setNbCredit($utilisateur->getNbCredit() - $montant);
            $covoiturage->setNbPlaces($covoiturage->getNbPlaces() - 1);
        }
        // Création du paiement
        $paiement = new Paiement();
        $paiement->setUtilisateurId($utilisateur);
        $paiement->setCovoiturageId($covoiturage);
        $paiement->setMontant($montant);
        $paiement->setDatePaiement($date);
        $paiement->setAvancement($avancement);

        $em->persist($paiement);
        $em->flush();

        return new JsonResponse([
            'message' => 'Paiement ajouté avec succès',
            'paiement_id' => $paiement->getPaiementId()  // Renvoi de l'ID du paiement
        ], 201);
    }
    // Route pour récupérer la liste de tous les paiements
    #[Route('/paiements', methods: ['GET'])]
    public function getAllPaiements(EntityManagerInterface $em): JsonResponse
    {
    // Récupération de tous les paiements
    $paiements = $em->getRepository(Paiement::class)->findAll();

    if (!$paiements) {
        return new JsonResponse(['message' => 'Aucun paiement trouvé'], 404);
    }

    // Préparer la liste des paiements à renvoyer
    $paiementsData = [];
    foreach ($paiements as $paiement) {
        $paiementsData[] = [
            'paiement_id' => $paiement->getPaiementId(),
            'utilisateur_id' => $paiement->getUtilisateurId()->getUtilisateurId(),
            'pseudo_utilisateur' => $paiement->getUtilisateurId()->getPseudo(),
            'covoiturage_id' => $paiement->getCovoiturageId()->getCovoiturageId(),
            'conducteur_id' => $paiement->getCovoiturageId()->getConducteur()->getUtilisateurId(),
            'pseudo_conducteur_id' => $paiement->getCovoiturageId()->getConducteur()->getPseudo(),
            'montant' => $paiement->getMontant(),
            'date_paiement' => $paiement->getDatePaiement()->format('Y-m-d H:i:s'),
            'avancement' => $paiement->getAvancement(),
            'credit_total_plateforme' => $paiement->getCreditTotalPlateforme()
        ];
    }

    // Retourner les paiements sous forme de JSON
    return new JsonResponse($paiementsData, 200);
}

    #[Route('/paiement/confirmation/{id}', methods: ['GET'])]
    public function getPaiement(int $id, EntityManagerInterface $em, MailerInterface $mailer): JsonResponse
    {
        $paiement = $em->getRepository(Paiement::class)->find($id);
        $this->mailer = $mailer;

        if (!$paiement) {
            return new JsonResponse(['error' => 'Paiement non trouvé'], 404);
        }

        $info_paiements=[
            'paiement_id' => $paiement->getPaiementId(),
            'utilisateur_id' => $paiement->getUtilisateurId()->getUtilisateurId(),
            'email_utilisateur_id' => $paiement->getUtilisateurId()->getEmail(),
            'pseudo_utilisateur_id' => $paiement->getUtilisateurId()->getPseudo(),
            'covoiturage_id' => $paiement->getCovoiturageId()->getCovoiturageId(),
            'conducteur_id' => $paiement->getCovoiturageId()->getConducteur()->getUtilisateurId(),
            'pseudo_conducteur_id' => $paiement->getCovoiturageId()->getConducteur()->getPseudo(),
            'montant' => $paiement->getMontant(),
            'date_paiement' => $paiement->getDatePaiement()->format('Y-m-d H:i:s'),
            'avancement' => $paiement->getAvancement(),
        ];

        $covoiturage = $info_paiements['covoiturage_id'];
        $avis_link = 'https://ecoridespaacetree.netlify.app/avis';

        $email = (new TemplatedEmail())
            ->from('spaacetree@gmail.com')
            ->to($info_paiements['email_utilisateur_id'])
            ->subject('Noter et valider votre covoiturage')
            ->htmlTemplate('confirmation_avis.twig')
            ->embedFromPath(
                // chemin physique vers l’image
                \dirname(__DIR__, 2).'/public/images/hero.avif',
                // alias CID utilisé dans le template
                'heroImage'
            )
            ->context([
                'pseudo_utilisateur_id' => $info_paiements['pseudo_utilisateur_id'],
                'pseudo_conducteur_id' => $info_paiements['pseudo_conducteur_id'],
                'date_paiement' => $info_paiements['date_paiement'],
                'link_avis' => $avis_link,
            ]);

        $this->mailer->send($email);

        return new JsonResponse(['message' => 'Email de réinitialisation envoyé'], Response::HTTP_OK);
    }

    // Route PUT pour modifier un paiement
    #[Route('/paiement/{id}', methods: ['PATCH'])]
    public function updatePaiement(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Récupération des données envoyées dans la requête
        $data = json_decode($request->getContent(), true);
    
        // Récupération du paiement existant
        $paiement = $em->getRepository(Paiement::class)->find($id);
        if (!$paiement) {
            return new JsonResponse(['error' => 'Paiement non trouvé'], 404);
        }
    
        // Récupération du covoiturage associé à ce paiement
        $covoiturage = $em->getRepository(Covoiturage::class)->find($paiement->getCovoiturageId());
        if (!$covoiturage) {
            return new JsonResponse(['error' => 'Covoiturage non trouvé'], 404);
        }
    
        // Récupération de l'utilisateur (conducteur) à partir de l'ID du conducteur dans le covoiturage
        $utilisateur = $em->getRepository(Utilisateur::class)->find($covoiturage->getConducteur());
        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Conducteur non trouvé'], 404);
        }
    
        // Mise à jour de l'avancement du paiement, si envoyé dans la requête
        if (isset($data['avancement'])) {
            $paiement->setAvancement($data['avancement']);
        }
    
        // Si l'avancement est "OK", on met à jour le crédit de l'utilisateur
        if ($paiement->getAvancement() == "OK") {
            $utilisateur->setNbCredit($utilisateur->getNbCredit() + ($paiement->getMontant() - 2));
            $paiement->setCreditTotalPlateforme(2);
        }
    
        // Sauvegarde des modifications en base de données
        $em->flush();
    
        // Retour des données mises à jour
        return new JsonResponse([
            'message' => 'Paiement mis à jour avec succès',
            'paiement' => [
                'paiement_id' => $paiement->getPaiementId(),
                'utilisateur_id' => $paiement->getUtilisateurId()->getUtilisateurId(),
                'covoiturage_id' => $paiement->getCovoiturageId()->getCovoiturageId(),
                'montant' => $paiement->getMontant(),
                'date_paiement' => $paiement->getDatePaiement()->format('Y-m-d H:i:s'),
                'avancement' => $paiement->getAvancement(),
            ]
        ], 200);
    }
    
}

