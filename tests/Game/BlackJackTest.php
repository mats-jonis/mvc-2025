<?php

namespace App\Tests\Game;

use App\Card\Card;
use App\Card\Deck;
use App\Game\BlackJack;
use PHPUnit\Framework\TestCase;

class BlackJackTest extends TestCase
{
    /**
     * Build a deck that yields cards in the given draw order.
     *
     * Deck::draw() uses array_pop, so cards are added in reverse to make
     * the first listed value the first one drawn.
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

    public function testInitialState(): void
    {
        $game = new BlackJack('Mats', 1000, $this->makeDeck([]));
        $this->assertSame('Mats', $game->getPlayerName());
        $this->assertSame(1000, $game->getMoney());
        $this->assertSame('betting', $game->getStatus());
    }

    public function testDealDeductsBetAndStartsPlaying(): void
    {
        // player 10+9=19, bank 10+7=17, no one busts or has blackjack
        $game = new BlackJack('Mats', 1000, $this->makeDeck([10, 9, 10, 7]));
        $game->deal([100]);
        $this->assertSame(900, $game->getMoney());
        $this->assertSame('playing', $game->getStatus());
    }

    public function testPlayerBlackjackPaysThreeToTwo(): void
    {
        // player ace+king = blackjack, bank 10+7=17
        $game = new BlackJack('Mats', 1000, $this->makeDeck([1, 13, 10, 7]));
        $game->deal([100]);
        // 1000 - 100 bet + (100 stake + 150 win) = 1150
        $this->assertSame(1150, $game->getMoney());
        $this->assertSame('finished', $game->getStatus());
    }

    public function testPushReturnsStake(): void
    {
        // player 10+7=17, bank 10+7=17
        $game = new BlackJack('Mats', 1000, $this->makeDeck([10, 7, 10, 7]));
        $game->deal([100]);
        $game->stand();
        $this->assertSame(1000, $game->getMoney());
        $this->assertSame('finished', $game->getStatus());
    }

    public function testPlayerWinsWhenBankBusts(): void
    {
        // player 10+9=19 stand; bank 6+6=12 then draws 10 -> 22 bust
        $game = new BlackJack('Mats', 1000, $this->makeDeck([10, 9, 6, 6, 10]));
        $game->deal([100]);
        $game->stand();
        // 1000 - 100 + 200 = 1100
        $this->assertSame(1100, $game->getMoney());
    }

    public function testPlayerBustLosesAndBankDoesNotDraw(): void
    {
        // player 10+6=16, bank 5+5=10, player hits 10 -> 26 bust
        $game = new BlackJack('Mats', 1000, $this->makeDeck([10, 6, 5, 5, 10]));
        $game->deal([100]);
        $game->hit();
        $this->assertSame(900, $game->getMoney());
        // all player hands busted, so the bank must NOT draw up to 17
        $state = $game->getState();
        $this->assertSame(10, $state['bankValue']);
    }

    public function testBankBlackjackBeatsOrdinaryHand(): void
    {
        // player 10+7=17 stand; bank ace+king = blackjack
        $game = new BlackJack('Mats', 1000, $this->makeDeck([10, 7, 1, 13]));
        $game->deal([100]);
        $game->stand();
        $this->assertSame(900, $game->getMoney());
    }

    public function testBothBlackjackPush(): void
    {
        // player ace+king blackjack; bank ace+king blackjack
        $game = new BlackJack('Mats', 1000, $this->makeDeck([1, 13, 1, 13]));
        $game->deal([100]);
        $this->assertSame(1000, $game->getMoney());
        $this->assertSame('finished', $game->getStatus());
    }

    public function testTwoHandsOneWinsOneLoses(): void
    {
        // hand0 10+9=19, hand1 10+5=15, bank 10+7=17
        $game = new BlackJack('Mats', 1000, $this->makeDeck([10, 9, 10, 5, 10, 7]));
        $game->deal([100, 100]);
        $game->stand(); // hand0 stands
        $game->stand(); // hand1 stands -> bank plays
        // -200 bets, hand0 wins +200, hand1 loses -> 1000
        $this->assertSame(1000, $game->getMoney());
    }

    public function testDealRejectsZeroHands(): void
    {
        $game = new BlackJack('Mats', 1000, $this->makeDeck([]));
        $this->expectException(\InvalidArgumentException::class);
        $game->deal([]);
    }

    public function testDealRejectsMoreThanThreeHands(): void
    {
        $game = new BlackJack('Mats', 1000, $this->makeDeck([]));
        $this->expectException(\InvalidArgumentException::class);
        $game->deal([100, 100, 100, 100]);
    }

    public function testDealRejectsBetOverBalance(): void
    {
        $game = new BlackJack('Mats', 100, $this->makeDeck([]));
        $this->expectException(\InvalidArgumentException::class);
        $game->deal([200]);
    }

    public function testDealRejectsNonPositiveBet(): void
    {
        $game = new BlackJack('Mats', 1000, $this->makeDeck([]));
        $this->expectException(\InvalidArgumentException::class);
        $game->deal([0]);
    }

    public function testHitIsIgnoredDuringBetting(): void
    {
        $game = new BlackJack('Mats', 1000, $this->makeDeck([]));
        $game->hit();
        $this->assertSame('betting', $game->getStatus());
    }

    public function testStandIsIgnoredDuringBetting(): void
    {
        $game = new BlackJack('Mats', 1000, $this->makeDeck([]));
        $game->stand();
        $this->assertSame('betting', $game->getStatus());
    }

    public function testDealIsIgnoredWhenAlreadyPlaying(): void
    {
        $game = new BlackJack('Mats', 1000, $this->makeDeck([10, 9, 10, 7]));
        $game->deal([100]);
        $game->deal([100]); // no-op, already playing
        $this->assertSame(900, $game->getMoney());
        $this->assertSame('playing', $game->getStatus());
    }

    public function testNewRoundResetsButKeepsMoney(): void
    {
        $game = new BlackJack('Mats', 1000, $this->makeDeck([10, 7, 10, 7]));
        $game->deal([100]);
        $game->stand(); // push -> finished, money 1000
        $game->newRound();
        $this->assertSame('betting', $game->getStatus());
        $this->assertSame(1000, $game->getMoney());
    }

    public function testNewRoundIgnoredWhenNotFinished(): void
    {
        $game = new BlackJack('Mats', 1000, $this->makeDeck([]));
        $game->newRound();
        $this->assertSame('betting', $game->getStatus());
    }

    public function testGetStateStructure(): void
    {
        $game = new BlackJack('Mats', 1000, $this->makeDeck([10, 9, 10, 7]));
        $game->deal([100]);
        $state = $game->getState();
        $this->assertSame('Mats', $state['playerName']);
        $this->assertIsArray($state['hands']);
        $this->assertCount(1, $state['hands']);
        $this->assertArrayHasKey('bankValue', $state);
        $this->assertArrayHasKey('cardsLeft', $state);
    }

    public function testDealWorksWithDefaultDeck(): void
    {
        $game = new BlackJack('Mats', 1000);
        $game->deal([100]);
        $this->assertContains($game->getStatus(), ['playing', 'finished']);
        $this->assertGreaterThanOrEqual(900, $game->getMoney());
    }

    public function testNewRoundRebuildsDeckWhenLow(): void
    {
        // only 4 cards, exactly enough for one round leaving deck < 15
        $game = new BlackJack('Mats', 1000, $this->makeDeck([10, 7, 10, 7]));
        $game->deal([100]);
        $game->stand();
        $game->newRound();
        $state = $game->getState();
        $this->assertSame(52, $state['cardsLeft']);
    }
}
