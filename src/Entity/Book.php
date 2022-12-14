<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Validator\Constraints as Assert;
use Symdony\Component\Validator\Constraints\NotBlank;




/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 * 
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")

     * @Groups({"booksByCategory","book_list", "books_read", "books_wish" , "books_infos", "last_book_read"})
     */
    private $id;
    
    /**

     * @ORM\Column(type="bigint")
     * @Groups({"booksByCategory","book_list","books_read", "books_wish", "books_infos"})
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(min=13, max=13)
     * 
     */
    private $isbn;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"booksByCategory","book_list","books_infos","books_read", "books_wish", "last_book_read"})
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(min=2)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"booksByCategory","book_list","books_infos", "books_read", "books_wish", "last_book_read"})
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(min=10)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"booksByCategory","book_list","books_infos"})
     */
    private $publisher;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToMany(targetEntity=Author::class, mappedBy="book", cascade={"persist"}, fetch="EAGER")
     * @Groups({"booksByCategory","book_list","books_infos", "books_read", "books_wish"})
     * @Assert\Valid
     */
    private $authors;

    /**
     * @ORM\OneToMany(targetEntity=BookKid::class, mappedBy="book", cascade={"persist"})
     * @Groups({"book_list"})
     */
    private $bookKids;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"booksByCategory","book_list","books_infos", "books_read", "books_wish","last_book_read"})

     */
    private $cover;

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

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

}
