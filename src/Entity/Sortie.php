<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateHeureDebut = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $duree = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateLimiteInscription = null;

    #[ORM\Column]
    private ?int $nbInscriptionMax = null;

    #[ORM\Column(length: 255)]
    private ?string $infosSortie = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    private ?Lieu $sortieLieu = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    private ?etat $sortieEtat = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    private ?campus $sortieCampus = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    private ?participant $sortieParticipant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $dateHeureDebut): static
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?\DateTimeInterface
    {
        return $this->duree;
    }

    public function setDuree(\DateTimeInterface $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): static
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionMax(): ?int
    {
        return $this->nbInscriptionMax;
    }

    public function setNbInscriptionMax(int $nbInscriptionMax): static
    {
        $this->nbInscriptionMax = $nbInscriptionMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(string $infosSortie): static
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getSortieLieu(): ?Lieu
    {
        return $this->sortieLieu;
    }

    public function setSortieLieu(?Lieu $sortieLieu): static
    {
        $this->sortieLieu = $sortieLieu;

        return $this;
    }

    public function getSortieEtat(): ?etat
    {
        return $this->sortieEtat;
    }

    public function setSortieEtat(?etat $sortieEtat): static
    {
        $this->sortieEtat = $sortieEtat;

        return $this;
    }

    public function getSortieCampus(): ?campus
    {
        return $this->sortieCampus;
    }

    public function setSortieCampus(?campus $sortieCampus): static
    {
        $this->sortieCampus = $sortieCampus;

        return $this;
    }

    public function getSortieParticipant(): ?participant
    {
        return $this->sortieParticipant;
    }

    public function setSortieParticipant(?participant $sortieParticipant): static
    {
        $this->sortieParticipant = $sortieParticipant;

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom;
    }
}
