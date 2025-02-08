<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Role;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/utilisateurs')]
class UtilisateurController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(EntityManagerInterface $entityManager, UtilisateurRepository $utilisateurRepository)
    {
        $this->entityManager = $entityManager;
        $this->utilisateurRepository = $utilisateurRepository;
    }

    // Liste des utilisateurs
    #[Route('/liste', name: 'api_utilisateurs_liste', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $utilisateurs = $this->utilisateurRepository->findAll();
        $data = [];

        foreach ($utilisateurs as $utilisateur) {
            $data[] = $this->formatUtilisateurGet($utilisateur);
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    // Détails d'un utilisateur par ID
    #[Route('/details/{id}', name: 'api_utilisateurs_details', methods: ['GET'])]
    public function show(Utilisateur $utilisateur): JsonResponse
    {
        return new JsonResponse($this->formatUtilisateurGet($utilisateur), Response::HTTP_OK);
    }

    // Création d'un utilisateur
    #[Route('/ajouter', name: 'api_utilisateurs_ajouter', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $utilisateur = new Utilisateur();
        $utilisateur->setNom($data['nom']);
        $utilisateur->setPrenom($data['prenom']);
        $utilisateur->setEmail($data['email']);
        $utilisateur->setMdp(password_hash($data['mdp'], PASSWORD_BCRYPT));
        $utilisateur->setTelephone($data['telephone']);
        $utilisateur->setAdresse($data['adresse']);
        $utilisateur->setDateNaissance(new \DateTime($data['date_naissance']));
        $utilisateur->setPseudo($data['pseudo']);

        if (isset($data['photo'])) {
            $utilisateur->setPhoto($data['photo']);
        }

        // Ajout des rôles
        if (isset($data['roles']) && is_array($data['roles'])) {
            foreach ($data['roles'] as $roleNom) {
                $role = $this->entityManager->getRepository(Role::class)->findOneBy(['role_id' => $roleNom]);
                if ($role) {
                    $utilisateur->addRole($role);
                }
            }
        }

        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();

        return new JsonResponse($this->formatUtilisateur($utilisateur), Response::HTTP_CREATED);
    }

    // Modification d'un utilisateur
    #[Route('/modifier/{id}', name: 'api_utilisateurs_modifier', methods: ['PUT'])]
    public function update(Request $request, Utilisateur $utilisateur): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $utilisateur->setNom($data['nom']);
        $utilisateur->setPrenom($data['prenom']);
        $utilisateur->setEmail($data['email']);
        
        if (isset($data['mdp'])) {
            $utilisateur->setMdp(password_hash($data['mdp'], PASSWORD_BCRYPT));
        }

        $utilisateur->setTelephone($data['telephone']);
        $utilisateur->setAdresse($data['adresse']);
        $utilisateur->setDateNaissance(new \DateTime($data['date_naissance']));
        $utilisateur->setPseudo($data['pseudo']);

        if (isset($data['photo'])) {
            $utilisateur->setPhoto($data['photo']);
        }

        // Mise à jour des rôles
        if (isset($data['roles']) && is_array($data['roles'])) {
            // Supprime les anciens rôles
            foreach ($utilisateur->getRoles() as $role) {
                $utilisateur->removeRole($role);
            }

            // Ajoute les nouveaux rôles
            foreach ($data['roles'] as $roleNom) {
                $role = $this->entityManager->getRepository(Role::class)->findOneBy(['role_id' => $roleNom]);
                if ($role) {
                    $utilisateur->addRole($role);
                }
            }
        }

        $this->entityManager->flush();

        return new JsonResponse($this->formatUtilisateur($utilisateur), Response::HTTP_OK);
    }

    // Suppression d'un utilisateur
    #[Route('/supprimer/{id}', name: 'api_utilisateurs_supprimer', methods: ['DELETE'])]
    public function delete(Utilisateur $utilisateur): JsonResponse
    {
        $this->entityManager->remove($utilisateur);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    private function formatUtilisateur(Utilisateur $utilisateur): array
{
    return [
        'id' => $utilisateur->getUtilisateurId(),
        'nom' => $utilisateur->getNom(),
        'prenom' => $utilisateur->getPrenom(),
        'email' => $utilisateur->getEmail(),
        'nbCredit' => $utilisateur->getNbCredit(),
        'telephone' => $utilisateur->getTelephone(),
        'adresse' => $utilisateur->getAdresse(),
        'date_naissance' => $utilisateur->getDateNaissance()->format('Y-m-d'),
        'pseudo' => $utilisateur->getPseudo(),
        'photo' => $utilisateur->getPhoto(),

        // Sérialisation des rôles
        'roles' => $utilisateur->getRoles()->map(fn($role) => $role->getLibelle())->toArray(),

        // Sérialisation des covoiturages
        'covoiturages' => $utilisateur->getCovoiturages()->map(fn($covoiturage) => [
            'id' => $covoiturage->getId(),
            'depart' => $covoiturage->getLieuDepart(),
            'arrivee' => $covoiturage->getLieuArrivee(),
            'date' => $covoiturage->getDate()->format('Y-m-d H:i:s'),
        ])->toArray(),

        // Sérialisation des voitures
        'voitures' => $utilisateur->getVoitures()->map(fn($voiture) => [
            'id' => $voiture->getId(),
            'marque' => $voiture->getMarque(),
            'modele' => $voiture->getModele(),
            'annee' => $voiture->getAnnee(),
        ])->toArray(),
    ];
}
    private function formatUtilisateurGet(Utilisateur $utilisateur): array
    {
        return [
            'id' => $utilisateur->getUtilisateurId(),
            'nom' => $utilisateur->getNom(),
            'prenom' => $utilisateur->getPrenom(),
            'email' => $utilisateur->getEmail(),
            'nbCredit' => $utilisateur->getNbCredit(),
            'telephone' => $utilisateur->getTelephone(),
            'adresse' => $utilisateur->getAdresse(),
            'date_naissance' => $utilisateur->getDateNaissance()->format('Y-m-d'),
            'pseudo' => $utilisateur->getPseudo(),
            'photo' => $utilisateur->getPhoto() ? base64_encode(stream_get_contents($utilisateur->getPhoto())) : null,

            // Sérialisation des rôles
            'roles' => $utilisateur->getRoles()->map(fn($role) => $role->getLibelle())->toArray(),

            // Sérialisation des covoiturages
            'covoiturages' => $utilisateur->getCovoiturages()->map(fn($covoiturage) => [
                'id' => $covoiturage->getId(),
                'depart' => $covoiturage->getLieuDepart(),
                'arrivee' => $covoiturage->getLieuArrivee(),
                'date' => $covoiturage->getDate()->format('Y-m-d H:i:s'),
            ])->toArray(),

            // Sérialisation des voitures
            'voitures' => $utilisateur->getVoitures()->map(fn($voiture) => [
                'id' => $voiture->getId(),
                'marque' => $voiture->getMarque(),
                'modele' => $voiture->getModele(),
                'annee' => $voiture->getAnnee(),
            ])->toArray(),
        ];
    }

}



