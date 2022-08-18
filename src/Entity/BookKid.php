<?php

namespace App\Entity;

use App\Repository\BookKidRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BookKidRepository::class)
 */
class BookKid
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"book_list"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"book_list"})

     */
    private $comment;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"book_list"})

     */
    private $rating;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"book_list"})

     */
    private $is_read;

    /**
     * @ORM\ManyToOne(targetEntity=Kid::class, inversedBy="bookKids")
     * @Groups({"book_list"})

     */
    private $kid;

    /**
     * @ORM\ManyToOne(targetEntity=Book::class, inversedBy="bookKids")
     */
    private $book;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="bookKids")
     */
    private $category;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    public function __construct()
    {
        $this->updated_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function isIsRead(): ?bool
    {
        return $this->is_read;
    }

    public function setIsRead(bool $is_read): self
    {
        $this->is_read = $is_read;

        return $this;
    }

    public function getKid(): ?kid
    {
        return $this->kid;
    }

    public function setKid(?kid $kid): self
    {
        $this->kid = $kid;

        return $this;
    }

    public function getBook(): ?book
    {
        return $this->book;
    }

    public function setBook(?book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
