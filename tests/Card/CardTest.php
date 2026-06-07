<?php

namespace App\Tests\Card;

use App\Card\Card;
use App\Card\Deck;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Card.
 */
class CardTest extends TestCase
{
    /**
     * Helper that builds a Card with a given value.
     */
    private function makeCard(int $value): Card
    {
        $card = new Card();
        $card->setValue($value);
        return $card;
    }

    /**
     * Verify that adding cards increases the card count.
     */
    public function testAddAndCount(): void
    {
        $deck = new Deck();

        $res = $deck->getNumberCards();
        $exp = 0;
        $this->assertEquals($exp, $res);

        $deck->add($this->makeCard(1));
        $deck->add($this->makeCard(2));

        $res = $deck->getNumberCards();
        $exp = 2;
        $this->assertEquals($exp, $res);
    }

    /**
     * Set a value and verify it is stored and returned.
     */
    public function testSetAndGetValue(): void
    {
        $card = new Card();

        $res = $card->setValue(7);
        $exp = 7;
        $this->assertEquals($exp, $res);

        $res = $card->getValue();
        $exp = 7;
        $this->assertEquals($exp, $res);
    }

    /**
    * Shuffle assigns a random value within the range 1 to 52.
    */
    public function testShuffleReturnsValueInRange(): void
    {
        $card = new Card();
        $res = $card->shuffle();

        $this->assertGreaterThanOrEqual(1, $res);
        $this->assertLessThanOrEqual(52, $res);
        $this->assertEquals($res, $card->getValue());
    }

    /**
    * Verify the string representation wraps the value in brackets.
    */
    public function testGetAsString(): void
    {
        $card = new Card();
        $card->setValue(5);

        $res = $card->getAsString();
        $exp = "[5]";
        $this->assertEquals($exp, $res);
    }

}
