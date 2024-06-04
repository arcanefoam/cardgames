<?php
namespace App\Data\Cards;

interface Deck {
    
    function shuffle(): Deck;
    function reset(): Deck;
    function drawOne(): Card;
    function draw(int $count): array;
    
}

