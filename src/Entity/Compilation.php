<?php

namespace App\Entity;

use DateTime;
use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CompilationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: CompilationRepository::class)]
class Compilation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'compilations')]
    private ?User $user = null;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'compilations', cascade: ['persist'])]
    private Collection $tags;

    /**
     * @var Collection<int, Save>
     */
    #[ORM\OneToMany(targetEntity: Save::class, mappedBy: 'compilation', orphanRemoval: true)] // si Compilation meurt, alors toutes les saves de cette Compilation mourront
    private Collection $saves;

    /**
     * @var Collection<int, Recipe>
     */
    #[ORM\ManyToMany(targetEntity: Recipe::class, mappedBy: 'compilations')]
    private Collection $recipes;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->saves = new ArrayCollection();
        $this->recipes = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addCompilation($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeCompilation($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Save>
     */
    public function getSaves(): Collection
    {
        return $this->saves;
    }

    public function addSave(Save $save): static
    {
        if (!$this->saves->contains($save)) {
            $this->saves->add($save);
            $save->setCompilation($this);
        }

        return $this;
    }

    public function removeSave(Save $save): static
    {
        if ($this->saves->removeElement($save)) {
            // set the owning side to null (unless already changed)
            if ($save->getCompilation() === $this) {
                $save->setCompilation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(Recipe $recipe): static
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes->add($recipe);
            $recipe->addCompilation($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): static
    {
        if ($this->recipes->removeElement($recipe)) {
            $recipe->removeCompilation($this);
        }

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function getCreationDateFr(): ?string
    {
        return ($this->creationDate->diff(new DateTime())->format("%a") > 0
            ? $this->creationDate->format("d/m/Y")
            : "aujourd'hui, " . $this->creationDate->format("H:i"));
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }
}
