<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups("info")
     * @Assert\NotBlank(message="This field is required")
     * @Assert\Email(message="Email is not valid")
     *
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="float")
     * @Groups("info")
     */
    private $balance = 0;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="This field is required")
     * @Assert\Length(
     *     min="2",
     *     max="50",
     *     minMessage="Your username should be at least {{ limit }} long",
     *     maxMessage="Your username is too long. Max number of characters is {{ limit }}"
     * )
     */
    private $profileName;

    /**
     * @ORM\Column(type="boolean")
     */
    private $verified = 0;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("info")
     */
    private $chartType = "pie";


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="user")
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CustomTransactionType", mappedBy="user")
     */
    private $customTransactionTypes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasTutorial = 1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hashedEmail;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->customTransactionTypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getProfileName(): ?string
    {
        return $this->profileName;
    }

    public function setProfileName($profileName): self
    {
        $this->profileName = $profileName;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): ?string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): self
    {
        $this->verified = $verified;

        return $this;
    }

    public function getChartType(): ?string
    {
        return $this->chartType;
    }

    public function setChartType(string $chartType): self
    {
        $this->chartType = $chartType;

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
            $transaction->setUser($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getUser() === $this) {
                $transaction->setUser(null);
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
            $customTransactionType->setUser($this);
        }

        return $this;
    }

    public function removeCustomTransactionType(CustomTransactionType $customTransactionType): self
    {
        if ($this->customTransactionTypes->contains($customTransactionType)) {
            $this->customTransactionTypes->removeElement($customTransactionType);
            // set the owning side to null (unless already changed)
            if ($customTransactionType->getUser() === $this) {
                $customTransactionType->setUser(null);
            }
        }

        return $this;
    }

    public function getHasTutorial(): ?bool
    {
        return $this->hasTutorial;
    }

    public function setHasTutorial(bool $hasTutorial): self
    {
        $this->hasTutorial = $hasTutorial;

        return $this;
    }

    public function getHashedEmail(): ?string
    {
        return $this->hashedEmail;
    }

    public function setHashedEmail(): self
    {
        $this->hashedEmail = md5($this->getEmail());

        return $this;
    }
}
