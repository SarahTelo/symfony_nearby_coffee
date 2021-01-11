<?php

namespace App\Entity;

use App\Repository\CoffeeRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CoffeeRepository::class)
 * @UniqueEntity("name", message="Le café existe déjà")
 */
class Coffee
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Champ obligatoire")
     * @Assert\Length(
     *      min=1, max=50,
     *      minMessage = "Minimum 1 caractère",
     *      maxMessage = "Maximum 50 caractères",
     * )
     * @Assert\Type(type = "string")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Champ obligatoire")
     * @Assert\Length(
     *      min=1, max=50,
     *      minMessage = "Minimum {{ limit }} caractère",
     *      maxMessage = "Maximum {{ limit }} caractères",
     * )
     */
    private $country;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Positive(message = "Entrez un nombre positif")
     * @Assert\Type(
     *      type = "float",
     *      message = "La valeur {{ value }} n'est pas un nombre.",
     * )
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=Roasting::class, inversedBy="coffees")
     */
    private $roasting;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

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

    public function getRoasting(): ?Roasting
    {
        return $this->roasting;
    }

    public function setRoasting(?Roasting $roasting): self
    {
        $this->roasting = $roasting;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
