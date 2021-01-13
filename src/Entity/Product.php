<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use \DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"list", "show"})
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     * @Serializer\Groups({"list", "show"})
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"show"})
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Serializer\Groups({"show"})
     */
    private $launchDate;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"show"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Phone::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"list", "show"})
     */
    private $phone;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

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

    public function getLaunchDate(): ?DateTimeInterface
    {
        return $this->launchDate;
    }

    public function setLaunchDate(?DateTimeInterface $launchDate): self
    {
        $this->launchDate = $launchDate;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPhone(): ?Phone
    {
        return $this->phone;
    }

    public function setPhone(?Phone $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
}
