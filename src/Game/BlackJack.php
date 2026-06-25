<?php

namespace App\Game;

use App\Card\CardGraphic;
use App\Card\Deck;

/**
 * Black Jack game where the player plays 1 to 3 hands against the bank,
 * betting money from a personal balance.
 *
 * Black Jack rules: the bank draws to 17, a natural blackjack
 * pays 3:2, an equal value is a push (stake returned), otherwise 1:1.
 */
class BlackJack
{
    private string $playerName;
    private int $money;
    private Deck $deck;
    /** @var BlackJackHand[] The player's hands for the current round. */
    private array $hands = [];
    private BlackJackHand $bankHand;
    private int $currentHandIndex = 0;
    /** @var string betting | playing | finished */
    private string $status = 'betting';
    private int $bankStopValue = 17;

    public function __construct(string $playerName, int $startMoney, ?Deck $deck = null)
    {
        $this->playerName = $playerName;
        $this->money = $startMoney;
        $this->deck = $deck ?? $this->buildDeck();
        $this->bankHand = new BlackJackHand(0);
    }

    /**
     * Build a new shuffled 52-card deck.
     */
    private function buildDeck(): Deck
    {
        $deck = new Deck();
        for ($i = 1; $i <= 52; $i++) {
            $card = new CardGraphic();
            $card->setValue($i);
            $deck->add($card);
        }
        $deck->shuffle();
        return $deck;
    }

    /**
     * Start a round by placing 1 to 3 bets and dealing the opening cards.
     *
     * @param int[] $bets One bet per hand. Total must not exceed the balance.
     * @throws \InvalidArgumentException On an invalid number of hands or bet amount.
     */
    public function deal(array $bets): void
    {
        if ($this->status !== 'betting') {
            return;
        }
        $count = count($bets);
        if ($count < 1 || $count > 3) {
            throw new \InvalidArgumentException('You must play between 1 and 3 hands.');
        }
        $total = 0;
        foreach ($bets as $bet) {
            if ($bet <= 0) {
                throw new \InvalidArgumentException('Every bet must be greater than zero.');
            }
            $total += $bet;
        }
        if ($total > $this->money) {
            throw new \InvalidArgumentException('You cannot bet more than your balance.');
        }

        foreach ($bets as $bet) {
            $hand = new BlackJackHand($bet);
            $this->money -= $bet;
            $this->dealCardTo($hand);
            $this->dealCardTo($hand);
            if ($hand->isBlackjack()) {
                $hand->setStatus('stand');
            }
            $this->hands[] = $hand;
        }
        $this->dealCardTo($this->bankHand);
        $this->dealCardTo($this->bankHand);

        $this->status = 'playing';
        $this->currentHandIndex = 0;
        $this->advanceToNextPlayableHand();
    }

    /**
     * Draw a card from the deck into a hand, ignoring an empty deck.
     */
    private function dealCardTo(BlackJackHand $hand): void
    {
        $card = $this->deck->draw();
        if ($card !== null) {
            $hand->addCard($card);
        }
    }

    /**
     * The current hand draws a card. Busting advances to the next hand.
     */
    public function hit(): void
    {
        if ($this->status !== 'playing') {
            return;
        }
        $hand = $this->currentHand();
        if ($hand === null) {
            return;
        }
        $this->dealCardTo($hand);
        if ($hand->isBust()) {
            $hand->setStatus('bust');
            $this->advanceToNextPlayableHand();
        }
    }

    /**
     * The current hand stands; play moves to the next hand.
     */
    public function stand(): void
    {
        if ($this->status !== 'playing') {
            return;
        }
        $hand = $this->currentHand();
        if ($hand === null) {
            return;
        }
        $hand->setStatus('stand');
        $this->advanceToNextPlayableHand();
    }

    /**
     * The hand currently being played, or null when the round is over.
     */
    private function currentHand(): ?BlackJackHand
    {
        return $this->hands[$this->currentHandIndex] ?? null;
    }

    /**
     * Skip past finished hands. When none remain, the bank plays and the
     * round is settled.
     */
    private function advanceToNextPlayableHand(): void
    {
        while ($this->currentHandIndex < count($this->hands)) {
            if ($this->hands[$this->currentHandIndex]->getStatus() === 'playing') {
                return;
            }
            $this->currentHandIndex++;
        }
        $this->playBank();
        $this->settle();
        $this->status = 'finished';
    }

    /**
     * The bank draws until reaching its stop value, unless every player
     * hand has already busted.
     */
    private function playBank(): void
    {
        $anyAlive = false;
        foreach ($this->hands as $hand) {
            if ($hand->getStatus() !== 'bust') {
                $anyAlive = true;
                break;
            }
        }
        if (!$anyAlive) {
            return;
        }
        while ($this->bankHand->getValue() < $this->bankStopValue) {
            $card = $this->deck->draw();
            if ($card === null) {
                break;
            }
            $this->bankHand->addCard($card);
        }
    }

    /**
     * Compare every player hand against the bank and adjust the balance.
     */
    private function settle(): void
    {
        $bankValue = $this->bankHand->getValue();
        $bankBust = $bankValue > 21;
        $bankBlackjack = $this->bankHand->isBlackjack();

        foreach ($this->hands as $hand) {
            $bet = $hand->getBet();
            $playerBlackjack = $hand->isBlackjack();

            if ($hand->getStatus() === 'bust') {
                $hand->setResult('lose');
                continue;
            }
            if ($bankBlackjack) {
                if ($playerBlackjack) {
                    $hand->setResult('push');
                    $this->money += $bet;
                } else {
                    $hand->setResult('lose');
                }
                continue;
            }
            if ($playerBlackjack) {
                $hand->setResult('blackjack');
                $this->money += $bet + (int) round($bet * 1.5);
                continue;
            }
            if ($bankBust || $hand->getValue() > $bankValue) {
                $hand->setResult('win');
                $this->money += $bet * 2;
            } elseif ($hand->getValue() === $bankValue) {
                $hand->setResult('push');
                $this->money += $bet;
            } else {
                $hand->setResult('lose');
            }
        }
    }

    /**
    * Begin a new round, keeping the player's balance. The deck is rebuilt
    * when it runs low.
    */
    public function newRound(): void
    {
        if ($this->status !== 'finished') {
            return;
        }
        $this->hands = [];
        $this->bankHand = new BlackJackHand(0);
        $this->currentHandIndex = 0;
        if ($this->deck->getNumberCards() < 15) {
            $this->deck = $this->buildDeck();
        }
        $this->status = 'betting';
    }

    /**
     * A serializable snapshot of the game, for Twig and the JSON API.
     *
     * @return array<string, mixed>
     */
    public function getState(): array
    {
        $hands = [];
        foreach ($this->hands as $hand) {
            $hands[] = [
                'cards'  => $hand->getCardStrings(),
                'value'  => $hand->getValue(),
                'bet'    => $hand->getBet(),
                'status' => $hand->getStatus(),
                'result' => $hand->getResult(),
            ];
        }
        return [
            'playerName'  => $this->playerName,
            'money'       => $this->money,
            'status'      => $this->status,
            'currentHand' => $this->currentHandIndex,
            'hands'       => $hands,
            'bankCards'   => $this->bankHand->getCardStrings(),
            'bankValue'   => $this->bankHand->getValue(),
            'cardsLeft'   => $this->deck->getNumberCards(),
        ];
    }

    public function getPlayerName(): string
    {
        return $this->playerName;
    }

    public function getMoney(): int
    {
        return $this->money;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
