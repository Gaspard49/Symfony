<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

      /**
     * @ORM\Column(type="string",  nullable=true)
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="projects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $language;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TraductionSource", mappedBy="project", orphanRemoval=true)
     */
    private $traductionSources;

    public function __construct()
    {
        $this->traductionSources = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->file;
    }

    public function setFileName(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getLanguageId(): ?Language
    {
        return $this->language;
    }

    public function setLanguageId(?Language $language): self
    {
        $this->language = $language;

        return $this;
	}
	

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return Collection|TraductionSource[]
     */
    public function getTraductionSources(): Collection
    {
        return $this->traductionSources;
    }

    public function addTraductionSource(TraductionSource $traductionSource): self
    {
        if (!$this->traductionSources->contains($traductionSource)) {
            $this->traductionSources[] = $traductionSource;
            $traductionSource->setProjectId($this);
        }

        return $this;
    }

    public function removeTraductionSource(TraductionSource $traductionSource): self
    {
        if ($this->traductionSources->contains($traductionSource)) {
            $this->traductionSources->removeElement($traductionSource);
            // set the owning side to null (unless already changed)
            if ($traductionSource->getProjectId() === $this) {
                $traductionSource->setProjectId(null);
            }
        }

        return $this;
	}
	
	public function __toString()
    {
        return $this->name;
    }
}
