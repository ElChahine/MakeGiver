<?php

namespace App\Entity;

use App\Repository\ProjetsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjetsRepository::class)]
#[ORM\Table(name: 'projets')]
class Projets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ProjetID')]
    private ?int $id = null;

    #[ORM\Column(name: 'Titre_Besoin', length: 100, nullable: true)]
    private ?string $titreBesoin = null;

    #[ORM\Column(name: 'Description_Detaillee', type: Types::TEXT, nullable: true)]
    private ?string $descriptionDetaillee = null;

    #[ORM\Column(name: 'Statut', length: 50)]
    private ?string $statut = 'Ouvert';

    #[ORM\Column(name: 'Date_Creation', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(name: 'DemandeurID', nullable: true)]
    private ?int $demandeurId = null;

    // Relation avec le Maker final (celui qui est validé)
    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(name: 'maker_id', referencedColumnName: 'UtilisateurID')]
    private ?Utilisateurs $maker = null;

    // --- AJOUT : Relation avec la liste des volontaires ---
    #[ORM\OneToMany(mappedBy: 'projet', targetEntity: Candidatures::class)]
    private Collection $candidatures;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->candidatures = new ArrayCollection(); // Initialisation obligatoire
    }

    public function getId(): ?int { return $this->id; }

    public function getTitreBesoin(): ?string { return $this->titreBesoin; }
    public function setTitreBesoin(?string $titreBesoin): static { $this->titreBesoin = $titreBesoin; return $this; }

    public function getDescriptionDetaillee(): ?string { return $this->descriptionDetaillee; }
    public function setDescriptionDetaillee(?string $descriptionDetaillee): static { $this->descriptionDetaillee = $descriptionDetaillee; return $this; }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }

    public function getDateCreation(): ?\DateTimeInterface { return $this->dateCreation; }

    public function getDemandeurId(): ?int { return $this->demandeurId; }
    public function setDemandeurId(?int $demandeurId): static { $this->demandeurId = $demandeurId; return $this; }

    public function getMaker(): ?Utilisateurs { return $this->maker; }
    public function setMaker(?Utilisateurs $maker): static { $this->maker = $maker; return $this; }

    // --- AJOUT : Getter pour Twig ---
    /**
     * @return Collection<int, Candidatures>
     */
    public function getCandidatures(): Collection
    {
        return $this->candidatures;
    }
}