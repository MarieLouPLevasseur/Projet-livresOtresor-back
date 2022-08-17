<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $isbn;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $publisher;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToMany(targetEntity=Author::class, mappedBy="book")
     */
    private $authors;

    /**
     * @ORM\OneToMany(targetEntity=BookKid::class, mappedBy="book")
     */
    private $bookKids;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->kids = new ArrayCollection();
        $this->bookKids = new ArrayCollection();
        $this->created_at = new \DateTime();
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsbn(): ?int
    {
        return $this->isbn;
    }

    public function setIsbn(int $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, Author>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
            $author->addBook($this);
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        if ($this->authors->removeElement($author)) {
            $author->removeBook($this);
        }

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
            $bookKid->setBook($this);
        }

        return $this;
    }

    public function removeBookKid(BookKid $bookKid): self
    {
        if ($this->bookKids->removeElement($bookKid)) {
            // set the owning side to null (unless already changed)
            if ($bookKid->getBook() === $this) {
                $bookKid->setBook(null);
            }
        }

        return $this;
    }

}
