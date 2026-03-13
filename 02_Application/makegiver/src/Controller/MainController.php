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

        // Fichiers liés
        $fichiers = $connection->fetchAllAssociative("
            SELECT * FROM Fichiers WHERE SolutionID = ?
        ", [$id]);

        // Commentaires
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

    // -------------------------------------------------------
    // NOUVELLE ROUTE : formulaire de proposition de solution
    // -------------------------------------------------------
    #[Route('/solutions/nouvelle', name: 'app_nouvelle_solution', methods: ['GET', 'POST'])]
    public function nouvelleSolution(Connection $connection, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $titre       = $request->request->get('titre');
            $description = $request->request->get('description');
            $materiel    = $request->request->get('materiel');
            $difficulte  = $request->request->get('difficulte', 'Facile');
            $licence     = $request->request->get('licence', 'Creative Commons BY-NC');

            // On récupère l'utilisateur connecté (ou ID 1 par défaut pour la démo)
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

    // -------------------------------------------------------
    // BESOINS — liste + formulaire intégré
    // -------------------------------------------------------
    #[Route('/besoins', name: 'app_besoins', methods: ['GET', 'POST'])]
    public function besoins(Connection $connection, Request $request): Response
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

        $sql = "SELECT p.*, u.Nom, u.Prenom 
                FROM Projets p 
                LEFT JOIN Utilisateurs u ON p.DemandeurID = u.UtilisateurID
                ORDER BY p.Date_Creation DESC";

        $besoins = $connection->fetchAllAssociative($sql);

        return $this->render('main/besoins.html.twig', [
            'besoins' => $besoins,
        ]);
    }

    // -------------------------------------------------------
    // NOUVELLE ROUTE : formulaire dédié dépôt de besoin
    // -------------------------------------------------------
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

    #[Route('/agenda', name: 'app_agenda')]
    public function agenda(Connection $connection): Response
    {
        $sql = "
            SELECT *
            FROM Evenements
            WHERE Date_Debut >= CURDATE()
            ORDER BY Date_Debut ASC
        ";
        $evenements = $connection->fetchAllAssociative($sql);

        return $this->render('main/agenda.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    #[Route('/fablabs', name: 'app_fablabs')]
    public function fablabs(): Response
    {
        $labs  = [];
        $error = null;

        try {
            $client = \Symfony\Component\HttpClient\HttpClient::create();

            // Essai 1 : filtre par pays directement
            $response = $client->request('GET', 'https://api.fablabs.io/0/labs.json', [
                'query'   => ['country_code' => 'FR', 'per_page' => 500],
                'timeout' => 10,
                'verify_peer' => false,
                'verify_host' => false,
                'headers' => ['User-Agent' => 'MakeGiver/1.0'],
            ]);

            $data = $response->toArray();
            $labs = $data['labs'] ?? [];

            // Si vide, on pagine manuellement
            if (empty($labs)) {
                for ($page = 1; $page <= 8; $page++) {
                    $r = $client->request('GET', 'https://api.fablabs.io/0/labs.json', [
                        'query'   => ['page' => $page, 'per_page' => 100],
                        'timeout' => 10,
                        'headers' => ['User-Agent' => 'MakeGiver/1.0'],
                    ]);
                    $d = $r->toArray();
                    if (empty($d['labs'])) break;

                    $french = array_filter($d['labs'], fn($l) => strtolower($l['country_code'] ?? '') === 'fr');
                    $labs   = array_merge($labs, array_values($french));

                    if (count($d['labs']) < 100) break;
                }
            }

            // Garder uniquement ceux avec coordonnées valides
            $labs = array_values(array_filter($labs, fn($l) =>
                !empty($l['latitude']) && !empty($l['longitude'])
                && $l['latitude'] != 0 && $l['longitude'] != 0
            ));

        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->render('main/fablabs.html.twig', [
            'labs'  => $labs,
            'error' => $error,
            'total' => count($labs),
        ]);
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
                SELECT s.*, u.Nom, u.Prenom
                FROM Solutions s
                LEFT JOIN Utilisateurs u ON s.CreateurID = u.UtilisateurID
                WHERE s.Titre_Solution LIKE ?
                   OR s.Description_Technique LIKE ?
                   OR s.Materiel_Necessaire LIKE ?
            ", [$like, $like, $like]);

            $besoins = $connection->fetchAllAssociative("
                SELECT p.*, u.Nom, u.Prenom
                FROM Projets p
                LEFT JOIN Utilisateurs u ON p.DemandeurID = u.UtilisateurID
                WHERE p.Titre_Besoin LIKE ?
                   OR p.Description_Detaillee LIKE ?
            ", [$like, $like]);
        }

        return $this->render('main/recherche.html.twig', [
            'q'         => $q,
            'solutions' => $solutions,
            'besoins'   => $besoins,
            'total'     => count($solutions) + count($besoins),
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
    $client = \Symfony\Component\HttpClient\HttpClient::create();
    
    try {
        $response = $client->request('GET', 'https://api.fablabs.io/0/labs.json', [
            'query'   => ['per_page' => 5],
            'timeout' => 10,
        ]);
        
        $status = $response->getStatusCode();
        $data   = $response->toArray(false); // false = pas d'exception sur erreur
        
        return new Response(
            '<pre>' . $status . "\n" . json_encode($data, JSON_PRETTY_PRINT) . '</pre>'
        );
        
    } catch (\Exception $e) {
        return new Response('<pre>ERREUR : ' . $e->getMessage() . '</pre>');
    }
}
}
