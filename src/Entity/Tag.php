<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    /**
     * @var Collection<int, Compilation>
     */
    #[ORM\ManyToMany(targetEntity: Compilation::class, inversedBy: 'tags')]
    private Collection $compilations;

    public function __construct()
    {
        $this->compilations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Compilation>
     */
    public function getCompilations(): Collection
    {
        return $this->compilations;
    }

    public function addCompilation(Compilation $compilation): static
    {
        if (!$this->compilations->contains($compilation)) {
            $this->compilations->add($compilation);
        }

        return $this;
    }

    public function removeCompilation(Compilation $compilation): static
    {
        $this->compilations->removeElement($compilation);

        return $this;
    }
}
