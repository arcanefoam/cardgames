<?php

namespace App\Logic\Games\TrickTaking;

use App\Data\Cards\Card;
use App\Data\Cards\FrenchSuit;
use Exception;

class WhistPlayer implements HandPlayer {

    use PrintHand;

    public function __construct(private int $id, private string $name) {

    }

    public function id(): int {
        return $this->id;
    }

    public function name(): string {
        return $this->name;
    }

    public function hasOfSuit(FrenchSuit $suit): bool {
        $ofSuit = array_filter($this->hand, function($c) use ($suit) {
            return $c->suit() == $suit;
        });
        return count($ofSuit) > 0;
    }

    public function startHand(array $cards) {
        $this->hand = $cards;
        info("Player $this->name start hand ", ['cards' => $this->handToLog($this->hand)]);
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
            if ($ofSuit) {
                $card = $ofSuit[array_rand($ofSuit)];
            } else {
                // Throw the lowest rank card
                $card = $this->lowest();
            }
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

    public function hand(): array {
        return $this->hand;
    }

    private array $hand;

    private function lowest(): Card {
        $min = null;
        foreach ($this->hand as $card) {
            if ($card->rank() < $min?->rank() ?? 14) {
                $min = $card;
            }
        }
        return $card;
    }
}