<?php
namespace App\Data\Cards;

interface FrenchCard extends Card {
    
    function suit(): FrenchSuit;
    function rank(): int;
}

enum FrenchSuit : string {
    case Clubs = 'C';
    case Diamonds = 'D';
    case Hearts = 'H';
    case Spades = 'S';
}

