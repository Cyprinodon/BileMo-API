<?php

namespace App\Entity;

use App\Repository\ColorRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=ColorRepository::class)
 * @Serializer\AccessorOrder("custom", custom = {"id", "name", "hexadecimal", "phones"})
 */
class Color
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"show", "default"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=127, nullable=true)
     * @Serializer\Groups({"default", "show"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=6)
     * @Serializer\Groups({"default"})
     */
    private $hexadecimal;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getHexadecimal(): ?string
    {
        return $this->hexadecimal;
    }

    public function setHexadecimal(string $hexadecimal): self
    {
        $this->hexadecimal = $hexadecimal;

        return $this;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("hexadecimal")
     * @Serializer\Groups({"show"})
     */
    public function getSerializedHexadecimal()
    {
        return "#".$this->getHexadecimal();
    }
}
