<?php

namespace App\Entity;



use App\Repository\CandidaturesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidaturesRepository::class)]
class Candidatures
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Projets::class, inversedBy: 'candidatures')]
    #[ORM\JoinColumn(name: 'projet_id', referencedColumnName: 'ProjetID', nullable: false)]
    private ?Projets $projet = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(name: 'maker_id', referencedColumnName: 'UtilisateurID', nullable: false)]
    private ?Utilisateurs $maker = null;

    public function getId(): ?int { return $this->id; }
    public function getProjet(): ?Projets { return $this->projet; }
    public function setProjet(?Projets $projet): static { $this->projet = $projet; return $this; }
    public function getMaker(): ?Utilisateurs { return $this->maker; }
    public function setMaker(?Utilisateurs $maker): static { $this->maker = $maker; return $this; }
}