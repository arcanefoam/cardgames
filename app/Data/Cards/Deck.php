<?php
namespace App\Data\Cards;

interface Deck {
    
    function shuffle(): Deck;
    function draw(): Card;
    function place(Card $card): Deck;
    function reset(): Deck;
    

}

