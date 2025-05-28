<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UtilisateurRepository;

class ApiTokenVerifierListener
{
    private array $excludedRoutes = [
        '#^/api/utilisateurs/ajouter/?$#',
        '#^/api/utilisateurs/connexion$#',
        '#^/avis/fulllist/conducteur$#',
        '#^/covoiturage/list$#',
        '#^/marque/\d+$#',
        '#^/paiements/$#',
        '#^/api/utilisateurs/reinistialiser-mot-de-passe$#',
        '#^/api/utilisateurs/reset-password/\d+$#',
        '#^/api/utilisateurs/liste$#',
        '#^/api/utilisateurs/details/\d+$#',
        '#^/api/voitures/details/\d+$#',
    ];

    private UtilisateurRepository $utilisateurRepository;

    public function __construct(UtilisateurRepository $utilisateurRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        foreach ($this->excludedRoutes as $pattern) {
            if (preg_match($pattern, $path)) {
                return;
            }
        }

        $cookieToken = $request->cookies->get('API_TOKEN');
        $headerToken = $request->headers->get('X-AUTH-TOKEN');

        // Cas 1 : tokens header & cookie présents
        if ($cookieToken && $headerToken) {
            if ($cookieToken !== $headerToken) {
                $event->setResponse(new JsonResponse(
                    ['error' => 'Unauthorized – Token mismatch.'],
                    Response::HTTP_UNAUTHORIZED
                ));
                return;
            }
            return; // OK
        }

        // Cas 2 : Cookie absent mais token dans le header (cas Safari)
        if ($headerToken) {
            $utilisateur = $this->utilisateurRepository->findOneBy(['api_token' => $headerToken]);
            if ($utilisateur !== null) {
                return; // OK
            }

            $event->setResponse(new JsonResponse(
                ['error' => 'Unauthorized – Invalid token.'],
                Response::HTTP_UNAUTHORIZED
            ));
            return;
        }

        // Cas 3 : Aucun token fourni
        $event->setResponse(new JsonResponse(
            ['error' => 'Unauthorized – Missing token.'],
            Response::HTTP_UNAUTHORIZED
        ));
    }
}

