<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="symfony_user")
 * @UniqueEntity("email", message="L'utilisateur existe déjà")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(
     *      message="Champ obligatoire."
     * )
     * @Assert\Email(
     *      message = "Le format de l'adresse mail est incorrect."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Assert\NotBlank(
     *      message="Champ obligatoire."
     * )
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(
     *      message="Champ obligatoire."
     * )
     * @Assert\Regex(
     *      pattern = "^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*).+$^",
     *      match = true,
     *      message = "Votre mot de passe doit contenir au moins 8 caractères dont 1 majuscule, 1 minuscule et 1 chiffre"
     * )
     * @Assert\Length(
     *      min = 8, max = 255,
     *      minMessage = "Minimum {{ limit }} caractères.",
     *      maxMessage = "Maximum {{ limit }} caractères."
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Type(type = "string")
     * @Assert\NotBlank(
     *      message="Champ obligatoire."
     * )
     * @Assert\Length(
     *      min=1, max=100,
     *      minMessage = "Minimum {{ limit }} caractère",
     *      maxMessage = "Maximum {{ limit }} caractères",
     * )
     * @Assert\Regex(
     *      pattern = "[[=%\$<>*+\}\{\\\/\]\[;()]]",
     *      match = false,
     *      message = "Le prénom ne doit pas contenir les caractères spéciaux suivants: = % $ < > * + } { \ / ] [ ; ( )"
     * )
     * @Assert\Regex(
     *      pattern = "[\d]",
     *      match = false,
     *      message = "Le prénom ne doit pas contenir de chiffres ou de nombres."
     * )
     * @Assert\Regex(
     *      pattern = "[[a-zA-Z]]",
     *      match = true,
     *      message = "Le prénom doit contenir au minimum un caractère alphabétique."
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Type(type = "string")
     * @Assert\NotBlank(
     *      message="Champ obligatoire."
     * )
     * @Assert\Length(
     *      min=1, max=100,
     *      minMessage = "Minimum {{ limit }} caractère",
     *      maxMessage = "Maximum {{ limit }} caractères",
     * )
     * @Assert\Regex(
     *      pattern = "[[=%\$<>*+\}\{\\\/\]\[;()]]",
     *      match = false,
     *      message = "Le nom ne doit pas contenir les caractères spéciaux suivants: = % $ < > * + } { \ / ] [ ; ( )"
     * )
     * @Assert\Regex(
     *      pattern = "[\d]",
     *      match = false,
     *      message = "Le nom ne doit pas contenir de chiffres ou de nombres."
     * )
     * @Assert\Regex(
     *      pattern = "[[a-zA-Z]]",
     *      match = true,
     *      message = "Le nom doit contenir au minimum un caractère alphabétique."
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
