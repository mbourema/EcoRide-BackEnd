<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DebugController extends AbstractController
{
    #[Route('/debug-test', name: 'debug_test')]
    public function index(): Response
    {
        return new Response('<html><body><h1>Test Debug Toolbar</h1></body></html>');
    }
}
