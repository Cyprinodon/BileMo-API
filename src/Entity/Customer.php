<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use \DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 */
class Customer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"show", "default", "list"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=127)
     * @Serializer\Groups({"show", "default", "list"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=127)
     * @Serializer\Groups({"show", "default", "list"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=StoreAccount::class, inversedBy="buyers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $storeAccount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

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

    public function getStoreAccount(): ?StoreAccount
    {
        return $this->storeAccount;
    }

    public function setStoreAccount(?StoreAccount $storeAccount): self
    {
        $this->storeAccount = $storeAccount;

        return $this;
    }
}
