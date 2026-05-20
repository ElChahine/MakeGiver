<?php

namespace App\Controller;

use App\Entity\Projets;
use App\Entity\Utilisateurs;
use App\Entity\Candidatures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpClient\HttpClient;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }

    #[Route('/solutions/{id}', name: 'app_solution_detail', requirements: ['id' => '\d+'])]
    public function solutionDetail(Connection $connection, int $id): Response
    {
        $solution = $connection->fetchAssociative("
            SELECT s.*, u.Nom, u.Prenom, u.Bio_Description, u.Role
            FROM Solutions s
            LEFT JOIN Utilisateurs u ON s.CreateurID = u.UtilisateurID
            WHERE s.SolutionID = ?
        ", [$id]);

        if (!$solution) {
            throw $this->createNotFoundException('Solution introuvable.');
        }

        $fichiers = $connection->fetchAllAssociative("SELECT * FROM Fichiers WHERE SolutionID = ?", [$id]);

        $commentaires = $connection->fetchAllAssociative("
            SELECT c.*, u.Nom, u.Prenom
            FROM Commentaires c
            LEFT JOIN Utilisateurs u ON c.AuteurID = u.UtilisateurID
            WHERE c.SolutionID = ? AND c.Est_Valide = 1
            ORDER BY c.Date_Post DESC
        ", [$id]);

        return $this->render('main/solution_detail.html.twig', [
            'solution'     => $solution,
            'fichiers'     => $fichiers,
            'commentaires' => $commentaires,
        ]);
    }

    #[Route('/solutions', name: 'app_solutions')]
    public function solutions(Connection $connection): Response
    {
        $solutions = $connection->fetchAllAssociative("
            SELECT s.*, u.Nom, u.Prenom 
            FROM Solutions s 
            LEFT JOIN Utilisateurs u ON s.CreateurID = u.UtilisateurID
        ");

        return $this->render('main/solutions.html.twig', [
            'solutions' => $solutions,
        ]);
    }

    #[Route('/solutions/nouvelle', name: 'app_nouvelle_solution', methods: ['GET', 'POST'])]
    public function nouvelleSolution(Connection $connection, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $titre       = $request->request->get('titre');
            $description = $request->request->get('description');
            $materiel    = $request->request->get('materiel');
            $difficulte  = $request->request->get('difficulte', 'Facile');
            $licence     = $request->request->get('licence', 'Creative Commons BY-NC');

            $createurId = $this->getUser() ? $this->getUser()->getId() : 1;

            if ($titre && $description) {
                $connection->executeStatement("
                    INSERT INTO Solutions 
                        (Titre_Solution, Description_Technique, Materiel_Necessaire, Difficulte_Fabrication, Licence, CreateurID, Date_Publication)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ", [$titre, $description, $materiel, $difficulte, $licence, $createurId]);

                $this->addFlash('success', 'Votre solution a été publiée !');
                return $this->redirectToRoute('app_solutions');
            }
        }

        return $this->render('main/solution_form.html.twig');
    }

    // LISTE DES BESOINS
    #[Route('/besoins', name: 'app_besoins')]
    public function besoins(EntityManagerInterface $em): Response
    {
        // On récupère les objets pour que Twig puisse faire besoin.maker.pseudo
        $besoins = $em->getRepository(Projets::class)->findBy([], ['dateCreation' => 'DESC']);
        $makers = $em->getRepository(Utilisateurs::class)->findBy(['role' => 'Maker']);

        return $this->render('main/besoins.html.twig', [
            'besoins' => $besoins,
            'makers'  => $makers,
        ]);
    }

    // --- LA ROUTE QUI MANQUAIT : CRÉATION D'UN BESOIN ---
    #[Route('/besoins/nouveau', name: 'app_nouveau_besoin', methods: ['GET', 'POST'])]
    public function nouveauBesoin(Connection $connection, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $titre       = $request->request->get('titre');
            $description = $request->request->get('description');
            $demandeurId = $this->getUser() ? $this->getUser()->getId() : 1;

            if ($titre && $description) {
                $connection->executeStatement("
                    INSERT INTO Projets (Titre_Besoin, Description_Detaillee, Statut, DemandeurID, Date_Creation) 
                    VALUES (?, ?, 'Ouvert', ?, NOW())
                ", [$titre, $description, $demandeurId]);

                $this->addFlash('success', 'Votre besoin a été publié !');
                return $this->redirectToRoute('app_besoins');
            }
        }

        return $this->render('main/besoin_form.html.twig');
    }

    #[Route('/besoin/valider/{id}', name: 'app_besoin_valider', methods: ['POST'])]
    public function validerLeMaker(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $besoin = $em->getRepository(Projets::class)->find($id);
        $makerId = $request->request->get('maker_id');
        $maker = $em->getRepository(Utilisateurs::class)->find($makerId);

        if ($besoin && $maker) {
            $besoin->setMaker($maker);
            $besoin->setStatut('En cours');
            $em->flush();
            $this->addFlash('success', 'Collaborateur validé !');
        }

        return $this->redirectToRoute('app_besoins');
    }

    #[Route('/agenda', name: 'app_agenda')]
    public function agenda(Connection $connection): Response
    {
        $evenements = $connection->fetchAllAssociative("
            SELECT * FROM Evenements WHERE Date_Debut >= CURDATE() ORDER BY Date_Debut ASC
        ");
        return $this->render('main/agenda.html.twig', ['evenements' => $evenements]);
    }

    #[Route('/fablabs', name: 'app_fablabs')]
    public function fablabs(): Response
    {
        return $this->render('main/fablabs.html.twig', ['labs' => [], 'error' => null, 'total' => 0]);
    }

    #[Route('/recherche', name: 'app_recherche', methods: ['GET'])]
    public function recherche(Connection $connection, Request $request): Response
    {
        $q = trim($request->query->get('q', ''));
        $solutions = [];
        $besoins   = [];

        if ($q !== '') {
            $like = '%' . $q . '%';
            $solutions = $connection->fetchAllAssociative("
                SELECT s.*, u.Nom, u.Prenom FROM Solutions s
                LEFT JOIN Utilisateurs u ON s.CreateurID = u.UtilisateurID
                WHERE s.Titre_Solution LIKE ? OR s.Description_Technique LIKE ? OR s.Materiel_Necessaire LIKE ? OR u.Nom LIKE ? OR u.Prenom LIKE ?
            ", [$like, $like, $like, $like, $like]);

            $besoins = $connection->fetchAllAssociative("
                SELECT p.*, u.Nom, u.Prenom FROM Projets p
                LEFT JOIN Utilisateurs u ON p.DemandeurID = u.UtilisateurID
                WHERE p.Titre_Besoin LIKE ? OR p.Description_Detaillee LIKE ? OR u.Nom LIKE ? OR u.Prenom LIKE ?
            ", [$like, $like, $like, $like]);
        }

        return $this->render('main/recherche.html.twig', [
            'q' => $q, 'solutions' => $solutions, 'besoins' => $besoins, 'total' => count($solutions) + count($besoins)
        ]);
    }

    #[Route('/projets', name: 'app_projets')]
    public function projets(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('main/projets.html.twig');
    }

    #[Route('/debug-fablabs', name: 'app_debug_fablabs')]
    public function debugFablabs(): Response
    {
        $client = HttpClient::create();
        try {
            $response = $client->request('GET', 'https://api.fablabs.io/0/labs.json', ['query' => ['per_page' => 5], 'timeout' => 10]);
            return new Response('<pre>' . $response->getStatusCode() . "\n" . json_encode($response->toArray(false), JSON_PRETTY_PRINT) . '</pre>');
        } catch (\Exception $e) {
            return new Response('<pre>ERREUR : ' . $e->getMessage() . '</pre>');
        }
    }

    // 1. Route pour que le Maker postule
    #[Route('/besoin/postuler/{id}', name: 'app_besoin_postuler')]
    public function postuler(int $id, EntityManagerInterface $em): Response
    {
        $projet = $em->getRepository(Projets::class)->find($id);
        $user = $this->getUser();

        if (!$user) return $this->redirectToRoute('app_connexion');
        if (!$projet) return $this->redirectToRoute('app_besoins');

        // On vérifie si déjà postulé
        $existe = $em->getRepository(Candidatures::class)->findOneBy([
            'projet' => $projet, 
            'maker' => $user
        ]);

        if (!$existe) {
            $candidature = new Candidatures();
            $candidature->setProjet($projet);
            $candidature->setMaker($user);
            $em->persist($candidature);
            $em->flush();
            $this->addFlash('success', 'Aide proposée !');
        }

        return $this->redirectToRoute('app_besoins');
    }
}