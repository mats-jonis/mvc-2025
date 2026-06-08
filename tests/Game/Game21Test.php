<?php

namespace App\Tests\Game;

use App\Card\Card;
use App\Card\Deck;
use App\Game\Game21;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Game21.
 */
class Game21Test extends TestCase
{
    /**
     * Build a Deck whose cards are drawn in the given value order.
     *
     * @param int[] $drawOrder
     */
    private function makeDeck(array $drawOrder): Deck
    {
        $deck = new Deck();
        foreach (array_reverse($drawOrder) as $value) {
            $card = new Card();
            $card->setValue($value);
            $deck->add($card);
        }
        return $deck;
    }

    /**
     * A new game uses a full, shuffled deck and starts in playing state.
     */
    public function testCreateGameInitialState(): void
    {
        $game = new Game21();

        $this->assertEquals(52, $game->getCardsLeft());
        $this->assertEquals('playing', $game->getStatus());
        $this->assertEquals(0, $game->getPlayerValue());
        $this->assertEquals(0, $game->getBankValue());
        $this->assertEquals([], $game->getPlayerHandStrings());
        $this->assertEquals([], $game->getBankHandStrings());
    }

    /**
    * playerDraw adds a card to the player's hand and removes it from the deck.
    */
    public function testPlayerDrawAddsCard(): void
    {
        $game = new Game21($this->makeDeck([5, 5, 5]));

        $game->playerDraw();

        $this->assertEquals(5, $game->getPlayerValue());
        $this->assertEquals(2, $game->getCardsLeft());
        $this->assertCount(1, $game->getPlayerHandStrings());
        $this->assertEquals('playing', $game->getStatus());
    }

    /**
     * When the bank busts the player wins.
     */
    public function testStandBankBustsPlayerWins(): void
    {
        $game = new Game21($this->makeDeck([10, 10, 10, 6, 10]));

        $game->playerDraw();
        $game->playerDraw();
        $game->stand();

        $this->assertEquals(26, $game->getBankValue());
        $this->assertEquals('player_win', $game->getStatus());
        $this->assertNotEmpty($game->getBankHandStrings());
    }

    /**
     * The bank wins when its value reaches the player's or higher.
     */
    public function testStandBankWins(): void
    {
        $game = new Game21($this->makeDeck([5, 10, 7]));

        $game->playerDraw();
        $game->stand();

        $this->assertEquals(17, $game->getBankValue());
        $this->assertEquals(5, $game->getPlayerValue());
        $this->assertEquals('bank_win', $game->getStatus());
    }

    /**
     * The player wins when the hand value beats the bank's.
     */
    public function testStandPlayerWinsWithHigherValue(): void
    {
        $game = new Game21($this->makeDeck([10, 10, 10, 7]));

        $game->playerDraw();
        $game->playerDraw();
        $game->stand();

        $this->assertEquals(17, $game->getBankValue());
        $this->assertEquals(20, $game->getPlayerValue());
        $this->assertEquals('player_win', $game->getStatus());
    }

    /**
     * playerDraw is ignored once the game is no longer in playing state.
     */
    public function testPlayerDrawIgnoredWhenGameOver(): void
    {
        $game = new Game21($this->makeDeck([10, 10, 10, 5]));

        $game->playerDraw();
        $game->playerDraw();
        $game->playerDraw();
        $this->assertEquals('player_bust', $game->getStatus());

        $cardsLeft = $game->getCardsLeft();
        $game->playerDraw();

        $this->assertEquals('player_bust', $game->getStatus());
        $this->assertEquals($cardsLeft, $game->getCardsLeft());
    }

    /**
     * The player busts when the hand value exceeds 21.
     */
    public function testPlayerBust(): void
    {
        $game = new Game21($this->makeDeck([10, 10, 10]));

        $game->playerDraw();
        $game->playerDraw();
        $game->playerDraw();

        $this->assertEquals(30, $game->getPlayerValue());
        $this->assertEquals('player_bust', $game->getStatus());
    }


    /**
     * Ace is counted as 14 when that does not exceed 21.
     */
    public function testAceCountsAsFourteenWhenPossible(): void
    {
        $game = new Game21($this->makeDeck([1, 10]));

        $game->playerDraw();

        $this->assertEquals(14, $game->getPlayerValue());
    }

    /**
     * Ace is counted as 1 when counting it as 14 would bust the hand.
     */
    public function testAceCountsAsOneWhenFourteenWouldBust(): void
    {
        $game = new Game21($this->makeDeck([1, 10]));

        $game->playerDraw();
        $game->playerDraw();

        $this->assertEquals(11, $game->getPlayerValue());
    }

    /**
     * stand is ignored once the game is no longer in playing state.
     */
    public function testStandIgnoredWhenGameOver(): void
    {
        $game = new Game21($this->makeDeck([10, 10, 10, 7]));

        $game->playerDraw();
        $game->playerDraw();
        $game->stand();
        $this->assertEquals('player_win', $game->getStatus());

        $bankValue = $game->getBankValue();
        $game->stand();

        $this->assertEquals('player_win', $game->getStatus());
        $this->assertEquals($bankValue, $game->getBankValue());
    }

    /**
    * The bank stops drawing when the deck runs out.
    */
    public function testStandStopsWhenDeckRunsOut(): void
    {
        $game = new Game21($this->makeDeck([2, 5]));

        $game->playerDraw();
        $game->stand();

        $this->assertEquals(5, $game->getBankValue());
        $this->assertEquals(0, $game->getCardsLeft());
        $this->assertEquals('bank_win', $game->getStatus());
    }
}
