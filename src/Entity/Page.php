<?php

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PageRepository::class)
 */
class Page
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * @ORM\ManyToOne(targetEntity=Domain::class, inversedBy="pages")
     * @ORM\JoinColumn(nullable=false)
     */
    private Domain $domain;

    /**
     * @ORM\Column(type="text")
     */
    private string $path;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="pages")
     * @ORM\JoinColumn(nullable=false)
     */
    private Customer $owner;

    public function __construct(string $title, Domain $domain, string $path, Customer $owner)
    {
        $this->title = $title;
        $this->domain = $domain;
        $this->path = $path;
        $this->owner = $owner;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function title(): ?string
    {
        return $this->title;
    }

    public function changeTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function domain(): ?Domain
    {
        return $this->domain;
    }

    public function changeDomain(?Domain $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function path(): ?string
    {
        return $this->path;
    }

    public function changePath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function owner(): Customer
    {
        return $this->owner;
    }

    public function changeOwner(Customer $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
