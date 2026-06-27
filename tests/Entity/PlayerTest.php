<?php

namespace App\Tests\Entity;

use App\Entity\GameResult;
use App\Entity\Player;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the Player entity: getters, setters and the relation.
 */
class PlayerTest extends TestCase
{
    /**
     * A new player has no id until it is persisted.
     */
    public function testNewPlayerHasNullId(): void
    {
        $player = new Player();
        $this->assertNull($player->getId());
    }

    public function testSetAndGetName(): void
    {
        $player = new Player();
        $player->setName('Mats');
        $this->assertSame('Mats', $player->getName());
    }

    /**
     * A new player starts with an empty result collection.
     */
    public function testNewPlayerHasNoResults(): void
    {
        $player = new Player();
        $this->assertCount(0, $player->getGameResults());
    }

    /**
     * Adding a result links it both ways and stores it in the collection.
     */
    public function testAddGameResult(): void
    {
        $player = new Player();
        $result = new GameResult();

        $player->addGameResult($result);

        $this->assertCount(1, $player->getGameResults());
        $this->assertTrue($player->getGameResults()->contains($result));
        $this->assertSame($player, $result->getPlayer());
    }

    /**
     * Removing a result detaches it from the player.
     */
    public function testRemoveGameResult(): void
    {
        $player = new Player();
        $result = new GameResult();

        $player->addGameResult($result);
        $player->removeGameResult($result);

        $this->assertCount(0, $player->getGameResults());
    }

}
