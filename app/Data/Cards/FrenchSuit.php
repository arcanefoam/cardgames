<?php
namespace App\Data\Cards;

enum FrenchSuit : string {
    case Clubs = 'C';
    case Diamonds = 'D';
    case Hearts = 'H';
    case Spades = 'S';

    public function color(): string {
        return match($this) {
            FrenchSuit::Hearts, FrenchSuit::Diamonds => "\033[0;31m",
            FrenchSuit::Clubs, FrenchSuit::Spades => "\033[1;30m",
        };
    }

    public function unicode(): string {
        return match($this) {
            FrenchSuit::Clubs =>    "\u{2663}",
            FrenchSuit::Diamonds => "\u{2666}",
            FrenchSuit::Hearts =>   "\u{2665}",
            FrenchSuit::Spades =>   "\u{2660}",
        };
    }
}