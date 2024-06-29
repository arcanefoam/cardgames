<?php

namespace App\Logic\Games\TrickTaking;

use App\Data\Cards\Card;
use App\Data\Cards\Deck;
use App\Data\Game\Player;

interface Hand {


    /**
     * Start the Hand with the given deck
     */
    function start(Deck $deck): Hand;

    /**
     * Play the next trick in the hand, if possible
     */
    function nextTrick(): Hand;

    /**
     * Return true if the current trick is complete
     */
    function trickComplete(): bool;

    /**
     * Return the cards played in the current trick
     */
    function currentTrick(): array;

    /**
     * Pick the Player that will be the initial Lead
     */
    function nextLead(): Player;

     /**
     * True if the hand is complete, i.e. all tricks have been played.
     */
    function complete(): bool;

    /**
     * After the hand is complete, the player scores can be calculated
     */
    function scores(): array;

    /**
     * Play a card
     * @param Player    $player The player making the play
     * @param Card      $card   The card played by the player
     */
    function play(Player $player, Card $card);

    function currentPlayer(): Player;

}
