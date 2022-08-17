<?php

namespace App\Entity;

use App\Repository\KidRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=KidRepository::class)
 */
class Kid
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
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $profile_avatar;

    /**
     * @ORM\ManyToMany(targetEntity=diploma::class, inversedBy="kids")
     */
    private $diploma;

    /**
     * @ORM\ManyToMany(targetEntity=avatar::class, inversedBy="kids")
     */
    private $avatar;

    /**
     * @ORM\ManyToOne(targetEntity=role::class, inversedBy="kids")
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="kid")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=BookKid::class, mappedBy="kid")
     */
    private $bookKids;

    public function __construct()
    {
        $this->book = new ArrayCollection();
        $this->diploma = new ArrayCollection();
        $this->avatar = new ArrayCollection();
        $this->bookKids = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getProfileAvatar(): ?string
    {
        return $this->profile_avatar;
    }

    public function setProfileAvatar(string $profile_avatar): self
    {
        $this->profile_avatar = $profile_avatar;

        return $this;
    }

    /**
     * @return Collection<int, diploma>
     */
    public function getDiploma(): Collection
    {
        return $this->diploma;
    }

    public function addDiploma(diploma $diploma): self
    {
        if (!$this->diploma->contains($diploma)) {
            $this->diploma[] = $diploma;
        }

        return $this;
    }

    public function removeDiploma(diploma $diploma): self
    {
        $this->diploma->removeElement($diploma);

        return $this;
    }

    /**
     * @return Collection<int, avatar>
     */
    public function getAvatar(): Collection
    {
        return $this->avatar;
    }

    public function addAvatar(avatar $avatar): self
    {
        if (!$this->avatar->contains($avatar)) {
            $this->avatar[] = $avatar;
        }

        return $this;
    }

    public function removeAvatar(avatar $avatar): self
    {
        $this->avatar->removeElement($avatar);

        return $this;
    }

    public function getRole(): ?role
    {
        return $this->role;
    }

    public function setRole(?role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
            $bookKid->setKid($this);
        }

        return $this;
    }

    public function removeBookKid(BookKid $bookKid): self
    {
        if ($this->bookKids->removeElement($bookKid)) {
            // set the owning side to null (unless already changed)
            if ($bookKid->getKid() === $this) {
                $bookKid->setKid(null);
            }
        }

        return $this;
    }
}
