<?php

namespace App\Entity;

use App\Repository\ColorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ColorRepository::class)
 */
class Color
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=127, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=6)
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
}
