<?php

namespace App\Logic\Games\TrickTaking;

use App\Data\Cards\Card;
use App\Data\Cards\FrenchSuit;
use Exception;

class WhistPlayer implements HandPlayer {

    public function __construct(private int $id, private string $name) {

    }

    public function id(): int {
        return $this->id;
    }

    public function name(): string {
        return $this->name;
    }

    public function hasOfSuit(FrenchSuit $suit): bool {
        return true;
    }

    public function startHand(array $cards) {
        $this->hand = $cards;
    }

    public function play(Hand $hand): Card {
        if (!$this->hand) {
            throw new Exception("No cards left in the player's hand");
        }
        $playedCards = array_map(fn($t) => $t->card(), $hand->currentTrick());
        if ($playedCards) {
            $leadSuit = $playedCards[0]->suit();
            $ofSuit = array_filter($this->hand, function($c) use ($leadSuit) {
                return $c->suit() == $leadSuit;
            });
            $card = $ofSuit[array_rand($ofSuit)];
            $pos = array_search($card, $this->hand);
        } else {
            $card = $this->hand[0];
            $pos = 0;
        }
        unset($this->hand[$pos]);
        $this->hand = array_values($this->hand);
        return $card;
    }

    public function take(Card $card): void {
        $this->hand[] = $card;
    }

    private array $hand;
}