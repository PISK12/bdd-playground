<?php

namespace App\Entity;

use App\Repository\DomainRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DomainRepository::class)
 */
class Domain
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=1048)
     */
    private string $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isPublic;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="domains")
     * @ORM\JoinColumn(nullable=false)
     */
    private Customer $owner;

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

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function getOwner(): ?Customer
    {
        return $this->owner;
    }

    public function setOwner(?Customer $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
