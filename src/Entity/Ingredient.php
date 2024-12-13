<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $priceUnit = null;

    #[ORM\Column(length: 10)]
    private ?string $unit = null;

    /**
     * @var Collection<int, Contain>
     */
    #[ORM\OneToMany(targetEntity: Contain::class, mappedBy: 'ingredient', orphanRemoval: true)] // Si Ingredient meurt alors ses contains mourront
    private Collection $contains;

    public function __construct()
    {
        $this->contains = new ArrayCollection();
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

    public function getPriceUnit(): ?int
    {
        return $this->priceUnit;
    }

    public function setPriceUnit(int $priceUnit): static
    {
        $this->priceUnit = $priceUnit;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * @return Collection<int, Contain>
     */
    public function getContains(): Collection
    {
        return $this->contains;
    }

    public function addContain(Contain $contain): static
    {
        if (!$this->contains->contains($contain)) {
            $this->contains->add($contain);
            $contain->setIngredient($this);
        }

        return $this;
    }

    public function removeContain(Contain $contain): static
    {
        if ($this->contains->removeElement($contain)) {
            // set the owning side to null (unless already changed)
            if ($contain->getIngredient() === $this) {
                $contain->setIngredient(null);
            }
        }

        return $this;
    }
}
