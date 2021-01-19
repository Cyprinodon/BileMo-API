<?php

namespace App\Entity;

use App\Repository\PhoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=PhoneRepository::class)
 */
class Phone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"default"})
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     * @Serializer\Groups({"default"})
     */
    private $weight;

    /**
     * @ORM\ManyToOne(targetEntity=Display::class, inversedBy="phones")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"default", "show"})
     */
    private $display;

    /**
     * @ORM\OneToOne(targetEntity=Brand::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"default"})
     */
    private $brand;

    /**
     * @ORM\ManyToMany(targetEntity=Color::class)
     * @Serializer\Groups({"show", "default"})
     */
    private $possibleColors;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="phone", orphanRemoval=true)
     * @Serializer\SkipWhenEmpty()
     */
    private $products;

    /**
     * @ORM\ManyToMany(targetEntity=Storage::class, inversedBy="phones")
     * @Serializer\SkipWhenEmpty()
     * @Serializer\Groups({"show", "default"})
     */
    private $storage;

    /**
     * @ORM\ManyToMany(targetEntity=OS::class, inversedBy="phones")
     * @Serializer\SkipWhenEmpty()
     * @Serializer\Groups({"show", "default"})
     */
    private $possibleOS;

    /**
     * @ORM\ManyToOne(targetEntity=Processor::class, inversedBy="phones")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"show", "default"})
     */
    private $processor;

    /**
     * @ORM\ManyToOne(targetEntity=Dimensions::class)
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"default"})
     */
    private $size;

    public function __construct()
    {
        $this->possibleColors = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->storage = new ArrayCollection();
        $this->possibleOS = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getDisplay(): ?Display
    {
        return $this->display;
    }

    public function setDisplay(?Display $display): self
    {
        $this->display = $display;

        return $this;
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

    /**
     * @return Collection|Color[]
     */
    public function getPossibleColors(): Collection
    {
        return $this->possibleColors;
    }

    public function addPossibleColor(Color $possibleColor): self
    {
        if (!$this->possibleColors->contains($possibleColor)) {
            $this->possibleColors[] = $possibleColor;
        }

        return $this;
    }

    public function removePossibleColor(Color $possibleColor): self
    {
        $this->possibleColors->removeElement($possibleColor);

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setPhone($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getPhone() === $this) {
                $product->setPhone(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Storage[]
     */
    public function getStorage(): Collection
    {
        return $this->storage;
    }

    public function addStorage(Storage $storage): self
    {
        if (!$this->storage->contains($storage)) {
            $this->storage[] = $storage;
        }

        return $this;
    }

    public function removeStorage(Storage $storage): self
    {
        $this->storage->removeElement($storage);

        return $this;
    }

    /**
     * @return Collection|OS[]
     */
    public function getPossibleOS(): Collection
    {
        return $this->possibleOS;
    }

    public function addPossibleOS(OS $o): self
    {
        if (!$this->possibleOS->contains($o)) {
            $this->possibleOS[] = $o;
        }

        return $this;
    }

    public function removePossibleOS(OS $o): self
    {
        $this->possibleOS->removeElement($o);

        return $this;
    }

    public function getProcessor(): ?Processor
    {
        return $this->processor;
    }

    public function setProcessor(?Processor $processor): self
    {
        $this->processor = $processor;

        return $this;
    }

    public function getSize(): ?Dimensions
    {
        return $this->size;
    }

    public function setSize(?Dimensions $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("brand")
     * @Serializer\Groups({"list", "show"})
     */
    public function getSerializedBrand()
    {
        $brand = $this->getBrand();
        $name = $brand->getName();
        $serial = $brand->getSerie();
        $manufacturer = $brand->getManufacturer();

        return $manufacturer." ".$name." ".$serial;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("weight")
     * @Serializer\Groups({"list", "show"})
     */
    public function getSerializedWeight()
    {
        return $this->getWeight()."g";

    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("size")
     * @Serializer\Groups({"list", "show"})
     */
    public function getSerializedSize()
    {
        return $this->getSize()->getSerializedDimensions();
    }
}
