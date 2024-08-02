<?php

namespace App\Entity;

use App\Repository\ContractRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContractRepository::class)]
class Contract
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Token $token = null;

    #[ORM\Column]
    private ?float $rate = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?bool $initialized = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $issuer = null;

    /**
     * @var Collection<int, UserContract>
     */
    #[ORM\OneToMany(targetEntity: UserContract::class, mappedBy: 'contract', orphanRemoval: true)]
    private Collection $users;

    #[ORM\Column]
    private ?int $claimMonths = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $fundsReached = false;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getToken(): ?Token
    {
        return $this->token;
    }

    public function setToken(?Token $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isInitialized(): ?bool
    {
        return $this->initialized;
    }

    public function setInitialized(bool $initialized): static
    {
        $this->initialized = $initialized;

        return $this;
    }

    public function getIssuer(): ?User
    {
        return $this->issuer;
    }

    public function setIssuer(?User $issuer): static
    {
        $this->issuer = $issuer;

        return $this;
    }

    /**
     * @return Collection<int, UserContract>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(UserContract $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setContract($this);
        }

        return $this;
    }

    public function removeUser(UserContract $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getContract() === $this) {
                $user->setContract(null);
            }
        }

        return $this;
    }

    public function getClaimMonths(): ?int
    {
        return $this->claimMonths;
    }

    public function setClaimMonths(int $claimMonths): static
    {
        $this->claimMonths = $claimMonths;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isFundsReached(): ?bool
    {
        return $this->fundsReached;
    }

    public function setFundsReached(bool $fundsReached): static
    {
        $this->fundsReached = $fundsReached;

        return $this;
    }
}
