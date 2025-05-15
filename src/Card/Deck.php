<?php

namespace App\Card;

class Deck
{
    private $deck = [];

    public function add(Card $card): void
    {
        $this->deck[] = $card;
    }

    public function shuffle(): void
    {
        foreach ($this->deck as $card) {
            $card->shuffle($this->deck);
        }
    }

    public function getNumberCards(): int
    {
        return count($this->deck);
    }

    public function getValues(): array
    {
        $values = [];
        foreach ($this->deck as $card) {
            $values[] = $card->getValue();
        }
        return $values;
    }

    public function setValues(array $values): void
    {
        foreach ($values as $index => $value) {
            $this->deck[$index] ->setValue($value);
        }
    }


    public function getString(): array
    {
        $values = [];
        foreach ($this->deck as $card) {
            $values[] = $card->getAsString();
        }
        return $values;
    }

    public function draw(): ?card
    {
        return array_pop($this->deck);
    }
}
