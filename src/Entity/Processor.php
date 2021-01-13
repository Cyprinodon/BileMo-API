<?php

namespace App\Entity;

use App\Repository\ProcessorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProcessorRepository::class)
 */
class Processor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Brand::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $brand;

    /**
     * @ORM\Column(type="smallint")
     */
    private $cores;

    /**
     * @ORM\Column(type="integer")
     */
    private $frequency;

    /**
     * @ORM\OneToMany(targetEntity=Phone::class, mappedBy="processor")
     */
    private $phones;

    public function __construct()
    {
        $this->phones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getCores(): ?int
    {
        return $this->cores;
    }

    public function setCores(int $cores): self
    {
        $this->cores = $cores;

        return $this;
    }

    public function getFrequency(): ?int
    {
        return $this->frequency;
    }

    public function setFrequency(int $frequency): self
    {
        $this->frequency = $frequency;

        return $this;
    }

    /**
     * @return Collection|Phone[]
     */
    public function getPhones(): Collection
    {
        return $this->phones;
    }

    public function addPhone(Phone $phone): self
    {
        if (!$this->phones->contains($phone)) {
            $this->phones[] = $phone;
            $phone->setProcessor($this);
        }

        return $this;
    }

    public function removePhone(Phone $phone): self
    {
        if ($this->phones->removeElement($phone)) {
            // set the owning side to null (unless already changed)
            if ($phone->getProcessor() === $this) {
                $phone->setProcessor(null);
            }
        }

        return $this;
    }
}
