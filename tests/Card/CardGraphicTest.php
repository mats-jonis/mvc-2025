<?php

namespace App\Tests\Card;

use App\Card\Card;
use App\Card\CardGraphic;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for class CardGraphic.
 */
class CardGraphicTest extends TestCase
{
    /**
     * Construct object and verify it is both a CardGraphic and a Card.
    */
    public function testCreateCardGraphic(): void
    {
        $card = new CardGraphic();
        $this->assertInstanceOf("\App\Card\CardGraphic", $card);
        $this->assertInstanceOf("\App\Card\Card", $card);
    }

    /**
     * Verify getAsString returns the img path matching the card value.
    */
    public function testGetAsStringReturnsRepresentation(): void
    {
        $card = new CardGraphic();

        $card->setValue(1);
        $res = $card->getAsString();
        $exp = "img/SPADE-1.svg";
        $this->assertEquals($exp, $res);

        $card->setValue(14);
        $res = $card->getAsString();
        $exp = "img/HEART-1.svg";
        $this->assertEquals($exp, $res);

        $card->setValue(52);
        $res = $card->getAsString();
        $exp = "img/CLUB-13-KING.svg";
        $this->assertEquals($exp, $res);
    }
}
