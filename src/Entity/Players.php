<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\PlayersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayersRepository::class)]
class Players
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 132)]
    private ?string $name = null;

    #[ORM\Column(length: 32)]
    private ?string $surname = null;

    #[ORM\Column]
    private ?int $team_id = null;

    #[ORM\Column(length: 16, nullable: true)]
    private ?string $purchase_amount = null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Teams", mappedBy="players")
     */
    private $team;

    // ...

    public function getTeam(): ?Teams
    {
        return $this->team;
    }

    public function setTeam(Teams $team): self
    {
        $this->team = $team;
        $team->setPlayer($this);

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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getTeamId(): ?int
    {
        return $this->team_id;
    }

    public function setTeamId(int $team_id): self
    {
        $this->team_id = $team_id;

        return $this;
    }

    public function getPurchaseAmount(): ?string
    {
        return $this->purchase_amount;
    }

    public function setPurchaseAmount(?string $purchase_amount): self
    {
        $this->purchase_amount = $purchase_amount;

        return $this;
    }
}
