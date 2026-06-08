<?php

namespace App\Game;

use App\Card\Card;
use App\Card\CardGraphic;
use App\Card\Deck;

class Game21
{
    private Deck $deck;
    /** @var Card[] */
    private array $playerHand = [];
    /** @var Card[] */
    private array $bankHand = [];
    // playing | player_bust | player_win | bank_win
    private string $status = 'playing';
    private int $bankStopValue = 17;

    public function __construct(?Deck $deck = null)
    {
        if ($deck !== null) {
            $this->deck = $deck;
            return;
        }

        $this->deck = new Deck();

        for ($i = 1; $i <= 52; $i++) {
            $card = new CardGraphic();
            $card->setValue($i);
            $this->deck->add($card);
        }
        $this->deck->shuffle();
    }

    public function playerDraw(): void
    {
        if ($this->status !== 'playing') {
            return;
        }
        $card = $this->deck->draw();
        if ($card !== null) {
            $this->playerHand[] = $card;
        }
        if ($this->getPlayerValue() > 21) {
            $this->status = 'player_bust';
        }
    }

    public function stand(): void
    {
        if ($this->status !== 'playing') {
            return;
        }
        // Banks turn,  draw until 17 or more
        while ($this->getBankValue() < $this->bankStopValue) {
            $card = $this->deck->draw();
            if ($card === null) {
                break;
            }
            $this->bankHand[] = $card;
        }
        $this->decideWinner();
    }

    private function decideWinner(): void
    {
        $bank = $this->getBankValue();
        $player = $this->getPlayerValue();

        if ($bank > 21) {
            $this->status = 'player_win';
            return;
        }
        if ($bank >= $player) {
            $this->status = 'bank_win';
            return;
        }
        $this->status = 'player_win';
    }

    public function getPlayerValue(): int
    {
        return $this->handValue($this->playerHand);
    }

    public function getBankValue(): int
    {
        return $this->handValue($this->bankHand);
    }

    private function cardValue(Card $card): int
    {
        $rank = (($card->getValue() - 1) % 13) + 1; // 1=ess, 11=J, 12=Q, 13=K
        if ($rank === 1) {
            return 1; // ace counts as one
        }
        return min($rank, 10); // J/Q/K = 10
    }

    /** @param Card[] $hand */
    private function handValue(array $hand): int
    {
        $sum = 0;
        $aces = 0;
        foreach ($hand as $card) {
            $value = $this->cardValue($card);
            $sum += $value;
            if ($value === 1) {
                $aces++;
            }
        }
        // Ace may count as 14 instead of 1 if doing so does not go over 21
        for ($i = 0; $i < $aces; $i++) {
            if ($sum + 13 <= 21) {
                $sum += 13;
            }
        }
        return $sum;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCardsLeft(): int
    {
        return $this->deck->getNumberCards();
    }

    /** @return string[] */
    public function getPlayerHandStrings(): array
    {
        return array_map(fn (Card $card) => $card->getAsString(), $this->playerHand);
    }

    /** @return string[] */
    public function getBankHandStrings(): array
    {
        return array_map(fn (Card $card) => $card->getAsString(), $this->bankHand);
    }
}
