<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TraductionTargetRepository")
 */
class TraductionTarget
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="traductionTargets")
     * @ORM\JoinColumn(nullable=false)
     */
    public $language;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TraductionSource", inversedBy="traductionTargets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $traduction_source;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $target;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="traductionTargets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_id;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLanguageId(): ?Language
    {
        return $this->language;
    }

	public function getLanguage(): ?Language
    {
        return $this->language;
	}
	
    public function setLanguageId(?Language $language): self
    {
        $this->language = $language;

        return $this;
	}
	
	public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }


    public function getTraductionSourceId(): ?TraductionSource
    {
        return $this->traduction_source;
    }

    public function setTraductionSourceId(?TraductionSource $traduction_source): self
    {
        $this->traduction_source = $traduction_source;

        return $this;
	}
	
	public function getTraductionSource(): ?TraductionSource
    {
        return $this->traduction_source;
    }

    public function setTraductionSource(?TraductionSource $traduction_source): self
    {
        $this->traduction_source = $traduction_source;

        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(string $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user_id;
    }

    public function setUser(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
	}

	public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
	}
	
	public function __toString()
    {
        return $this->target;
    }

}
