<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LanguageRepository")
 */
class Language
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */

    private $language_name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="languages")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="language_id")
     */
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TraductionTarget", mappedBy="language_id", orphanRemoval=true)
     */
    private $traductionTargets;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->traductionTargets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLanguageName(): ?string
    {
        return $this->language_name;
    }

    public function setLanguageName(string $language_name): self
    {
        $this->language_name = $language_name;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addLanguage($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeLanguage($this);
        }

        return $this;
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
            $project->setLanguageId($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            // set the owning side to null (unless already changed)
            if ($project->getLanguageId() === $this) {
                $project->setLanguageId(null);
            }
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
            $traductionTarget->setLanguageId($this);
        }

        return $this;
    }

    public function removeTraductionTarget(TraductionTarget $traductionTarget): self
    {
        if ($this->traductionTargets->contains($traductionTarget)) {
            $this->traductionTargets->removeElement($traductionTarget);
            // set the owning side to null (unless already changed)
            if ($traductionTarget->getLanguageId() === $this) {
                $traductionTarget->setLanguageId(null);
            }
        }

        return $this;
	}
	
	public function __toString()
    {
        return $this->language_name;
    }
}
