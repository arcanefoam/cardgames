<?php

namespace App\Logic\AI\TrickTaking;

use App\Data\Cards\FrenchSuit;
use App\Logic\Games\TrickTaking\HandPlayer;
use App\Logic\Games\TrickTaking\WhistGame;
use RL\State as RLState;

class WhistState implements RLState {

    public function __construct(WhistGame $game, private HandPlayer $agent) {
        // Need to place each card in its position in 52 slots
        $this->table = array_fill(2, 52, 0); 
        foreach ($game->currentHand()->currentTrick() as $card) {
            $base = match($card->card()->suit()) {
                FrenchSuit::Clubs => 0,
                FrenchSuit::Diamonds => 13,
                FrenchSuit::Hearts => 26,
                FrenchSuit::Spades => 39,
            };
            $this->table[$base+$card->card()->rank()] = 1;
        };
        $this->playerHand = array_fill(2, 52, 0); 
        foreach ($this->agent->hand() as $card) {
            $base = match($card->suit()) {
                FrenchSuit::Clubs => 0,
                FrenchSuit::Diamonds => 13,
                FrenchSuit::Hearts => 26,
                FrenchSuit::Spades => 39,
            };
            $this->playerHand[$base+$card->rank()] = 1;
        };
        $this->uid = $this->defineUid($game);
    }

    public function uid(): string {
        return $this->uid;
    }

    public function table(): array {
        return $this->table;
    }

    public function playerHand(): array {
        return $this->playerHand;
    }

    private string $uid;
    private array $table;

    private function defineUid(WhistGame $game): string {
        $gUid = $game->uid();
        $hand = "";
        foreach($this->agent->hand() as $c) {
            $hand .= " ".$c;
        }
        return <<<EOT
table: $gUid
hand: $hand
EOT;
    }
}

