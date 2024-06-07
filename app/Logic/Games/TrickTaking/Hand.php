<?php

namespace App\Logic\Games\TrickTaking;

use App\Data\Cards\Card;
use App\Data\Game\Player;

interface Hand {

    /**
     * The trick being played
     */
    function nextTrick(): Hand;

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
     * @param callable  $valid  A function to validate if the play is valid. The callable accepts 
     *                          an array of cards that has been played and the player 
     */
    function play(Player $player, Card $card, callable $valid);

}
