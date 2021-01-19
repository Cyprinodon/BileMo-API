<?php

namespace App\Entity;

use App\Repository\StorageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=StorageRepository::class)
 * @Serializer\AccessorOrder("custom", custom = {"id", "capacity", "unit", "phones"})
 */
class Storage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"show", "default"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Groups ({"default"})
     */
    private $capacity;

    /**
     * @ORM\Column(type="string", length=2)
     * @Serializer\Groups({"default"})
     */
    private $unit;

    /**
     * @ORM\ManyToMany(targetEntity=Phone::class, mappedBy="storage")
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

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

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
            $phone->addStorage($this);
        }

        return $this;
    }

    public function removePhone(Phone $phone): self
    {
        if ($this->phones->removeElement($phone)) {
            $phone->removeStorage($this);
        }

        return $this;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("capacity")
     * @Serializer\Groups({"show"})
     */
    public function getSerializedStorage()
    {
        return $this->getCapacity().$this->getUnit();
    }
}
