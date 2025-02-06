<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Role;
use App\Entity\Covoiturage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    #[Route('/utilisateur/add', methods: ['POST'])]
    public function addUtilisateur(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom'], $data['prenom'], $data['email'], $data['mdp'], $data['telephone'], $data['adresse'], $data['date_naissance'], $data['pseudo'])) {
            return new JsonResponse(['error' => 'Données manquantes'], 400);
        }

        // Création d'un nouvel utilisateur
        $utilisateur = new Utilisateur();
        $utilisateur->setNom($data['nom']);
        $utilisateur->setPrenom($data['prenom']);
        $utilisateur->setEmail($data['email']);
        $utilisateur->setMdp($data['mdp']);
        $utilisateur->setTelephone($data['telephone']);
        $utilisateur->setAdresse($data['adresse']);
        $utilisateur->setDateNaissance(new \DateTime($data['date_naissance']));
        $utilisateur->setPseudo($data['pseudo']);

        $em->persist($utilisateur);
        $em->flush();

        return new JsonResponse(['message' => 'Utilisateur ajouté avec succès'], 201);
    }

    #[Route('/utilisateur/{id}', methods: ['GET'])]
    public function getUtilisateur(int $id, EntityManagerInterface $em): JsonResponse
    {
        $utilisateur = $em->getRepository(Utilisateur::class)->find($id);

        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }

        return new JsonResponse([
            'utilisateur_id' => $utilisateur->getUtilisateurId(),
            'nom' => $utilisateur->getNom(),
            'prenom' => $utilisateur->getPrenom(),
            'email' => $utilisateur->getEmail(),
            'telephone' => $utilisateur->getTelephone(),
            'adresse' => $utilisateur->getAdresse(),
            'date_naissance' => $utilisateur->getDateNaissance()->format('Y-m-d'),
            'pseudo' => $utilisateur->getPseudo(),
        ]);
    }

    #[Route('/utilisateur/{id}/roles', methods: ['GET'])]
    public function getRolesUtilisateur(int $id, EntityManagerInterface $em): JsonResponse
    {
        $utilisateur = $em->getRepository(Utilisateur::class)->find($id);

        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }

        $roles = $utilisateur->getRoles();

        $rolesArray = array_map(fn($role) => [
            'role_id' => $role->getRoleId(),
            'libelle' => $role->getLibelle(),
        ], $roles->toArray());

        return new JsonResponse($rolesArray);
    }

    #[Route('/utilisateur/{id}/covoiturages', methods: ['GET'])]
    public function getCovoituragesUtilisateur(int $id, EntityManagerInterface $em): JsonResponse
    {
        $utilisateur = $em->getRepository(Utilisateur::class)->find($id);

        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }

        $covoiturages = $utilisateur->getCovoiturages();

        $covoituragesArray = array_map(fn($covoiturage) => [
            'covoiturage_id' => $covoiturage->getCovoiturageId(),
            'date_depart' => $covoiturage->getDateDepart()->format('Y-m-d H:i:s'),
            'lieu_depart' => $covoiturage->getLieuDepart(),
            'lieu_arrivee' => $covoiturage->getLieuArrivee(),
        ], $covoiturages->toArray());

        return new JsonResponse($covoituragesArray);
    }
}
