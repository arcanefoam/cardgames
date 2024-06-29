<?php

namespace App\Data\Cards;

use App\Data\Game\Player;

interface CardPlayer extends Player {

    /**
     * Returns true if the player has cards of the provided suit.
     */
    function hasOfSuit(FrenchSuit $suit): bool;

    /**
     * Start a hand with the provided cards
     */
    function startHand(array $cards);

    function take(Card $card): void;

    /**
     * 
     * @return List<Card>
     */
    function hand(): array;
}