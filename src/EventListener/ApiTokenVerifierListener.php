<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenVerifierListener
{
    private array $excludedRoutes = [
        '#^/api/utilisateurs/ajouter$#',
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

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        // Vérifie si l'URL correspond à l'une des routes exclues
        foreach ($this->excludedRoutes as $pattern) {
            if (preg_match($pattern, $path)) {
                return;
            }
        }

        $cookieToken = $request->cookies->get('API_TOKEN');
        $headerToken = $request->headers->get('X-AUTH-TOKEN');

        if (!$cookieToken || !$headerToken || $cookieToken !== $headerToken) {
            $event->setResponse(new JsonResponse(
                ['error' => 'Unauthorized – Token mismatch or missing.'],
                Response::HTTP_UNAUTHORIZED
            ));
        }
    }
}
