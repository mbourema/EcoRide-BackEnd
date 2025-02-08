<?php
// src/Controller/ImageController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends AbstractController
{
    /**
     * Route pour afficher une image depuis le dossier public/uploads/photos
     *
     * @Route("/uploads/photos/{photo}", name="app_image_show")
     */
    public function showImage($photo): BinaryFileResponse
    {
        // Définir le chemin complet du fichier
        $path = $this->getParameter('kernel.project_dir') . '/public/uploads/photos/' . $photo;

        // Vérifie si le fichier existe
        if (!file_exists($path)) {
            throw $this->createNotFoundException('Image not found');
        }

        // Retourne l'image en tant que réponse binaire
        return new BinaryFileResponse($path);
    }
}
