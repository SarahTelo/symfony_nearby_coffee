<?php

namespace App\Entity;

use App\Repository\RoastingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=RoastingRepository::class)
 * @ORM\Table(name="symfony_roasting")
 * @UniqueEntity("name", message="La torréfcation existe déjà.")
 */
class Roasting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Type(type = "string")
     * @Assert\NotBlank(
     *      message="Champ obligatoire."
     * )
     * @Assert\Length(
     *      min=1, max=200,
     *      minMessage = "Minimum {{ limit }} caractère",
     *      maxMessage = "Maximum {{ limit }} caractères",
     * )
     * @Assert\Regex(
     *      pattern = "[[=%\$<>*+\}\{\\\/\]\[;()]]",
     *      match = false,
     *      message = "Le nom de la torréfaction ne doit pas contenir les caractères spéciaux suivants: = % $ < > * + } { \ / ] [ ; ( )"
     * )
     * @Assert\Regex(
     *      pattern = "[[a-zA-Z]]",
     *      match = true,
     *      message = "Le nom de la torréfaction doit contenir au minimum un caractère alphabétique."
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity=Coffee::class, mappedBy="roasting")
     */
    private $coffees;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Type(type = "string")
     * @Assert\Length(
     *      min=1, max=500,
     *      minMessage = "Minimum {{ limit }} caractère",
     *      maxMessage = "Maximum {{ limit }} caractères",
     * )
     * @Assert\Regex(
     *      pattern = "[[=%\$<>*+\}\{\\\/\]\[;]]",
     *      match = false,
     *      message = "La description ne doit pas contenir les caractères spéciaux suivants: = % $ < > * + } { \ / ] [ ;"
     * )
     * @Assert\Regex(
     *      pattern = "[[a-zA-Z]]",
     *      match = true,
     *      message = "La description doit contenir au minimum un caractère alphabétique."
     * )
     */
    private $description;

    public function __construct()
    {
        $this->coffees = new ArrayCollection();
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

    /**
     * @return Collection|Coffee[]
     */
    public function getCoffees(): Collection
    {
        return $this->coffees;
    }

    public function addCoffee(Coffee $coffee): self
    {
        if (!$this->coffees->contains($coffee)) {
            $this->coffees[] = $coffee;
            $coffee->setRoasting($this);
        }

        return $this;
    }

    public function removeCoffee(Coffee $coffee): self
    {
        if ($this->coffees->removeElement($coffee)) {
            // set the owning side to null (unless already changed)
            if ($coffee->getRoasting() === $this) {
                $coffee->setRoasting(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
