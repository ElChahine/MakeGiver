<?php

namespace App\Controller;

use App\Entity\Projets;
use App\Entity\Utilisateurs;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        $nbBesoins = $em->getRepository(Projets::class)->count([]);
        $nbMembres = $em->getRepository(Utilisateurs::class)->count([]);
        $nbSolutions = $em->getConnection()->fetchOne("SELECT COUNT(*) FROM solutions");

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

    #[Route('/signalements', name: 'app_admin_signalements')]
    public function signalements(Connection $connection): Response
    {
        $signalements = $connection->fetchAllAssociative("
            SELECT s.SignalementID, s.TypeContenu, s.ContenuID, s.Raison, s.Date_Signalement,
                   u.pseudo AS pseudo, u.Nom AS nom, u.Prenom AS prenom, u.Email AS email
            FROM signalements s
            LEFT JOIN utilisateurs u ON s.UtilisateurID = u.UtilisateurID
            ORDER BY s.Date_Signalement DESC
        ");

        return $this->render('admin/signalements.html.twig', [
            'signalements' => $signalements,
        ]);
    }

    #[Route('/signalement/{id}/supprimer', name: 'app_admin_signalement_supprimer', methods: ['POST'])]
    public function supprimerSignalement(int $id, Request $request, Connection $connection): Response
    {
        if ($this->isCsrfTokenValid('supprimer_signalement_' . $id, (string) $request->request->get('_token'))) {
            $connection->executeStatement("DELETE FROM signalements WHERE SignalementID = ?", [$id]);
            $this->addFlash('success', 'Signalement supprimé.');
        }

        return $this->redirectToRoute('app_admin_signalements');
    }

    #[Route('/solution/{id}/supprimer', name: 'app_admin_solution_supprimer', methods: ['POST'])]
    public function supprimerSolution(int $id, Request $request, Connection $connection): Response
    {
        if ($this->isCsrfTokenValid('supprimer_solution_' . $id, (string) $request->request->get('_token'))) {
            $connection->transactional(function (Connection $conn) use ($id) {
                $conn->executeStatement("DELETE FROM commentaires WHERE SolutionID = ?", [$id]);
                $conn->executeStatement("DELETE FROM fichiers WHERE SolutionID = ?", [$id]);
                $conn->executeStatement("DELETE FROM solutions WHERE SolutionID = ?", [$id]);
            });
            $this->addFlash('success', 'Solution supprimée.');
        }

        return $this->redirectToRoute('app_solutions');
    }

    #[Route('/projet/{id}/supprimer', name: 'app_admin_projet_supprimer', methods: ['POST'])]
    public function supprimerProjet(int $id, Request $request, Connection $connection): Response
    {
        if ($this->isCsrfTokenValid('supprimer_projet_' . $id, (string) $request->request->get('_token'))) {
            $connection->transactional(function (Connection $conn) use ($id) {
                $conn->executeStatement("DELETE FROM candidatures WHERE projet_id = ?", [$id]);
                $conn->executeStatement("DELETE FROM commentaires WHERE ProjetID = ?", [$id]);
                $conn->executeStatement("DELETE FROM fichiers WHERE ProjetID = ?", [$id]);
                $conn->executeStatement("DELETE FROM membresprojet WHERE ProjetID = ?", [$id]);
                $conn->executeStatement("DELETE FROM projets WHERE ProjetID = ?", [$id]);
            });
            $this->addFlash('success', 'Besoin supprimé.');
        }

        return $this->redirectToRoute('app_besoins');
    }

    #[Route('/evenement/{id}/supprimer', name: 'app_admin_evenement_supprimer', methods: ['POST'])]
    public function supprimerEvenement(int $id, Request $request, Connection $connection): Response
    {
        if ($this->isCsrfTokenValid('supprimer_evenement_' . $id, (string) $request->request->get('_token'))) {
            $connection->executeStatement("DELETE FROM evenements WHERE EvenementID = ?", [$id]);
            $this->addFlash('success', 'Événement supprimé.');
        }

        return $this->redirectToRoute('app_agenda');
    }

    #[Route('/commentaire/{id}/basculer', name: 'app_admin_commentaire_basculer', methods: ['POST'])]
    public function basculerCommentaire(int $id, Request $request, Connection $connection): Response
    {
        if ($this->isCsrfTokenValid('moderer_commentaire_' . $id, (string) $request->request->get('_token'))) {
            $connection->executeStatement("UPDATE commentaires SET Est_Valide = 1 - Est_Valide WHERE CommentaireID = ?", [$id]);
            $this->addFlash('success', 'Visibilité du commentaire mise à jour.');
        }

        $referer = $request->headers->get('referer');
        return $referer ? $this->redirect($referer) : $this->redirectToRoute('app_home');
    }

    #[Route('/commentaire/{id}/supprimer', name: 'app_admin_commentaire_supprimer', methods: ['POST'])]
    public function supprimerCommentaire(int $id, Request $request, Connection $connection): Response
    {
        if ($this->isCsrfTokenValid('moderer_commentaire_' . $id, (string) $request->request->get('_token'))) {
            $connection->executeStatement("DELETE FROM commentaires WHERE CommentaireID = ?", [$id]);
            $this->addFlash('success', 'Commentaire supprimé.');
        }

        $referer = $request->headers->get('referer');
        return $referer ? $this->redirect($referer) : $this->redirectToRoute('app_home');
    }
}