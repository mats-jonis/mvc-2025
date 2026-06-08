<?php

namespace App\Card;

/**
 * Represents a deck of playing cards.
 *
 * Cards can be added, shuffled and drawn from the top.
 */
class Deck
{
    /** @var Card[] The cards currently in the deck. */
    private array $deck = [];

    /**
    * Add a card to the deck.
    */
    public function add(Card $card): void
    {
        $this->deck[] = $card;
    }

    /**
     * Shuffle the cards in the deck into a random order.
     */
    public function shuffle(): void
    {
        shuffle($this->deck);
    }

    /**
     * Get the number of cards left in the deck.
     */
    public function getNumberCards(): int
    {
        return count($this->deck);
    }

    /**
     * Get the value of every card in the deck.
     *
     * @return int[] The numeric value of each card, in order.
     */
    public function getValues(): array
    {
        $values = [];
        foreach ($this->deck as $card) {
            $values[] = $card->getValue();
        }
        return $values;
    }

    /**
     * Set the value of each card by index.
     *
     * @param int[] $values The new values, applied to the cards in order.
     */
    public function setValues(array $values): void
    {
        foreach ($values as $index => $value) {
            $this->deck[$index] ->setValue($value);
        }
    }

    /**
     * Get the string representation of every card in the deck.
     *
     * @return string[] The string representation of each card, in order.
     */
    public function getString(): array
    {
        $values = [];
        foreach ($this->deck as $card) {
            $values[] = $card->getAsString();
        }
        return $values;
    }

    /**
     * Draw and remove the top card from the deck.
     *
     * @return Card|null The drawn card, or null if the deck is empty.
     */
    public function draw(): ?Card
    {
        return array_pop($this->deck);
    }
}
