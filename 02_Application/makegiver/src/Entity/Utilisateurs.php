<?php

namespace App\Entity;

use App\Repository\UtilisateursRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UtilisateursRepository::class)]
#[ORM\Table(name: 'utilisateurs')]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
class Utilisateurs implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'UtilisateurID')]
    private ?int $id = null;

    #[ORM\Column(name: 'Nom', length: 50, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(name: 'Prenom', length: 50, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(name: 'Email', length: 100, unique: true, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(name: 'Telephone', length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(name: 'MotDePasse_Hash', length: 255, nullable: true)]
    private ?string $motDePasseHash = null;

    #[ORM\Column(name: 'Role', type: 'string', columnDefinition: "ENUM('Patient','Maker','Soignant','Admin')", nullable: true)]
    private ?string $role = null;

    #[ORM\Column(name: 'Bio_Description', type: 'text', nullable: true)]
    private ?string $bioDescription = null;

    #[ORM\Column(name: 'Competences_Techniques', type: 'text', nullable: true)]
    private ?string $competencesTechniques = null;

    #[ORM\Column(name: 'Consentement_Public', type: 'boolean', options: ['default' => false])]
    private bool $consentementPublic = false;

    #[ORM\Column(name: 'Date_Inscription', type: 'datetime')]
    private ?\DateTimeInterface $dateInscription = null;

    public function __construct()
    {
        $this->dateInscription = new \DateTime();
    }

    // --- UserInterface ---

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        // Mapping rôle BDD → rôle Symfony
        return match($this->role) {
            'Admin'    => ['ROLE_ADMIN', 'ROLE_USER'],
            'Maker'    => ['ROLE_MAKER', 'ROLE_USER'],
            'Soignant' => ['ROLE_SOIGNANT', 'ROLE_USER'],
            default    => ['ROLE_USER'],
        };
    }

    public function eraseCredentials(): void {}

    // --- PasswordAuthenticatedUserInterface ---

    public function getPassword(): ?string
    {
        return $this->motDePasseHash;
    }

    // --- Getters / Setters ---

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(?string $nom): static { $this->nom = $nom; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(?string $prenom): static { $this->prenom = $prenom; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): static { $this->email = $email; return $this; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $telephone): static { $this->telephone = $telephone; return $this; }

    public function getMotDePasseHash(): ?string { return $this->motDePasseHash; }
    public function setMotDePasseHash(?string $hash): static { $this->motDePasseHash = $hash; return $this; }

    public function getRole(): ?string { return $this->role; }
    public function setRole(?string $role): static { $this->role = $role; return $this; }

    public function getBioDescription(): ?string { return $this->bioDescription; }
    public function setBioDescription(?string $bio): static { $this->bioDescription = $bio; return $this; }

    public function getCompetencesTechniques(): ?string { return $this->competencesTechniques; }
    public function setCompetencesTechniques(?string $comp): static { $this->competencesTechniques = $comp; return $this; }

    public function isConsentementPublic(): bool { return $this->consentementPublic; }
    public function setConsentementPublic(bool $val): static { $this->consentementPublic = $val; return $this; }

    public function getDateInscription(): ?\DateTimeInterface { return $this->dateInscription; }
    public function setDateInscription(\DateTimeInterface $date): static { $this->dateInscription = $date; return $this; }

    public function getNomComplet(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }
}