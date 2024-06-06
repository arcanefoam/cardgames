<?php

namespace App\Logic\Games\TrickTaking;

use App\Logic\Games\Player;

class WhistHand implements Hand {

    public function currentTrick(): Trick {
        return $this->currentTrick;
    }

    public function initialLead(): Player {
        return $this->players[$this->initialLeadId];
    }

    public function complete(): bool {
        return count($this->tricks) == 13;
    }

    public function scores(): array {
        return [];
    }

    /**
     * The first card in the trick is the lead suit.
     */
    public function nextLead(array $trick): Player {
        $trickSuit = $trick[0]->card->suit();
        usort($trick, function($a, $b) use ($trickSuit){
            if ($a->card->suit() == $trickSuit && $b->card->suit() == $trickSuit) {
                return $b->rank - $a->rank;
            }
            else if ($b->card->suit() === $trickSuit) {
                return 1;
            }
            else if ($a->card->suit() === $trickSuit) {
                return -1;
            }
        });
        return $trick[0]->player;
    }

    private Trick $currentTrick;
    private int $initialLeadId;
    private array $players;
    private array $tricks;
    
}