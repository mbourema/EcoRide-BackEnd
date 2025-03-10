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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/api/utilisateurs')]
class UtilisateurController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UtilisateurRepository $utilisateurRepository;
    private LimiterInterface $limiter;
    private MailerInterface $mailer;

    public function __construct(EntityManagerInterface $entityManager, UtilisateurRepository $utilisateurRepository, RateLimiterFactory $anonymousApiLimiter, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->utilisateurRepository = $utilisateurRepository;
        $this->limiter = $anonymousApiLimiter->create();
        $this->mailer = $mailer;
    }

    #[Route('/reinistialiser-mot-de-passe', name: 'api_utilisateurs_reinitialiser', methods: ['POST'])]
    public function reinitialiserMDP(Request $request): JsonResponse {
        $data = json_decode($request -> getContent(), true);
        $email = $data['email'] ?? '';

        $utilisateur = $this->utilisateurRepository->findOneBy(['email' => $email]);

        if(!$utilisateur) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $token = bin2hex(random_bytes(20));
        $utilisateur->setResetPasswordToken($token);
        $utilisateur->setResetPasswordTokenExpiration(new \DateTime('+1 hour'));
        $this->entityManager->flush();

        $resetPasswordLink = 'https://ecoridespaacetree.netlify.app/mdp?token=' . urlencode($token);

        $emailMessage = (new Email())
            ->from('spaacetree@gmail.com')
            ->to($utilisateur->getEmail())
            ->subject('Réinitialisation du mot de passe')
            ->text("Bonjour, voici votre lien de réinitialisation du mot de passe : $resetPasswordLink");

        $this->mailer->send($emailMessage);

        return new JsonResponse(['message' => 'Email de réinitialisation envoyé'], Response::HTTP_OK);

    }

    #[Route('/reset-password/{token}', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(string $token, Request $request, UtilisateurRepository $utilisateurRepository): JsonResponse
    {

    $utilisateur = $utilisateurRepository->findOneBy(['resetPasswordToken' => $token]);

    if (!$utilisateur || $utilisateur->getResetPasswordTokenExpiration() < new \DateTime()) {
        return new JsonResponse(['message' => 'Le lien de réinitialisation est invalide ou expiré.'], Response::HTTP_BAD_REQUEST);
    }

    if ($request->isMethod('POST')) {
        $data = json_decode($request->getContent(), true);
        $newPassword = $data['mdp'] ?? '';

        if ($newPassword) {
            $utilisateur->setMdp(password_hash($data['mdp'], PASSWORD_BCRYPT));
            $utilisateur->setResetPasswordToken(null); // Supprimer le token après utilisation
            $utilisateur->setResetPasswordTokenExpiration(null); // Supprimer la date d'expiration
            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Mot de passe réinitialisé avec succès'], Response::HTTP_OK);
        }
    }

    return new JsonResponse(['message' => 'Merci de soumettre un nouveau mot de passe.'], Response::HTTP_OK);
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





