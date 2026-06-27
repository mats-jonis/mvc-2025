<?php

namespace App\Entity;

use App\Repository\GameResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameResultRepository::class)]
class GameResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /** @phpstan-ignore property.unusedType */
    private ?int $id = null;

    #[ORM\Column]
    private ?int $bet = null;

    #[ORM\Column]
    private ?int $playerValue = null;

    #[ORM\Column]
    private ?int $bankValue = null;

    #[ORM\Column(length: 20)]
    private ?string $outcome = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'gameResults')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Player $player = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBet(): ?int
    {
        return $this->bet;
    }

    public function setBet(int $bet): static
    {
        $this->bet = $bet;

        return $this;
    }

    public function getPlayerValue(): ?int
    {
        return $this->playerValue;
    }

    public function setPlayerValue(int $playerValue): static
    {
        $this->playerValue = $playerValue;

        return $this;
    }

    public function getBankValue(): ?int
    {
        return $this->bankValue;
    }

    public function setBankValue(int $bankValue): static
    {
        $this->bankValue = $bankValue;

        return $this;
    }

    public function getOutcome(): ?string
    {
        return $this->outcome;
    }

    public function setOutcome(string $outcome): static
    {
        $this->outcome = $outcome;

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

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): static
    {
        $this->player = $player;

        return $this;
    }
}
