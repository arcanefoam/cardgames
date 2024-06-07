<?php

namespace App\Logic\Games\TrickTaking;

use \Exception;
use App\Data\Cards\Card;
use App\Data\Cards\PokerDeck;
use App\Data\Game\Player;


class WhistHand implements Hand {

    public function __construct(
        private array $players,
        private int $leadId,
        private array $tricks = [],
        ) {
    }

    public function nextTrick(): Hand {
        if ($this->complete()) {
            return $this;
        }
        if (!$this->tricks) {
            // Deal cards - This could be done in a "init" method
            $deck = new PokerDeck();
            $deck = $deck->reset()->shuffle();
            foreach ($this->players as $player) {
                $cards = $deck->draw(13);
                $player->startHand($cards);
            }
            return new WhistHand($this->players, $this->leadId, [new SimpleTrick($this->players, $this->leadId)]);
        } else {
            if (end($this->tricks)->complete()) {
                $leadId = $this->next();
                return new WhistHand(
                    $this->players,
                    $leadId,
                    array_merge($this->tricks, [new SimpleTrick($this->players, $leadId)]));
            }
        }
        return $this;
    }

    public function nextLead(): Player {
        return $this->players[$this->leadId];
    }

    public function complete(): bool {
        return count($this->tricks) == 13;
    }

    public function scores(): array {
        return [];
    }

    public function play(Player $player, Card $card, callable $valid): void {
        end($this->tricks)->play($player, $card, $valid);
    }   
    
    /**
     * The first card in the trick is the lead suit.
     * We sort the trick based on the lead suit and return the player with the higher rank card
     */
    private function next(): int {
        if (!end($this->tricks)->complete()) {
            throw new Exception("Current trick is not complete");
        }
        $trick = current($this->tricks)->trick();
        $trickSuit = $trick[0]->card()->suit();
        usort($trick, function($a, $b) use ($trickSuit) {
            if ($a->card()->suit() == $trickSuit && $b->card()->suit() == $trickSuit) {
                return $b->card()->rank() - $a->card()->rank();
            }
            else if ($b->card()->suit() === $trickSuit) {
                return -1;
            }
            else if ($a->card()->suit() === $trickSuit) {
                return 1;
            }
        });
        return $trick[0]->playerId();
    }
}
