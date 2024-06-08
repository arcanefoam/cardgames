<?php

namespace Tests\Unit\Logic\Games\TrickTaking;

use \Exception;
use PHPUnit\Framework\TestCase;
use App\Data\Cards\FrenchCard;
use App\Data\Cards\PokerDeck;
use App\Data\Game\Player;
use App\Logic\Games\TrickTaking\Trick;
use App\Logic\Games\TrickTaking\WhistHand;
use App\Logic\Games\TrickTaking\WhistPlayer;


class WhistHandTest extends TestCase {

    /**
     * @test
     */
    public function next_trick_returns_a_new_trick() {
        $players = [
            1 => new WhistPlayer(1, "John"),
            2 => new WhistPlayer(2, "Jane"),
            3 => new WhistPlayer(3, "Mark"),
            4 => new WhistPlayer(4, "Laura"),
        ];
        $hand = new WhistHand($players, 1);
        $deck = new PokerDeck();
        $deck = $deck->reset()->shuffle();
        $hand = $hand->start($deck);
        $trick = $hand->nextTrick();
        $this->assertNotNull($trick);
    }

    /**
     * @test
     */
    public function next_trick_returns_same_trick_if_not_complete() {
        $players = [
            1 => new WhistPlayer(1, "John"),
            2 => new WhistPlayer(2, "Jane"),
            3 => new WhistPlayer(3, "Mark"),
            4 => new WhistPlayer(4, "Laura"),
        ];
        $hand = new WhistHand($players, 1);
        $deck = new PokerDeck();
        $deck = $deck->reset()->shuffle();
        $hand = $hand->start($deck);
        $trick1 = $hand->nextTrick();
        $trick2 = $hand->nextTrick();
        $this->assertEquals($trick1, $trick2);
    }

    /**
     * @test
     */
    public function next_trick_returns_new_trick_if_complete() {
        $players = [
            1 => new WhistPlayer(1, "John"),
            2 => new WhistPlayer(2, "Jane"),
            3 => new WhistPlayer(3, "Mark"),
            4 => new WhistPlayer(4, "Laura"),
        ];
        $hand = new WhistHand($players, 1);
        $deck = new PokerDeck();
        $deck = $deck->reset()->shuffle();
        $hand = $hand->start($deck);
        $trick1 = $hand->nextTrick();
        // Play the hand
        $trick1->play($players[1], $players[1]->play(), fn($a, $b) => true);
        $trick1->play($players[2], $players[2]->play(), fn($a, $b) => true);
        $trick1->play($players[3], $players[3]->play(), fn($a, $b) => true);
        $trick1->play($players[4], $players[4]->play(), fn($a, $b) => true);
        $trick2 = $hand->nextTrick();
        $this->assertNotEquals($trick1, $trick2);
    }

    /**
     * @test
     */
    public function next_player_is_higher_rank() {
        $players = [
            1 => new WhistPlayer(1, "John"),
            2 => new WhistPlayer(2, "Jane"),
            3 => new WhistPlayer(3, "Mark"),
            4 => new WhistPlayer(4, "Laura"),
        ];
        $hand = new WhistHand($players, 1);
        $deck = new PokerDeck();
        $deck = $deck->reset()->shuffle();
        $hand = $hand->start($deck);
        $hand = $hand->nextTrick();
        // Play the hand
        $valid = function (array $cards, FrenchCard $c, Player $p) {
            if (count($cards) == 0) {
                return true;
            }
            $leadSuit = $cards[0]->card()->suit();
            if ($p->hasOfSuit($leadSuit)) {
                return $c->suit() == $leadSuit;
            }
            return true;
        };
        $tcard = $players[1]->play();
        $next = $players[1];
        $hand->play($players[1], $tcard, $valid);
        $error = true;
        do {
            try {
                $card = $players[2]->play();
                $hand->play($players[2], $card, $valid);   
                $error = false;     
            }  catch (Exception $e) {
                $players[2]->take($card);
             }
        } while($error);
        if ($card->rank() > $tcard->rank()) {
            $tcard = $card;
            $next = $players[2];
        }
        $error = true;
        do {
            try {
                $card = $players[3]->play();
                $hand->play($players[3], $card, $valid);
                $error = false;     
            }  catch (Exception $e) { 
                $players[3]->take($card);
            }
        } while($error);
        if ($card->rank() > $tcard->rank()) {
            $tcard = $card;
            $next = $players[3];
        }
        $error = true;
        do {
            try {
                $card = $players[4]->play();
                $hand->play($players[4], $card, $valid);
                $error = false;     
            }  catch (Exception $e) { 
                $players[4]->take($card);
            }
        } while($error);
        if ($card->rank() > $tcard->rank()) {
            $tcard = $card;
            $next = $players[4];
        }
        $hand = $hand->nextTrick();
        $this->assertEquals($next->id(), $hand->nextLead()->id());
    }

}