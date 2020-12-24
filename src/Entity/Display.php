<?php

namespace App\Entity;

use App\Repository\DisplayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DisplayRepository::class)
 */
class Display
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $touchscreen;

    /**
     * @ORM\ManyToOne(targetEntity=Dimensions::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $pixelSize;

    /**
     * @ORM\ManyToOne(targetEntity=Dimensions::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $viewport;

    /**
     * @ORM\OneToMany(targetEntity=Phone::class, mappedBy="display")
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

    public function getTouchscreen(): ?bool
    {
        return $this->touchscreen;
    }

    public function setTouchscreen(bool $touchscreen): self
    {
        $this->touchscreen = $touchscreen;

        return $this;
    }

    public function getPixelSize(): ?Dimensions
    {
        return $this->pixelSize;
    }

    public function setPixelSize(?Dimensions $pixelSize): self
    {
        $this->pixelSize = $pixelSize;

        return $this;
    }

    public function getViewport(): ?Dimensions
    {
        return $this->viewport;
    }

    public function setViewport(?Dimensions $viewport): self
    {
        $this->viewport = $viewport;

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
            $phone->setDisplay($this);
        }

        return $this;
    }

    public function removePhone(Phone $phone): self
    {
        if ($this->phones->removeElement($phone)) {
            // set the owning side to null (unless already changed)
            if ($phone->getDisplay() === $this) {
                $phone->setDisplay(null);
            }
        }

        return $this;
    }
}
