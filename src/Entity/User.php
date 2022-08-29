<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="email must be unique")
 * 
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user_list", "userConnected"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_list", "userConnected"})
     * @Assert\NotNull( message = "Ce champ ne peut pas être vide")
     * @Assert\Length(min=3)( message = "Le prénom doit contenir au moins 3 caractères")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_list", "userConnected"})
     * @Assert\NotNull( message = "Ce champ ne peut pas être vide")
     * @Assert\Length(min=2)( message = "Le prénom doit contenir au moins 2 caractères")
     */
    private $lastname;
    

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_list", "userConnected"})
     * @Assert\Email( message = "Cet email n'est pas valide")
     * @Assert\NotNull( message = "Ce champ ne peut pas être vide")
     * @Assert\Length(min=5)( message = "L'email doit contenir au moins 5 caractères")
  
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull( message = "Ce champ ne peut pas être vide")
     * @Assert\Length(min=5)( message = "Le mot de passe doit contenir au moins 5 caractères")
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="users")
     * @Groups({"user_list", "userConnected"})
     */
    private $role;

    /**
     * @ORM\OneToMany(targetEntity=Kid::class, mappedBy="user", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="kid_id", referencedColumnName="id", nullable=false)
     * @Groups({"userkids_list"})
     */
    private $kid;

    public function __construct()
    {
        $this->kid = new ArrayCollection();
        // $this->role = "ROLE_USER";

    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getRole() //: ?role
    {
        return $this->role;
        // return "ROLE_USER";
    }

    public function setRole(?role $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, kid>
     */
    public function getKid(): Collection
    {
        return $this->kid;
    }

    public function addKid(kid $kid): self
    {
        if (!$this->kid->contains($kid)) {
            $this->kid[] = $kid;
            $kid->setUser($this);
        }

        return $this;
    }

    public function removeKid(kid $kid): self
    {
        if ($this->kid->removeElement($kid)) {
            // set the owning side to null (unless already changed)
            if ($kid->getUser() === $this) {
                $kid->setUser(null);
            }
        }

        return $this;
    }

      /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
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

    public function __toString()
    {
        return $this->getUsername();
    }
    public function getUsername(): ?string
    {

        return $this->firstname." ". $this->lastname;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

}
