<?php

namespace App\Entity;

use App\Entity\Project;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TraductionSourceRepository")
 */
class TraductionSource
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="traductionSources")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\Column(type="string", length=255,  nullable=true)
     */
	private $source;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TraductionTarget", mappedBy="traduction_source", orphanRemoval=true)
     */
    private $traductionTargets;

    public function __construct()
    {
        $this->traductionTargets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectId(): ?Project
    {
        return $this->project;
    }

    public function setProjectId(?Project $project): self
    {
        $this->project = $project;

        return $this;
	}
	
	public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

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
            $traductionTarget->setTraductionSourceId($this);
        }

        return $this;
    }

    public function removeTraductionTarget(TraductionTarget $traductionTarget): self
    {
        if ($this->traductionTargets->contains($traductionTarget)) {
            $this->traductionTargets->removeElement($traductionTarget);
            // set the owning side to null (unless already changed)
            if ($traductionTarget->getTraductionSourceId() === $this) {
                $traductionTarget->setTraductionSourceId(null);
            }
        }

        return $this;
	}
	public function __toString()
    {
        return $this->source;
    }

}
