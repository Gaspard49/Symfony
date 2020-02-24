<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @UniqueEntity(fields={"pseudo"}, message="There is already an account with this username")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    public function getId(): ?int
    {
        return $this->id;
    }

   /**
     * @var string le token qui servira lors de l'oubli de mot de passe
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $resetToken;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="user", orphanRemoval=true)
     */
    private $projects;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Language", inversedBy="users")
     */
    private $languages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TraductionTarget", mappedBy="user_id")
     */
    private $traductionTargets;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->languages = new ArrayCollection();
        $this->traductionTargets = new ArrayCollection();
    }
  
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return string
     */
    public function getResetToken(): string
    {
       return $this->resetToken ? $this->resetToken : 'No';
    }

    /**
     * @param string $resetToken
     */
    public function setResetToken(?string $resetToken): void
    {
        $this->resetToken = $resetToken;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setUser($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            // set the owning side to null (unless already changed)
            if ($project->getUser() === $this) {
                $project->setUser(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }


    /**
     * @return Collection|Language[]
     */
    public function getLanguages(): Collection
    {
        return $this->languages;
    }

    public function addLanguage(Language $language): self
    {
        if (!$this->languages->contains($language)) {
            $this->languages[] = $language;
        }

        return $this;
    }

    public function removeLanguage(Language $language): self
    {
        if ($this->languages->contains($language)) {
            $this->languages->removeElement($language);
        }

        return $this;
    }

    /**
     * @return Collection|TraductionTarget[]
     */
    public function getTraductionTargets(): Collection
    {
        return $this->traductionTargets;
    }

    public function addTraductionTarget(TraductionTarget $traductionTarget): self
    {
        if (!$this->traductionTargets->contains($traductionTarget)) {
            $this->traductionTargets[] = $traductionTarget;
            $traductionTarget->setUserId($this);
        }

        return $this;
    }

    public function removeTraductionTarget(TraductionTarget $traductionTarget): self
    {
        if ($this->traductionTargets->contains($traductionTarget)) {
            $this->traductionTargets->removeElement($traductionTarget);
            // set the owning side to null (unless already changed)
            if ($traductionTarget->getUserId() === $this) {
                $traductionTarget->setUserId(null);
            }
        }

        return $this;
	}
	
	public function __toString()
    {
        return $this->pseudo;
    }
}
