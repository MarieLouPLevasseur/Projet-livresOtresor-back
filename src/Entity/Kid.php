<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\KidRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=KidRepository::class)
 * @UniqueEntity(fields={"username"}, message="Merci de choisir un autre pseudonyme")
 */

class Kid implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"book_list","books_infos", "userkids_list", "books_read", "books_wish", "userConnected","adminUsers"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"book_list","books_infos", "userkids_list", "books_read", "books_wish", "userConnected","adminUsers"})
     */
    private $username;

    /**
     * 
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"book_list","books_infos", "userConnected", "userkids_list"})
     * @Assert\NotNull( message = "Ce champ ne peut pas être vide")
     * @Assert\Length(min=10)
     */
    private $profile_avatar;

    /**
     * @ORM\ManyToMany(targetEntity=Diploma::class, inversedBy="kids")
     */
    private $diploma;

    /**
     * @ORM\ManyToMany(targetEntity=Avatar::class, inversedBy="kids")
     */
    private $avatar;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="kids")
     * @Groups({"userConnected"})
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="kid")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=BookKid::class, mappedBy="kid", fetch="EAGER", cascade={"persist", "remove"})
     * @Groups({"book_list"})
     */
    private $bookKids;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"book_list","books_infos", "userkids_list", "books_read", "books_wish", "userConnected"})
     * @Assert\NotNull( message = "Ce champ ne peut pas être vide")
     * @Assert\Length(min=3)( message = "Le nom d'utilisateur doit contenir au moins 3 caractères")
     */
    private $firstname;

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

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

     /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getRoles(): array
    {
        return [$this->role->getName()];
    }

    /**
     * A visual identifier that represents this user.
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    
}
