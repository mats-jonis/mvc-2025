<?php

namespace App\Tests\Entity;

use App\Entity\GameResult;
use App\Entity\Player;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the GameResult entity: getters, setters and the relation.
 */
class GameResultTest extends TestCase
{
    /**
    * A new result has no id until it is persisted.
    */
    public function testNewResultHasNullId(): void
    {
        $result = new GameResult();
        $this->assertNull($result->getId());
    }

    /**
     * The constructor sets the created-at timestamp automatically.
     */
    public function testConstructorSetsCreatedAt(): void
    {
        $result = new GameResult();
        $this->assertInstanceOf(\DateTimeImmutable::class, $result->getCreatedAt());
    }

    public function testSetAndGetBet(): void
    {
        $result = new GameResult();
        $result->setBet(100);
        $this->assertSame(100, $result->getBet());
    }

    public function testSetAndGetPlayerValue(): void
    {
        $result = new GameResult();
        $result->setPlayerValue(21);
        $this->assertSame(21, $result->getPlayerValue());
    }

    public function testSetAndGetBankValue(): void
    {
        $result = new GameResult();
        $result->setBankValue(17);
        $this->assertSame(17, $result->getBankValue());
    }

    public function testSetAndGetOutcome(): void
    {
        $result = new GameResult();
        $result->setOutcome('win');
        $this->assertSame('win', $result->getOutcome());
    }

    /**
     * The result can be linked to a player.
     */
    public function testSetAndGetPlayer(): void
    {
        $result = new GameResult();
        $player = new Player();
        $result->setPlayer($player);
        $this->assertSame($player, $result->getPlayer());
    }
}
