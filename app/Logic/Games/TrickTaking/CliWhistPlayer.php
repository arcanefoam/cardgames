<?php

namespace App\Logic\Games\TrickTaking;

use App\Data\Cards\Card;
use App\Data\Cards\FrenchSuit;
use App\Logic\Games\Cli;
use Exception;

class CliWhistPlayer implements HandPlayer {

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
        return true;
    }

    public function startHand(array $cards) {
        $cs = $this->bySuit($cards, FrenchSuit::Clubs);
        $ds = $this->bySuit($cards, FrenchSuit::Diamonds);
        $hs = $this->bySuit($cards, FrenchSuit::Hearts);
        $ss = $this->bySuit($cards, FrenchSuit::Spades);
        $this->hand = array_merge($ds, $ss, $hs, $cs);
        info("Player $this->name start hand ", ['cards' => $this->handToLog($this->hand)]);
    }

    public function play(Hand $hand): Card {
        if (!$this->hand) {
            throw new Exception("No cards left in the player's hand");
        }
        $playedCards = array_map(fn($t) => $t->card(), $hand->currentTrick());
        $prompt = "Played:\n";
        /*foreach($playedCards as $c) {
            $prompt .= " ".$c;
        }*/
        $prompt .= $this->handToBash($playedCards);
        $prompt .= "\n\nSelect card to play (type index):\n";
        /*foreach($this->hand as $c) {
            $prompt .= " ".$c;
        }*/
        $prompt .= $this->handToBash($this->hand);
        $prompt .= "\n";
        foreach(range(1, count($this->hand)) as $i) {
            $prompt .= str_pad($i, 4, " ", STR_PAD_LEFT)." ";
        }
        $prompt .= "\n";
        $cli = new Cli();
        $pos = intval($cli->input($prompt))-1;
        $card = $this->hand[$pos];
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

    private function bySuit(array $cards, FrenchSuit $suit) {
        $result = array_filter($cards, fn($c) => $c->suit() == $suit);
        usort($result, fn($a, $b) => $a->rank() <=> $b->rank());
        return $result;
    }

}