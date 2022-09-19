<?php

namespace App\Entity;

use App\Repository\SeriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SeriesRepository::class)
 */
class Series
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
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=BookKid::class, mappedBy="series")
     */
    private $bookKids;

    public function __construct()
    {
        $this->bookKids = new ArrayCollection();
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

    /**
     * @return Collection<int, BookKid>
     */
    public function getBookKids(): Collection
    {
        return $this->bookKids;
    }

    public function addBookKid(BookKid $bookKid): self
    {
        if (!$this->bookKids->contains($bookKid)) {
            $this->bookKids[] = $bookKid;
            $bookKid->setSeries($this);
        }

        return $this;
    }

    public function removeBookKid(BookKid $bookKid): self
    {
        if ($this->bookKids->removeElement($bookKid)) {
            // set the owning side to null (unless already changed)
            if ($bookKid->getSeries() === $this) {
                $bookKid->setSeries(null);
            }
        }

        return $this;
    }
}
