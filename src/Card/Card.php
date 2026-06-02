<?php

namespace App\Card;

class Card
{
    protected int $value = 0;

    public function __construct()
    {
        $this->value = 0;
    }

    public function shuffle(): int
    {
        $this->value = random_int(1, 52);
        return $this->value;
    }

    public function setValue(int $value): int
    {
        $this->value = $value;
        return $this->value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getAsString(): string
    {
        return "[{$this->value}]";
    }
}
