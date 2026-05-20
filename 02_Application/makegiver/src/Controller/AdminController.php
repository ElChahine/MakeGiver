<?php

namespace App\Controller;

use App\Entity\Projets;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/statistiques', name: 'app_admin_stats')]
    public function stats(EntityManagerInterface $em): Response
    {
        // 1. Statistiques Globales
        // On compte les lignes dans la table projets (Besoins)
        $nbBesoins = $em->getRepository(Projets::class)->count([]);
        
        // On compte les membres
        $nbMembres = $em->getRepository(Utilisateurs::class)->count([]);

        // Pour les solutions, on utilise une requête SQL directe car la table est en minuscules
        $nbSolutions = $em->getConnection()->fetchOne("SELECT COUNT(*) FROM solutions"); 

        // 2. Top 3 des contributeurs (Utilisateurs avec le plus de besoins postés)
        // J'utilise 'DemandeurID' car c'est le nom dans ton fichier SQL pour la table projets
        $topContributeurs = $em->getConnection()->fetchAllAssociative("
            SELECT u.pseudo, u.Nom, u.Prenom, COUNT(p.ProjetID) as nb_projets 
            FROM utilisateurs u
            LEFT JOIN projets p ON u.UtilisateurID = p.DemandeurID
            GROUP BY u.UtilisateurID, u.pseudo, u.Nom, u.Prenom
            ORDER BY nb_projets DESC
            LIMIT 3
        ");

        return $this->render('admin/statistiques.html.twig', [
            'nbBesoins' => $nbBesoins,
            'nbSolutions' => $nbSolutions,
            'nbMembres' => $nbMembres,
            'topContributeurs' => $topContributeurs,
        ]);
    }
}