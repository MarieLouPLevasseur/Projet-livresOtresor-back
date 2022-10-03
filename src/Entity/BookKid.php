<?php

namespace App\Entity;

use App\Repository\BookKidRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=BookKidRepository::class)
 */
class BookKid
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"book_list", "book_list", "books_infos", "books_read", "books_wish", "last_book_read", "booksByCategory"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"booksByCategory","book_list","books_infos"})
     */
    private $comment;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"booksByCategory","book_list","books_infos", "books_read","books_wish", "last_book_read"})
     * @Assert\Range(
     *      min = 0,
     *      max = 5,
     *      notInRangeMessage = "You must be between {{ min }} and {{ max }}")
     */
    private $rating;


    /**
     * @ORM\Column(type="boolean")
     * @Groups({"booksByCategory","book_list","books_infos", "books_read", "books_wish"})
     * @Assert\Type(type="boolean",message="The value passed is not a valid type. Boolean expected.")
     */
    private $is_read;

    /**
     * @ORM\ManyToOne(targetEntity=Kid::class, inversedBy="bookKids")
     * @Groups({"books_read", "books_wish", "author_books"})
     */
    private $kid;

    /**
     * @ORM\ManyToOne(targetEntity=Book::class, inversedBy="bookKids", cascade={"persist"})
     * @Groups({"booksByCategory","books_infos", "books_read", "books_wish","last_book_read"})
     * @Assert\Valid
     */
    private $book;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="bookKids", cascade={"persist"})
     * @Groups({"booksByCategory","books_infos", "books_read", "books_wish","last_book_read","book_list"})
     * 
     */
    private $category;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"last_book_read"})
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=Series::class, inversedBy="bookKids", cascade={"persist"})
     * @Groups({"booksByCategory","books_infos", "books_read", "books_wish","last_book_read","book_list"})
     */
    private $series;

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

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): self
    {
        $this->rating = $rating;
        return $this;
    }
     
    public function getIsRead()
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

    // public function getUpdatedAt():  ?\DateTimeInterface
    public function getUpdatedAt(): ?string
    {
        // return $this->updated_at;
        return $this->updated_at->format('d/m/Y');

    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function getSeries(): ?Series
    {
        return $this->series;
    }

    public function setSeries(?Series $series): self
    {
        $this->series = $series;

        return $this;
    }

   
}
