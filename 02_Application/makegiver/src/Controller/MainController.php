<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }

    #[Route('/solutions', name: 'app_solutions')]
    public function solutions(): Response
    {
        // On simule des données pour le catalogue (Page 5)
        $solutions = [
            ['titre' => 'Adaptateur Clé', 'tag' => '#Lowtech', 'note' => 4, 'auteur' => 'FabLab Lille'],
            ['titre' => 'Allume-Feu', 'tag' => '#3D', 'note' => 3, 'auteur' => 'Maker59'],
        ];

        return $this->render('main/solutions.html.twig', [
            'solutions' => $solutions
        ]);
    }

    #[Route('/besoins', name: 'app_besoins')]
    public function besoins(): Response
    {
        return $this->render('main/besoins.html.twig');
    }

    #[Route('/agenda', name: 'app_agenda')]
    public function agenda(): Response
    {
        return $this->render('main/agenda.html.twig');
    }

    #[Route('/projets', name: 'app_projets')]
    public function projets(): Response
    {
        return $this->render('main/projets.html.twig');
    }

    #[Route('/connexion', name: 'app_connexion')]
    public function connexion(): Response
    {
        return $this->render('main/connexion.html.twig');  
    }

    #[Route('/inscription', name: 'app_inscription')]
    public function inscription(): Response
    {
        return $this->render('main/inscription.html.twig');
    }
}
