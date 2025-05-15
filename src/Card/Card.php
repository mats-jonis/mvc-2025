<?php

namespace App\Card;

class Card
{
    protected $value;

    public function __construct()
    {
        $this->value = null;
    }

    public function shuffle(): int
    {
        $this->value = random_int(1, 52);
        return $this->value;
    }

    public function setValue($arg_1): int
    {
        $this->value = $arg_1;
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
