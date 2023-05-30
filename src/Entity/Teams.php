<?php

namespace App\Entity;

use App\Repository\TeamsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamsRepository::class)]
class Teams
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 132)]
    private ?string $name = null;

    #[ORM\Column(length: 32)]
    private ?string $country = null;

    #[ORM\Column(length: 16)]
    private ?string $money_balance = null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Players", inversedBy="teams")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    // ...

    public function getPlayer(): ?Players
    {
        return $this->player;
    }

    public function setPlayer(Players $player): self
    {
        $this->player = $player;

        return $this;
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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getMoneyBalance(): ?string
    {
        return $this->money_balance;
    }

    public function setMoneyBalance(string $money_balance): self
    {
        $this->money_balance = $money_balance;

        return $this;
    }
}
