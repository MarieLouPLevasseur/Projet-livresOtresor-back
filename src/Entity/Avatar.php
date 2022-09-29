<?php

namespace App\Entity;

use App\Repository\AvatarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AvatarRepository::class)
 */
class Avatar
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"KidAvatar"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"KidAvatar"})
     * @Assert\NotBlank(message = "Ce champ ne peut pas être vide")
     * @Assert\NotNull(message = "Ce champ ne peut pas être vide")
     * @Assert\Length(min=10)
     */
    private $url;

    /**
     * @ORM\Column(type="integer")
     */
    private $is_win;

    /**
     * @ORM\ManyToMany(targetEntity=Kid::class, mappedBy="avatar")
     */
    private $kids;

    public function __construct()
    {
        $this->kids = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getIsWin(): ?int
    {
        return $this->is_win;
    }

    public function setIsWin(int $is_win): self
    {
        $this->is_win = $is_win;
        return $this;
    }

    /**
     * @return Collection<int, Kid>
     */
    public function getKids(): Collection
    {
        return $this->kids;
    }

    public function addKid(Kid $kid): self
    {
        if (!$this->kids->contains($kid)) {
            $this->kids[] = $kid;
            $kid->addAvatar($this);
        }

        return $this;
    }

    public function removeKid(Kid $kid): self
    {
        if ($this->kids->removeElement($kid)) {
            $kid->removeAvatar($this);
        }

        return $this;
    }
}
