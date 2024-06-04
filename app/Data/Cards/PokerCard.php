<?php
namespace App\Data\Cards;

class PokerCard implements FrenchCard {
    
    public function __construct(private FrenchSuit $suit, private int $rank) {
        if ($rank < 1 || $rank > 14 ) {
            throw new CardException("Rank must be between 2 and 14. $rank is not valid");
        }
    }
    
    public function suit(): FrenchSuit {
        return $this->suit;
    }
    
    public function rank(): int {
        return $this->rank;
    }
    
}