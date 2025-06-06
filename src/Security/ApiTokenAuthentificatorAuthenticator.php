<?php

namespace App\Security;

use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use App\Repository\UtilisateurRepository;
use App\Repository\RoleRepository;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Doctrine\Common\Collections\ArrayCollection;


class ApiTokenAuthentificatorAuthenticator extends AbstractAuthenticator
{
    private $roleRepository;

    public function __construct(
        private UtilisateurRepository $repository, 
        RoleRepository $roleRepository
    ) {
        $this->roleRepository = $roleRepository;
    }

    public function supports(Request $request): bool
    {
        return $request->cookies->has('API_TOKEN');
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->cookies->get('API_TOKEN');
        if (!$apiToken) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
    }

    $user = $this->repository->findOneBy(['api_token' => $apiToken]);
    
    if (null === $user) {
        throw new UserNotFoundException('User not found');
    }


    $userBadge = new UserBadge($user->getEmail());


    return new SelfValidatingPassport($userBadge);
    }

        

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        
        $data = [
            'message' => 'Authentication failed: ' . $exception->getMessage(),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}






