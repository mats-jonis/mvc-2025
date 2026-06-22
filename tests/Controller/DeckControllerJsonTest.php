<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Functional tests for the JSON API endpoints in DeckControllerJson.
 */
class DeckControllerJsonTest extends WebTestCase
{
    /**
     * GET /api/ should return a JSON list of all available routes.
     */
    public function testAllRoutesReturnsJsonList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $data = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('All JSON routes on the site:', $data);
    }

    /**
     * GET /api/deck should return all 52 cards.
     */
    public function testDeckReturns52Cards(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/deck');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $data = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertCount(52, $data['All cards in the deck:']);
    }

    /**
     * GET /api/deck/shuffle should return a shuffled deck of 52 cards.
     */
    public function testShuffleReturns52Cards(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/deck/shuffle');

        $this->assertResponseIsSuccessful();

        $data = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertCount(52, $data['All shuffled cards in the deck:']);
    }

    /**
     * Drawing without shuffling first should return the "no cards" message
     * and an empty deck (covers the false branch of the session check).
     */
    public function testDrawWithoutShuffleReturnsNoCardsMessage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/deck/draw');

        $this->assertResponseIsSuccessful();

        $data = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertStringContainsString('no cards', $data['message']);
        $this->assertSame(0, $data['numCards']);
    }

    /**
     * After shuffling, drawing one card should leave 51 in the deck
     * (covers the true branch of the session check).
     */
    public function testDrawAfterShuffleReturnsOneCard(): void
    {
        $client = static::createClient();
        // Samma client behåller sessionen mellan anropen, så den blandade
        // kortleken från shuffle finns kvar vid draw.
        $client->request('GET', '/api/deck/shuffle');
        $client->request('GET', '/api/deck/draw');

        $this->assertResponseIsSuccessful();

        $data = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertSame('', $data['message']);
        $this->assertSame(51, $data['numCards']);
        $this->assertCount(1, $data['drawnCards']);
    }

    /**
     * After shuffling, drawing three cards should leave 49 in the deck.
     */
    public function testDrawMultipleAfterShuffle(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/deck/shuffle');
        $client->request('GET', '/api/deck/draw/3');

        $this->assertResponseIsSuccessful();

        $data = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertSame(49, $data['numCards']);
        $this->assertCount(3, $data['drawnCards']);
    }

    /**
     * Drawing more cards than the deck holds should empty it and set the
     * "no cards" message (covers the break/null branch inside the loop).
     */
    public function testDrawMoreThanAvailableEmptiesDeck(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/deck/shuffle');
        $client->request('GET', '/api/deck/draw/60');

        $this->assertResponseIsSuccessful();

        $data = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertSame(0, $data['numCards']);
        $this->assertCount(52, $data['drawnCards']);
        $this->assertStringContainsString('no cards', $data['message']);
    }

    /**
     * GET /api/game with no active game should report that none is running
     * (covers the else branch of apiGame).
     */
    public function testApiGameWithoutActiveGame(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/game');

        $this->assertResponseIsSuccessful();

        $data = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $data);
        $this->assertStringContainsString('No active game', $data['message']);
    }
}
