<?php

namespace App\Logic\AI;

use App\Data\Cards\PokerCard;
use App\Data\Cards\PokerDeck;
use RL\ActionSpace;

/**
 * A Poker ActionSpace allows to play any of the 52 cards of the Pocker deck
 */
class PokerActionSpace extends ActionSpace {

    public function __construct() {
        $deck = new PokerDeck();
        $deck = $deck->reset();
        $pos = 2;
        while ($deck->left() > 0) {
            $card = $deck->drawOne();
            $this->addAction($pos++, $card);
        }
        //info("PokerActionSpace init", ['space' => $this->actions]);
    }

    public function addAction(int $id, Object $card): void
    {
        $this->actions[$id] = $card;
    }

    public function removeAction(int $id): void
    {
        unset($this->actions[$id]);
    }

    public function getAction(int $id): Object
    {
        return $this->actions[$id];
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function getActionIds(): array
    {
        return array_keys($this->actions);
    }

    public function count(): int
    {
        return count($this->actions);
    }

    public function findByCard(PokerCard $card): int
    {
        return array_search($card, $this->actions);
    }

    private array $actions = [];
}