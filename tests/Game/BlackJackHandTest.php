<?php

namespace App\Tests\Game;

use App\Card\Card;
use App\Game\BlackJackHand;
use PHPUnit\Framework\TestCase;

class BlackJackHandTest extends TestCase
{
    /**
     * Build a Card with a given value.
     */
    private function card(int $value): Card
    {
        $card = new Card();
        $card->setValue($value);
        return $card;
    }

    public function testEmptyHandHasZeroValue(): void
    {
        $hand = new BlackJackHand();
        $this->assertSame(0, $hand->getValue());
    }

    public function testSimpleHandValue(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard($this->card(10)); // ten of spades
        $hand->addCard($this->card(7));  // seven of spades
        $this->assertSame(17, $hand->getValue());
    }

    public function testFaceCardsCountAsTen(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard($this->card(11)); // jack
        $hand->addCard($this->card(12)); // queen
        $this->assertSame(20, $hand->getValue());
    }

    public function testAceCountsAsElevenWhenItFits(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard($this->card(1)); // ace
        $hand->addCard($this->card(6));
        $this->assertSame(17, $hand->getValue());
    }

    public function testAceDropsToOneWhenElevenWouldBust(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard($this->card(1));  // ace
        $hand->addCard($this->card(6));
        $hand->addCard($this->card(10)); // now 11 would bust -> ace = 1
        $this->assertSame(17, $hand->getValue());
    }

    public function testMultipleAces(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard($this->card(1));  // ace of spades
        $hand->addCard($this->card(14)); // ace of hearts
        $hand->addCard($this->card(9));
        // one ace as 11, one as 1: 11 + 1 + 9 = 21
        $this->assertSame(21, $hand->getValue());
    }

    public function testIsBust(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard($this->card(10));
        $hand->addCard($this->card(10));
        $hand->addCard($this->card(5));
        $this->assertTrue($hand->isBust());
    }

    public function testIsNotBustAtTwentyOne(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard($this->card(10));
        $hand->addCard($this->card(11)); // jack
        $hand->addCard($this->card(1));  // ace = 1 -> 21
        $this->assertFalse($hand->isBust());
    }

    public function testBlackjackTrueOnTwoCardTwentyOne(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard($this->card(1));  // ace
        $hand->addCard($this->card(13)); // king
        $this->assertTrue($hand->isBlackjack());
    }

    public function testBlackjackFalseOnThreeCardTwentyOne(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard($this->card(7));
        $hand->addCard($this->card(7));
        $hand->addCard($this->card(7));
        $this->assertSame(21, $hand->getValue());
        $this->assertFalse($hand->isBlackjack());
    }

    public function testBetStatusAndResultAccessors(): void
    {
        $hand = new BlackJackHand(250);
        $this->assertSame(250, $hand->getBet());
        $this->assertSame('playing', $hand->getStatus());
        $this->assertSame('', $hand->getResult());

        $hand->setStatus('stand');
        $hand->setResult('win');
        $this->assertSame('stand', $hand->getStatus());
        $this->assertSame('win', $hand->getResult());
    }

    public function testGetCardStrings(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard($this->card(1));
        $hand->addCard($this->card(13));
        $this->assertSame(['[1]', '[13]'], $hand->getCardStrings());
    }
}
