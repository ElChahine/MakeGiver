<?php

namespace App\Entity;

use App\Repository\SignalementsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SignalementsRepository::class)]
#[ORM\Table(name: 'signalements')]
class Signalements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'SignalementID')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(name: 'UtilisateurID', referencedColumnName: 'UtilisateurID', nullable: true)]
    private ?Utilisateurs $utilisateur = null;

    #[ORM\Column(name: 'TypeContenu', length: 50, nullable: true)]
    private ?string $typeContenu = null;

    #[ORM\Column(name: 'ContenuID', nullable: true)]
    private ?int $contenuId = null;

    #[ORM\Column(name: 'Raison', type: Types::TEXT, nullable: true)]
    private ?string $raison = null;

    #[ORM\Column(name: 'Date_Signalement', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateSignalement = null;

    public function __construct()
    {
        $this->dateSignalement = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getUtilisateur(): ?Utilisateurs { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateurs $utilisateur): static { $this->utilisateur = $utilisateur; return $this; }

    public function getTypeContenu(): ?string { return $this->typeContenu; }
    public function setTypeContenu(?string $typeContenu): static { $this->typeContenu = $typeContenu; return $this; }

    public function getContenuId(): ?int { return $this->contenuId; }
    public function setContenuId(?int $contenuId): static { $this->contenuId = $contenuId; return $this; }

    public function getRaison(): ?string { return $this->raison; }
    public function setRaison(?string $raison): static { $this->raison = $raison; return $this; }

    public function getDateSignalement(): ?\DateTimeInterface { return $this->dateSignalement; }
    public function setDateSignalement(\DateTimeInterface $dateSignalement): static { $this->dateSignalement = $dateSignalement; return $this; }
}
