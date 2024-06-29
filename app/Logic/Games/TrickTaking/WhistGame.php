<?php

namespace App\Logic\Games\TrickTaking;

use App\Data\Cards\Card;
use App\Data\Cards\CardPlayer;
use App\Data\Cards\PokerDeck;
use App\Data\Game\Player;

class WhistGame {

    use PrintHand;

    public function __construct(private array $players) {
        $this->hand = new WhistHand($this->players, array_rand($this->players));
        $this->currentPlayer = $this->hand->nextLead();
        while (key($this->players) !== $this->currentPlayer->id()) next($this->players); // Advance until there's a match
        $deck = new PokerDeck();
        $deck = $deck->reset()->shuffle();
        $this->hand = $this->hand->start($deck);
    }

    public function play(Player $player, Card $card): void {
        info("Player ".$player->name()." played ".$this->cardToLog($card));
        if (!$this->hand->complete()
            && $this->currentPlayer === $player) {
            $this->hand->play($player, $card);
            if (!$this->hand->trickComplete()) {
                $this->currentPlayer = $this->hand->currentPlayer();
            } else {
                $this->hand = $this->hand->nextTrick();
                $this->currentPlayer = $this->hand->nextLead();
            }
        } else {
            throw new \Exception("Hand is complete, no more plays");
        }
        info("Next player ".$this->currentPlayer->name());
    }

    public function currentPlayer(): HandPlayer {
        return $this->currentPlayer;
    }

    public function currentHand() : Hand {
        return $this->hand;
    }

    public function done() : bool {
        return $this->hand->complete();
    }

    public function uid() {
        $playedCards = array_map(fn($t) => $t->card(), $this->hand->currentTrick());
        $prompt = "";
        foreach($playedCards as $c) {
            $prompt .= " ".$c;
        }
        return $prompt;
    }


    private Hand $hand;
    private CardPlayer $currentPlayer;


}