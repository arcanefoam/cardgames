<?php

namespace App\Logic\AI\TrickTaking;

use App\Data\Game\Player;
use App\Data\Cards\FrenchSuit;
use App\Logic\Games\TrickTaking\WhistGame;
use RL\State as RLState;

class WhistState implements RLState {

    public function __construct(WhistGame $game) {
        // Need to place each card in its position in 52 slots
        $this->table = array_fill(1, 52, 0); 
        foreach ($game->currentHand()->currentTrick() as $card) {
            $base = match($card->card()->suit()) {
                FrenchSuit::Diamonds => 0,
                FrenchSuit::Spades => 12,
                FrenchSuit::Hearts => 25,
                FrenchSuit::Clubs => 38,
            };
            $this->table[$base+$card->card()->rank()];
        };
        $this->currentPlayer = $game->currentPlayer();
        $this->playerHand = array_fill(1, 52, 0); 
        foreach ($game->currentPlayer()->hand() as $card) {
            $base = match($card->suit()) {
                FrenchSuit::Diamonds => 0,
                FrenchSuit::Spades => 12,
                FrenchSuit::Hearts => 25,
                FrenchSuit::Clubs => 38,
            };
            $this->playerHand[$base+$card->rank()];
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
    private Player $currentPlayer;

    private function defineUid(WhistGame $game): string {
        $gUid = $game->uid();
        $player = $game->currentPlayer();
        $hand = "";
        foreach($player->hand() as $c) {
            $hand .= " ".$c;
        }
        return <<<EOT
table: $gUid
hand: $hand
EOT;
    }
}

