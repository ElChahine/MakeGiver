<?php

namespace App\Controller;

use App\Entity\Projets;
use App\Entity\Utilisateurs;
use App\Entity\Candidatures;
use App\Entity\Signalements;
use App\Form\SignalementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MainController extends AbstractController
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }

    #[Route('/membre/{id}', name: 'app_membre_profil', requirements: ['id' => '\d+'])]
    public function membreProfil(int $id, Connection $connection): Response
    {
        $membre = $connection->fetchAssociative("
            SELECT UtilisateurID, Nom, Prenom, pseudo, Role, Region,
                   Bio_Description, Competences_Techniques, Consentement_Public, Date_Inscription
            FROM utilisateurs
            WHERE UtilisateurID = ?
        ", [$id]);

        if (!$membre) {
            throw $this->createNotFoundException('Membre introuvable.');
        }

        return $this->render('main/membre.html.twig', [
            'membre' => $membre,
        ]);
    }

    #[Route('/mentions-legales', name: 'app_mentions_legales')]
    public function mentionsLegales(): Response
    {
        return $this->render('main/legal/mentions_legales.html.twig');
    }

    #[Route('/confidentialite', name: 'app_confidentialite')]
    public function confidentialite(): Response
    {
        return $this->render('main/legal/confidentialite.html.twig');
    }

    #[Route('/cgu', name: 'app_cgu')]
    public function cgu(): Response
    {
        return $this->render('main/legal/cgu.html.twig');
    }

    #[Route('/solutions/{id}', name: 'app_solution_detail', requirements: ['id' => '\d+'])]
    public function solutionDetail(Connection $connection, int $id): Response
    {
        $solution = $connection->fetchAssociative("
            SELECT s.*, u.Nom, u.Prenom, u.Bio_Description, u.Role
            FROM solutions s
            LEFT JOIN utilisateurs u ON s.CreateurID = u.UtilisateurID
            WHERE s.SolutionID = ?
        ", [$id]);

        if (!$solution) {
            throw $this->createNotFoundException('Solution introuvable.');
        }

        $fichiers = $connection->fetchAllAssociative("SELECT * FROM fichiers WHERE SolutionID = ?", [$id]);

        $filtreValide = $this->isGranted('ROLE_ADMIN') ? '' : ' AND c.Est_Valide = 1';

        $commentaires = $connection->fetchAllAssociative("
            SELECT c.*, u.Nom, u.Prenom
            FROM commentaires c
            LEFT JOIN utilisateurs u ON c.AuteurID = u.UtilisateurID
            WHERE c.SolutionID = ?" . $filtreValide . "
            ORDER BY c.Date_Post DESC
        ", [$id]);

        return $this->render('main/solution_detail.html.twig', [
            'solution'     => $solution,
            'fichiers'     => $fichiers,
            'commentaires' => $commentaires,
        ]);
    }

    #[Route('/solutions/{id}/commentaire', name: 'app_solution_commenter', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function commenterSolution(int $id, Request $request, Connection $connection): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $contenu = trim((string) $request->request->get('commentaire'));

        if ($contenu === '') {
            $this->addFlash('error', 'Le commentaire ne peut pas être vide.');
        } elseif (!$this->isCsrfTokenValid('commenter_' . $id, (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton de sécurité invalide, veuillez réessayer.');
        } else {
            $connection->executeStatement("
                INSERT INTO commentaires (Contenu_Texte, Date_Post, Est_Valide, AuteurID, SolutionID)
                VALUES (?, NOW(), 1, ?, ?)
            ", [$contenu, $this->getUser()->getId(), $id]);

            $this->addFlash('success', 'Votre commentaire a été publié.');
        }

        return $this->redirectToRoute('app_solution_detail', ['id' => $id]);
    }

    #[Route('/solutions', name: 'app_solutions')]
    public function solutions(Connection $connection, Request $request): Response
    {
        $difficulte = trim((string) $request->query->get('difficulte', ''));
        $page       = max(1, (int) $request->query->get('page', 1));
        $parPage    = 9;
        $offset     = ($page - 1) * $parPage;

        $where  = '';
        $params = [];
        if ($difficulte !== '') {
            $where    = ' WHERE s.Difficulte_Fabrication = ?';
            $params[] = $difficulte;
        }

        $total = (int) $connection->fetchOne("SELECT COUNT(*) FROM solutions s" . $where, $params);

        $solutions = $connection->fetchAllAssociative("
            SELECT s.*, u.Nom, u.Prenom
            FROM solutions s
            LEFT JOIN utilisateurs u ON s.CreateurID = u.UtilisateurID
            " . $where . "
            ORDER BY s.Date_Publication DESC
            LIMIT " . $parPage . " OFFSET " . $offset . "
        ", $params);

        return $this->render('main/solutions.html.twig', [
            'solutions'  => $solutions,
            'difficulte' => $difficulte,
            'page'       => $page,
            'totalPages' => (int) ceil($total / $parPage),
            'total'      => $total,
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
                    INSERT INTO solutions
                        (Titre_Solution, Description_Technique, Materiel_Necessaire, Difficulte_Fabrication, Licence, CreateurID, Date_Publication)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ", [$titre, $description, $materiel, $difficulte, $licence, $createurId]);

                $this->addFlash('success', 'Votre solution a été publiée !');
                return $this->redirectToRoute('app_solutions');
            }
        }

        return $this->render('main/solution_form.html.twig');
    }

    #[Route('/besoins', name: 'app_besoins')]
    public function besoins(EntityManagerInterface $em, Request $request): Response
    {
        $page    = max(1, (int) $request->query->get('page', 1));
        $parPage = 9;

        $repo   = $em->getRepository(Projets::class);
        $total  = $repo->count([]);
        $besoins = $repo->findBy([], ['dateCreation' => 'DESC'], $parPage, ($page - 1) * $parPage);
        $makers  = $em->getRepository(Utilisateurs::class)->findBy(['role' => 'Maker']);

        $auteurs = [];
        foreach ($em->getRepository(Utilisateurs::class)->findAll() as $u) {
            $auteurs[$u->getId()] = $u;
        }

        return $this->render('main/besoins.html.twig', [
            'besoins'    => $besoins,
            'makers'     => $makers,
            'auteurs'    => $auteurs,
            'page'       => $page,
            'totalPages' => (int) ceil($total / $parPage),
            'total'      => $total,
        ]);
    }

    #[Route('/besoins/nouveau', name: 'app_nouveau_besoin', methods: ['GET', 'POST'])]
    public function nouveauBesoin(Connection $connection, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $titre       = $request->request->get('titre');
            $description = $request->request->get('description');
            $demandeurId = $this->getUser() ? $this->getUser()->getId() : 1;

            if ($titre && $description) {
                $connection->executeStatement("
                    INSERT INTO projets (Titre_Besoin, Description_Detaillee, Statut, DemandeurID, Date_Creation)
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
            SELECT * FROM evenements WHERE Date_Debut >= CURDATE() ORDER BY Date_Debut ASC
        ");
        return $this->render('main/agenda.html.twig', ['evenements' => $evenements]);
    }

    #[Route('/agenda/nouveau', name: 'app_nouvel_evenement', methods: ['GET', 'POST'])]
    public function nouvelEvenement(Connection $connection, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $erreurs = [];
        $valeurs = [
            'titre'       => '',
            'description' => '',
            'date_debut'  => '',
            'date_fin'    => '',
            'type'        => 'Webinaire',
            'lien'        => '',
        ];

        if ($request->isMethod('POST')) {
            $valeurs['titre']       = trim((string) $request->request->get('titre'));
            $valeurs['description'] = trim((string) $request->request->get('description'));
            $valeurs['date_debut']  = (string) $request->request->get('date_debut');
            $valeurs['date_fin']    = (string) $request->request->get('date_fin');
            $valeurs['type']        = trim((string) $request->request->get('type', 'Webinaire'));
            $valeurs['lien']        = trim((string) $request->request->get('lien'));

            if ($valeurs['titre'] === '') {
                $erreurs[] = 'Le titre est obligatoire.';
            }
            if ($valeurs['date_debut'] === '') {
                $erreurs[] = 'La date de début est obligatoire.';
            }

            if ($valeurs['lien'] === '') {
                $erreurs[] = "Le lien vers l'organisateur est obligatoire.";
            } elseif (!filter_var($valeurs['lien'], FILTER_VALIDATE_URL)) {
                $erreurs[] = "Le lien doit être une URL valide (ex : https://...).";
            } elseif (!preg_match('#^https?://#i', $valeurs['lien'])) {
                $erreurs[] = "Le lien doit commencer par http:// ou https://.";
            }

            if ($valeurs['date_debut'] !== '' && $valeurs['date_fin'] !== '' && $valeurs['date_fin'] < $valeurs['date_debut']) {
                $erreurs[] = 'La date de fin ne peut pas précéder la date de début.';
            }

            if (empty($erreurs)) {
                $connection->executeStatement("
                    INSERT INTO evenements (Titre_Event, Description, Date_Debut, Date_Fin, Type, Lien_Externe_Organisateur)
                    VALUES (?, ?, ?, ?, ?, ?)
                ", [
                    $valeurs['titre'],
                    $valeurs['description'] !== '' ? $valeurs['description'] : null,
                    $valeurs['date_debut'],
                    $valeurs['date_fin'] !== '' ? $valeurs['date_fin'] : null,
                    $valeurs['type'] !== '' ? $valeurs['type'] : null,
                    $valeurs['lien'],
                ]);

                $this->addFlash('success', "L'événement a été ajouté à l'agenda !");
                return $this->redirectToRoute('app_agenda');
            }
        }

        return $this->render('main/agenda_form.html.twig', [
            'erreurs' => $erreurs,
            'valeurs' => $valeurs,
        ]);
    }

    #[Route('/signaler', name: 'app_signaler', methods: ['GET', 'POST'])]
    public function signaler(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $signalement = new Signalements();

        if ($type = $request->query->get('type')) {
            $signalement->setTypeContenu($type);
        }
        if ($id = $request->query->get('id')) {
            $signalement->setContenuId((int) $id);
        }

        $form = $this->createForm(SignalementType::class, $signalement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $signalement->setUtilisateur($this->getUser());
            $em->persist($signalement);
            $em->flush();

            $this->addFlash('success', 'Merci, votre signalement a bien été transmis aux modérateurs.');
            return $this->redirectToRoute('app_home');
        }

        $libelles = [
            'Solution'    => 'cette solution',
            'Besoin'      => 'ce besoin',
            'Commentaire' => 'ce commentaire',
            'Evenement'   => 'cet événement',
            'Profil'      => 'ce profil',
        ];
        $cible = $libelles[$signalement->getTypeContenu()] ?? 'ce contenu';

        return $this->render('main/signalement_form.html.twig', [
            'form'  => $form->createView(),
            'cible' => $cible,
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
                SELECT s.*, u.Nom, u.Prenom FROM solutions s
                LEFT JOIN utilisateurs u ON s.CreateurID = u.UtilisateurID
                WHERE s.Titre_Solution LIKE ? OR s.Description_Technique LIKE ? OR s.Materiel_Necessaire LIKE ? OR u.Nom LIKE ? OR u.Prenom LIKE ?
            ", [$like, $like, $like, $like, $like]);

            $besoins = $connection->fetchAllAssociative("
                SELECT p.*, u.Nom, u.Prenom FROM projets p
                LEFT JOIN utilisateurs u ON p.DemandeurID = u.UtilisateurID
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

    #[Route('/fablabs', name: 'app_fablabs')]
    public function fablabs(): Response
    {
        $labs = [];
        $error = null;

        try {
            $response = $this->httpClient->request('GET', 'https://www.fablabs.io/labs.json', [
                'verify_peer' => false,
                'timeout' => 20,
                'headers' => [
                    'User-Agent' => 'MakeGiver Project (Student)',
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $allLabs = $response->toArray();

                $labs = array_filter($allLabs, function($lab) {
                    return isset($lab['country_code']) &&
                           strtoupper($lab['country_code']) === 'FR' &&
                           !empty($lab['latitude']) &&
                           !empty($lab['longitude']);
                });

                $labs = array_values($labs);
            } else {
                $error = "L'API FabLabs.io ne répond pas correctement (Code " . $response->getStatusCode() . ")";
            }
        } catch (\Exception $e) {
            $error = "Erreur de connexion : " . $e->getMessage();
        }

        return $this->render('main/fablabs.html.twig', [
            'labs'  => $labs,
            'error' => $error,
            'total' => count($labs)
        ]);
    }

    #[Route('/besoin/postuler/{id}', name: 'app_besoin_postuler')]
    public function postuler(int $id, EntityManagerInterface $em): Response
    {
        $projet = $em->getRepository(Projets::class)->find($id);
        $user = $this->getUser();

        if (!$user) return $this->redirectToRoute('app_connexion');
        if (!$projet) return $this->redirectToRoute('app_besoins');

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