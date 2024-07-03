<?php

namespace App\Logic\Games\TrickTaking;

use \Exception;
use App\Data\Cards\Card;
use App\Data\Cards\Deck;
use App\Data\Cards\FrenchCard;
use App\Data\Game\Player;
use App\Data\Cards\CardPlayer;


class WhistHand implements Hand {

    public function __construct(
        private array $players,
        private int $leadId,
        private array $tricks = [],
        ) {
    }

    /**
     * We assume the deck is ready to use
     */
    public function start(Deck $deck): Hand {
        info("Start hand");
        foreach ($this->players as $player) {
            info("Give cards to ".$player->name());
            $cards = $deck->draw(13);
            $player->startHand($cards);
        }
        return new WhistHand(
            $this->players, 
            $this->leadId,
            [new SimpleTrick($this->players, $this->leadId)]);
    }

    public function nextTrick(): Hand {
        if ($this->complete()) {
            return $this;
        }
        if (end($this->tricks)->complete()) {
            info("Whist Hand", ['played' => count($this->tricks)]);
            $leadId = $this->next();
            return new WhistHand(
                $this->players,
                $leadId,
                array_merge($this->tricks, [new SimpleTrick($this->players, $leadId)]));
        }
        return $this;
    }

    /**
     * Return the cards played in the current trick
     */
    function currentTrick(): array {
        return end($this->tricks)->trick();
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

    public function play(Player $player, Card $card): void {
        $valid = function (array $cards, FrenchCard $c, CardPlayer $p) {
            if (count($cards) == 0) {
                return true;
            }
            $leadSuit = $cards[0]->card()->suit();
            if ($p->hasOfSuit($leadSuit)) {
                return $c->suit() == $leadSuit;
            }
            return true;
        };
        end($this->tricks)->play($player, $card, $valid);
    }   

    function trickComplete(): bool {
        return end($this->tricks)->complete();
    }

    function currentPlayer(): Player {
        return end($this->tricks)->currentPlayer();
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
            //info("Sorting ".$a->card()." vs ".$b->card());
            if ($a->card()->suit() == $trickSuit && $b->card()->suit() == $trickSuit) {
                //info("Same suit as trick suit");
                return $b->card()->rank() - $a->card()->rank();
            }
            else if ($b->card()->suit() === $trickSuit) {
                //info("");
                return 1;
            }
            else if ($a->card()->suit() === $trickSuit) {
                return -1;
            }
        });
        return $trick[0]->playerId();
    }
}
