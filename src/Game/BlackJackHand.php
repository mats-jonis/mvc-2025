<?php

namespace App\Game;

use App\Card\Card;

/**
* Represents a single Black Jack hand with its own bet, status and result.
* A hand owns its cards, knows its own value with ace as 1 or 11,
* and whether it is bust or a natural blackjack.
*/
class BlackJackHand
{
    /** @var Card[] The cards in this hand. */
    private array $cards = [];
    private int $bet;
    /** @var string playing | stand | bust */
    private string $status = 'playing';
    /** @var string '' | win | lose | push | blackjack */
    private string $result = '';

    public function __construct(int $bet = 0)
    {
        $this->bet = $bet;
    }

    /**
     * Add a card to the hand.
     */
    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
    }

    /**
     * Calculate the hand value, counting each ace as 11 when it fits, else 1.
     */
    public function getValue(): int
    {
        $sum = 0;
        $aces = 0;
        foreach ($this->cards as $card) {
            $rank = (($card->getValue() - 1) % 13) + 1; // 1=ace, 11=J, 12=Q, 13=K
            if ($rank === 1) {
                $aces++;
                $sum += 1;
            } else {
                $sum += min($rank, 10);
            }
        }
        // Upgrade aces from 1 to 11 (+10) while it keeps the hand at 21 or below.
        for ($i = 0; $i < $aces; $i++) {
            if ($sum + 10 <= 21) {
                $sum += 10;
            }
        }
        return $sum;
    }

    /**
     * True if the hand is over 21.
     */
    public function isBust(): bool
    {
        return $this->getValue() > 21;
    }

    /**
     * True if the hand is a natural blackjack: 21 on exactly two cards.
     */
    public function isBlackjack(): bool
    {
        return count($this->cards) === 2 && $this->getValue() === 21;
    }

    /**
     * Get the string representation of every card in the hand.
     *
     * @return string[]
     */
    public function getCardStrings(): array
    {
        return array_map(fn (Card $card) => $card->getAsString(), $this->cards);
    }

    public function getBet(): int
    {
        return $this->bet;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
    * @param string $status One of: playing, stand, bust.
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    /**
     * @param string $result One of win, lose, push or blackjack.
     */
    public function setResult(string $result): void
    {
        $this->result = $result;
    }
}
