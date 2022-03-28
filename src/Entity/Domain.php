<?php

namespace App\Entity;

use App\Repository\DomainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity=Page::class, mappedBy="domain")
     */
    private Collection $pages;

    public function __construct(string $name,Customer $owner, bool $isPublic = false)
    {
        $this->pages = new ArrayCollection();
        $this->isPublic = $isPublic;
        $this->owner = $owner;
        $this->validName($name);
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function changeName(string $name): self
    {
        $this->validName($name);
        $this->name = $name;

        return $this;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setAsPublic(): self
    {
        $this->isPublic = true;

        return $this;
    }

    public function setAsPrivate(): self
    {
        $this->isPublic = false;

        return $this;
    }

    public function getOwner(): ?Customer
    {
        return $this->owner;
    }

    public function changeOwner(?Customer $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Page>
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Page $page): self
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
            $page->changeDomain($this);
        }

        return $this;
    }

    public function removePage(Page $page): self
    {
        if ($this->pages->removeElement($page)) {
            // set the owning side to null (unless already changed)
            if ($page->domain() === $this) {
                $page->changeDomain(null);
            }
        }

        return $this;
    }

    public function validName(string $name):void{
        if(!filter_var($name,FILTER_VALIDATE_DOMAIN)){
            throw new \InvalidArgumentException("Except domain, '{$name}' isn`t domain");
        }
    }
}
