<?php

namespace App\Entity;

use App\Repository\CoffeeRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CoffeeRepository::class)
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
     * @ORM\Column(type="string", length=255)
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
     *      minMessage = "Minimum 1 caractère",
     *      maxMessage = "Maximum 50 caractères",
     * )
     */
    private $country;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Positive(message = "Entrez un nombre positif")
     * @Assert\Type(
     *      type = "integer",
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

    public function __construct()
    {
        //Date par défaut
        $this->created_at = new \DateTime();
    }

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
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
}
