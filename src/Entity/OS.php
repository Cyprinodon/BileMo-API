<?php

namespace App\Entity;

use App\Repository\OSRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation  as Serializer;

/**
 * @ORM\Entity(repositoryClass=OSRepository::class)
 * @Serializer\AccessorOrder("custom", custom = {"id", "name", "manufacturer", "phones", "label"})
 */
class OS
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"show", "default"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=127)
     * @Serializer\Groups({"default"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=127)
     * @Serializer\Groups({"default"})
     */
    private $manufacturer;

    /**
     * @ORM\ManyToMany(targetEntity=Phone::class, mappedBy="possibleOS")
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(string $manufacturer): self
    {
        $this->manufacturer = $manufacturer;

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
            $phone->addPossibleOS($this);
        }

        return $this;
    }

    public function removePhone(Phone $phone): self
    {
        if ($this->phones->removeElement($phone)) {
            $phone->removePossibleOS($this);
        }

        return $this;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("label")
     * @Serializer\Groups({"show"})
     */
    public function getSerializedOS()
    {
        return $this->getName()." par ".$this->getManufacturer();
    }
}
