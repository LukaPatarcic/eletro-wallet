<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionTypeRepository")
 */
class TransactionType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("customTransactionList")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customTransactionList", "one_transaction_read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("customTransactionList")
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="transactionType")
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CustomTransactionType", mappedBy="transactionType")
     */
    private $customTransactionTypes;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->customTransactionTypes = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setTransactionType($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getTransactionType() === $this) {
                $transaction->setTransactionType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CustomTransactionType[]
     */
    public function getCustomTransactionTypes(): Collection
    {
        return $this->customTransactionTypes;
    }

    public function addCustomTransactionType(CustomTransactionType $customTransactionType): self
    {
        if (!$this->customTransactionTypes->contains($customTransactionType)) {
            $this->customTransactionTypes[] = $customTransactionType;
            $customTransactionType->setTransactionType($this);
        }

        return $this;
    }

    public function removeCustomTransactionType(CustomTransactionType $customTransactionType): self
    {
        if ($this->customTransactionTypes->contains($customTransactionType)) {
            $this->customTransactionTypes->removeElement($customTransactionType);
            // set the owning side to null (unless already changed)
            if ($customTransactionType->getTransactionType() === $this) {
                $customTransactionType->setTransactionType(null);
            }
        }

        return $this;
    }
}
