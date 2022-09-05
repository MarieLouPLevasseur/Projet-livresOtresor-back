<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"booksByCategory","book_list","books_infos", "books_read", "books_wish", "author_list", "category"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"booksByCategory","book_list","books_infos", "books_read", "books_wish", "author_list", "category"})

     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=BookKid::class, mappedBy="category")
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
            $bookKid->setCategory($this);
        }
        return $this;
    }

    public function removeBookKid(BookKid $bookKid): self
    {
        if ($this->bookKids->removeElement($bookKid)) {
            // set the owning side to null (unless already changed)
            if ($bookKid->getCategory() === $this) {
                $bookKid->setCategory(null);
            }
        }
        return $this;
    }
}
