<?php

namespace App\Logic\Games\TrickTaking;

use Illuminate\Support\Facades\Log;

use App\Data\Cards\Card;
use App\Data\Game\Player;
use Exception;

class SimpleTrick implements Trick {

    /**
     * @param validPlay  a function that evaluates if the card played by the player is valid for the trick
     */
    public function __construct(
        private array $players,
        int $leadId) {
        $this->currentPlayer = $leadId;
        $this->trick = [];
        while (current($this->players) !== $this->players[$this->currentPlayer]) next($this->players);
    }

    function currentPlayer(): Player {
        return $this->players[$this->currentPlayer];
    }

    function trick(): array {
        $result = $this->trick;
        return $result;
    }

    function complete(): bool {
        return count($this->players) == count($this->trick);
    }

    function play(Player $player, Card $card, callable $valid) {
        if ($this->complete()) {
            Log::error("Trying to playe a Trick that is complete");
            throw new Exception("The trick is complete, no more plays are allowed");
        }
        if ($player->id() !== $this->currentPlayer) {
            Log::error("The play was not made by the current player. ",
                ['current' => $this->currentPlayer, 'played' => $player->id()]);
            throw new Exception("The play was not made by the current player");
        }
        if (($valid)($this->trick(), $card, $player)) {
            $this->trick[] = new TrickCard($player->id(), $card);
            // Next player
            $next = next($this->players);
            if (!$next) {
                $next = reset($this->players);
            }
            $this->currentPlayer = $next->id();
        } else {
            Log::error("The played card is not valid");
            throw new \Exception("The played card is not valid for this trick");
        }
        
    }

    public function __toString() {
        $result = "";
        foreach($this->trick as $trick) {
            $result .= " ".$trick;
        }
        return $result;
    }

    private int $currentPlayer;
    private array $trick;

}