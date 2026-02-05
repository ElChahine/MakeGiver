<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }

    #[Route('/solutions', name: 'app_solutions')]
        public function solutions(Connection $connection): Response
        {
            // On récupère les solutions ET on va chercher le Prénom/Nom de l'auteur dans la table Utilisateurs
            $sql = "
                SELECT s.*, u.Nom, u.Prenom 
                FROM Solutions s 
                LEFT JOIN Utilisateurs u ON s.CreateurID = u.UtilisateurID
            ";
            
            $solutions = $connection->fetchAllAssociative($sql);

            return $this->render('main/solutions.html.twig', [
                'solutions' => $solutions,
            ]);
        }

#[Route('/besoins', name: 'app_besoins', methods: ['GET', 'POST'])] // On autorise le POST
    public function besoins(Connection $connection, Request $request): Response
    {
        // 1. GESTION DE L'ENVOI DU FORMULAIRE
        if ($request->isMethod('POST')) {
            // On récupère les données des champs "name" du formulaire
            $titre = $request->request->get('titre');
            $description = $request->request->get('description');

            if ($titre && $description) {
                // On insère dans la table Projets
                // On met DemandeurID = 1 par défaut pour la démo (Jean Dupont)
                $connection->executeStatement("
                    INSERT INTO Projets (Titre_Besoin, Description_Detaillee, Statut, DemandeurID, Date_Creation) 
                    VALUES (?, ?, 'Ouvert', 1, NOW())
                ", [$titre, $description]);

                // Petit message de succès (optionnel mais pro)
                $this->addFlash('success', 'Votre besoin a été publié !');

                // On redirige vers la même page pour vider le formulaire et voir le résultat
                return $this->redirectToRoute('app_besoins');
            }
        }

        // 2. RÉCUPÉRATION DE LA LISTE (Comme avant)
        $sql = "SELECT p.*, u.Nom, u.Prenom 
                FROM Projets p 
                LEFT JOIN Utilisateurs u ON p.DemandeurID = u.UtilisateurID
                ORDER BY p.Date_Creation DESC"; // Le plus récent en premier
        
        $besoins = $connection->fetchAllAssociative($sql);

        return $this->render('main/besoins.html.twig', [
            'besoins' => $besoins,
        ]);
    }

    #[Route('/agenda', name: 'app_agenda')]
    public function agenda(Connection $connection): Response
    {
        // On récupère les événements et le nom du lieu associé
        $sql = "
            SELECT e.*, l.Nom_Lieu, l.Ville 
            FROM Evenements e 
            LEFT JOIN Lieux l ON e.LieuID = l.LieuID
            WHERE e.Date_Evenement >= CURDATE()
            ORDER BY e.Date_Evenement ASC
        ";
        
        $evenements = $connection->fetchAllAssociative($sql);

        return $this->render('main/agenda.html.twig', [
            'evenements' => $evenements,
        ]);
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
