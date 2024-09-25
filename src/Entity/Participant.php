<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    private ?string $mail = null;

    #[ORM\Column(length: 255)]
    private ?string $motPasse = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $actif = true;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\ManyToMany(targetEntity: Sortie::class, mappedBy: 'participants')]
    private Collection $inscriptions;

    #[ORM\OneToMany(mappedBy: 'sortieParticipant', targetEntity: Sortie::class)]
    private Collection $sorties;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    private ?Campus $participantCampus = null;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
    }

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getMotPasse(): ?string
    {
        return $this->motPasse;
    }

    public function setMotPasse(string $motPasse): static
    {
        $this->motPasse = $motPasse;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function getParticipantCampus(): ?Campus
    {
        return $this->participantCampus;
    }

    public function setParticipantCampus(?Campus $participantCampus): static
    {
        $this->participantCampus = $participantCampus;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->motPasse;
    }

    public function getUserIdentifier(): string
    {
        return $this->mail;
    }

    public function getSalt(): ?string
    {
        return null; // Not needed for bcrypt or argon2i
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Sortie $sortie): static
    {
        if (!$this->inscriptions->contains($sortie)) {
            $this->inscriptions->add($sortie);
            $sortie->addParticipant($this);
        }

        return $this;
    }

    public function removeInscription(Sortie $sortie): static
    {
        if ($this->inscriptions->removeElement($sortie)) {
            $sortie->removeParticipant($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom;
    }
}
