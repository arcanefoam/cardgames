<?php

namespace App\Logic\Games\TrickTaking;

use App\Logic\Games\Player;

interface Hand {

    /**
     * The trick being played
     */
    function currentTrick(): Trick;

    /**
     * Pick the Player that will be the initial Lead
     */
    function initialLead(): Player;

     /**
     * True if the hand is complete, i.e. all tricks have been played.
     */
    function complete(): bool;

    /**
     * After the hand is complete, the player scores can be calculated
     */
    function scores(): array;

    /**
     * Determine the next Player to lead based on the provided Trick.
     * 
     * This will depends on the rules of the hand.
     */
    function nextLead(array $trick): Player;
}
