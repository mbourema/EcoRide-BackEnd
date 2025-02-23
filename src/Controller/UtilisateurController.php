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
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;

#[Route('/api/utilisateurs')]
class UtilisateurController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UtilisateurRepository $utilisateurRepository;
    private LimiterInterface $limiter;

    public function __construct(EntityManagerInterface $entityManager, UtilisateurRepository $utilisateurRepository, RateLimiterFactory $anonymousApiLimiter)
    {
        $this->entityManager = $entityManager;
        $this->utilisateurRepository = $utilisateurRepository;
        $this->limiter = $anonymousApiLimiter->create();
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
        if (isset($data['fumeur'])) {
            $utilisateur->setFumeur($data['fumeur']);
        }
        if (isset($data['animal'])) {
            $utilisateur->setAnimal($data['animal']);
        }
        if (isset($data['preference'])) {
            $utilisateur->setPreference($data['preference']);
        }

        // Gestion de l'URL de la photo
        if (isset($data['photo']) && filter_var($data['photo'], FILTER_VALIDATE_URL)) {
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

    // Connexion d'un utilisateur
    #[Route('/connexion', name: 'api_utilisateurs_connexion', methods: ['POST'])]
    public function login(Request $request, UtilisateurRepository $utilisateurRepository): JsonResponse
    {
        $limit = $this->limiter->consume(1);

        if (!$limit->isAccepted()) {
            return new JsonResponse(['message' => 'Trop de tentatives, veuillez réessayer plus tard.'], Response::HTTP_TOO_MANY_REQUESTS);
        }

        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['mdp'])) {
            return new JsonResponse(['message' => 'Informations manquantes'], Response::HTTP_UNAUTHORIZED);
        }

        $utilisateur = $utilisateurRepository->findOneBy(['email' => $data['email']]);

        if (!$utilisateur || !password_verify($data['mdp'], $utilisateur->getMdp())) {
            return new JsonResponse(['message' => 'Email ou mot de passe incorrect'], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse($this->formatUtilisateurGet($utilisateur), Response::HTTP_OK);
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
        if (isset($data['fumeur'])) {
            $utilisateur->setFumeur($data['fumeur']);
        }
        if (isset($data['animal'])) {
            $utilisateur->setAnimal($data['animal']);
        }
        if (isset($data['preference'])) {
            $utilisateur->setPreference($data['preference']);
        }

        // Mise à jour de l'URL de la photo
        if (isset($data['photo']) && filter_var($data['photo'], FILTER_VALIDATE_URL)) {
            $utilisateur->setPhoto($data['photo']);
        }

        // Mise à jour des rôles
        if (isset($data['roles']) && is_array($data['roles'])) {
            // Suppression des anciens rôles
            foreach ($utilisateur->getRole() as $role) {
                $utilisateur->removeRole($role);
            }

            // Ajout des nouveaux rôles
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
            'fumeur' => $utilisateur->getFumeur(),
            'animal' => $utilisateur->getAnimal(),
            'preference' => $utilisateur->getPreference(),
            'api_token' => $utilisateur->getApiToken(),
            'roles' => $utilisateur->getRoles(),
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
            'photo' => $utilisateur->getPhoto(),
            'fumeur' => $utilisateur->getFumeur(),
            'animal' => $utilisateur->getAnimal(),
            'preference' => $utilisateur->getPreference(),
            'api_token' => $utilisateur->getApiToken(),
            'roles' => $utilisateur->getRoles(),
        ];
    }
}





