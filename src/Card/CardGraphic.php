<?php
namespace App\Card;

class CardGraphic extends Card
{
    private $representation = [
        'img/SPADE-1.svg',
        'img/SPADE-2.svg',
        'img/SPADE-3.svg',
        'img/SPADE-4.svg',
        'img/SPADE-5.svg',
        'img/SPADE-6.svg',
        'img/SPADE-7.svg',
        'img/SPADE-8.svg',
        'img/SPADE-9.svg',
        'img/SPADE-10.svg',
        'img/SPADE-11-JACK.svg',
        'img/SPADE-12-QUEEN.svg',
        'img/SPADE-13-KING.svg',
        'img/HEART-1.svg',
        'img/HEART-2.svg',
        'img/HEART-3.svg',
        'img/HEART-4.svg',
        'img/HEART-5.svg',
        'img/HEART-6.svg',
        'img/HEART-7.svg',
        'img/HEART-8.svg',
        'img/HEART-9.svg',
        'img/HEART-10.svg',
        'img/HEART-11-JACK.svg',
        'img/HEART-12-QUEEN.svg',
        'img/HEART-13-KING.svg',
        'img/DIAMOND-1.svg',
        'img/DIAMOND-2.svg',
        'img/DIAMOND-3.svg',
        'img/DIAMOND-4.svg',
        'img/DIAMOND-5.svg',
        'img/DIAMOND-6.svg',
        'img/DIAMOND-7.svg',
        'img/DIAMOND-8.svg',
        'img/DIAMOND-9.svg',
        'img/DIAMOND-10.svg',
        'img/DIAMOND-11-JACK.svg',
        'img/DIAMOND-12-QUEEN.svg',
        'img/DIAMOND-13-KING.svg',
        'img/CLUB-1.svg',
        'img/CLUB-2.svg',
        'img/CLUB-3.svg',
        'img/CLUB-4.svg',
        'img/CLUB-5.svg',
        'img/CLUB-6.svg',
        'img/CLUB-7.svg',
        'img/CLUB-8.svg',
        'img/CLUB-9.svg',
        'img/CLUB-10.svg',
        'img/CLUB-11-JACK.svg',
        'img/CLUB-12-QUEEN.svg',
        'img/CLUB-13-KING.svg',
    ];

   
    public function __construct()
    {
        parent::__construct();
    }

    public function getAsString(): string
    {
        return $this->representation[$this->value - 1];
    }
}