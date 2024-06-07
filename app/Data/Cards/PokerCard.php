<?php
namespace App\Data\Cards;

class PokerCard implements FrenchCard {
    
    public function __construct(private FrenchSuit $suit, private int $rank) {
        if ($rank < 2 || $rank > 14 ) {
            throw new CardException("Rank must be between 2 and 14. $rank is not valid");
        }
    }
    
    public function suit(): FrenchSuit {
        return $this->suit;
    }
    
    public function rank(): int {
        return $this->rank;
    }

    public function __toString() {
        $rep = match($this->rank) {
            2,3,4,5,6,7,8,9 => " ".$this->rank,
            10 => $this->rank,
            11 => " J",
            12 => " Q",
            13 => " K",
            14 => " A",
        };
        return $this->suit()->color()."$rep".$this->suit()->unicode()." \033[0m";
    }
    
}