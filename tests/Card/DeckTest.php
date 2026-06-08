<?php

namespace App\Tests\Card;

use App\Card\Card;
use App\Card\Deck;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Deck.
 */
class DeckTest extends TestCase
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
     * Verify getValues returns each card's value in insertion order.
     */
    public function testGetValues(): void
    {
        $deck = new Deck();
        $deck->add($this->makeCard(10));
        $deck->add($this->makeCard(20));

        $res = $deck->getValues();
        $exp = [10, 20];
        $this->assertEquals($exp, $res);
    }

    /**
     * Verify setValues overwrites each existing card's value by index.
     */
    public function testSetValues(): void
    {
        $deck = new Deck();
        $deck->add($this->makeCard(0));
        $deck->add($this->makeCard(0));

        $deck->setValues([3, 8]);

        $res = $deck->getValues();
        $exp = [3, 8];
        $this->assertEquals($exp, $res);
    }

    /**
     * Verify getString returns the string representation of each card.
     */
    public function testGetString(): void
    {
        $deck = new Deck();
        $deck->add($this->makeCard(4));

        $res = $deck->getString();
        $exp = ["[4]"];
        $this->assertEquals($exp, $res);
    }

    /**
     * Verify drawing from an empty deck returns null.
     */
    public function testDrawFromEmptyDeck(): void
    {
        $deck = new Deck();

        $res = $deck->draw();
        $this->assertNull($res);
    }

    /**
     * Verify draw removes and returns the last card.
     */
    public function testDraw(): void
    {
        $deck = new Deck();
        $card = $this->makeCard(7);
        $deck->add($card);

        $res = $deck->draw();
        $this->assertSame($card, $res);

        $res = $deck->getNumberCards();
        $exp = 0;
        $this->assertEquals($exp, $res);
    }

    /**
     * Verify shuffle keeps the same cards and only reorders them.
     */
    public function testShuffleKeepsSameCards(): void
    {
        $deck = new Deck();
        for ($value = 1; $value <= 52; $value++) {
            $deck->add($this->makeCard($value));
        }

        $before = $deck->getValues();
        $deck->shuffle();
        $after = $deck->getValues();

        $this->assertCount(52, $after);

        sort($before);
        sort($after);
        $this->assertEquals($before, $after);
    }

}
