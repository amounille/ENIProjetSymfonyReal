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
    private ?string $motPasse = null; // Mot de passe encodé

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\OneToMany(mappedBy: 'sortieParticipant', targetEntity: Sortie::class)]
    private Collection $sorties;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    private ?campus $participantCampus = null;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
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

    public function getParticipantCampus(): ?campus
    {
        return $this->participantCampus;
    }

    public function setParticipantCampus(?campus $participantCampus): static
    {
        $this->participantCampus = $participantCampus;

        return $this;
    }

    // Implémentation des méthodes de UserInterface

    /**
     * Retourne l'identifiant unique de l'utilisateur (par ex. l'adresse e-mail).
     */
    public function getUserIdentifier(): string
    {
        return $this->mail;
    }

    /**
     * Retourne le mot de passe encodé.
     */
    public function getPassword(): string
    {
        return $this->motPasse;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // Ajoute toujours ROLE_USER par défaut
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt(): ?string
    {
        // Pas nécessaire si bcrypt ou sodium est utilisé
        return null;
    }

    public function eraseCredentials()
    {
        // Supprime les données sensibles temporaires si nécessaire
    }
    public function __toString(): string
    {
        return $this->nom;
    }
}
