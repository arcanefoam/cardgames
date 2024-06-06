<?php

namespace App\Logic\Games\TrickTaking;

use App\Data\Cards\Card;
use App\Data\Game\Player;

interface Trick {

    /**
     * The Player that will place a card next
     */
    function currentPlayer(): Player;

    /**
     * Play a card
     * @param Player    $player The player making the play
     * @param Card      $card   The card played by the player
     * @param callable  $valid  A function to validate if the play is valid. The callable accepts 
     *                          an array of cards that has been played and the player 
     */
    function play(Player $player, Card $card, callable $valid);
    
    /**
     * The current cards in the trick
     */
    function trick(): array;

    /**
     * True if the Trick is complete, i.e. all players have played a card.
     */
    function complete(): bool;
}