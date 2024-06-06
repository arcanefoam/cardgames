<?php

namespace App\Logic\Games\TrickTaking;

use App\Data\Cards\Card;
use App\Data\Cards\CardPlayer;
use App\Data\Cards\FrenchSuit;
use Exception;

class WhistPlayer implements CardPlayer {

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

    public function play(): Card {
        if (!$this->hand) {
            throw new Exception("No cards left in the player's hand");
        }
        $card = $this->hand[0];
        unset($this->hand[0]);
        $this->hand = array_values($this->hand);
        return $card;
    }

    public function take(Card $card): void {
        $this->hand[] = $card;
    }

    private array $hand;

}