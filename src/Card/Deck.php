<?php

namespace App\Card;

class Deck
{
    /** @var Card[] */
    private array $deck = [];

    public function add(Card $card): void
    {
        $this->deck[] = $card;
    }

    public function shuffle(): void
    {
        shuffle($this->deck);
    }

    public function getNumberCards(): int
    {
        return count($this->deck);
    }

    /** @return int[] */
    public function getValues(): array
    {
        $values = [];
        foreach ($this->deck as $card) {
            $values[] = $card->getValue();
        }
        return $values;
    }

    /** @param int[] $values */
    public function setValues(array $values): void
    {
        foreach ($values as $index => $value) {
            $this->deck[$index] ->setValue($value);
        }
    }

    /** @return string[] */
    public function getString(): array
    {
        $values = [];
        foreach ($this->deck as $card) {
            $values[] = $card->getAsString();
        }
        return $values;
    }

    public function draw(): ?Card
    {
        return array_pop($this->deck);
    }
}
