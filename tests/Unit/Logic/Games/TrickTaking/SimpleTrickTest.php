<?php

namespace Tests\Unit\Logic\Games\TrickTaking;

use \Exception;
use PHPUnit\Framework\TestCase;

use App\Logic\Games\TrickTaking\SimpleTrick;
use App\Logic\Games\TrickTaking\WhistPlayer;
use App\Data\Cards\FrenchSuit;
use App\Data\Cards\PokerCard;

class SimpleTrickTest extends TestCase {

    /**
     * @test
     */
    public function after_init_current_player_is_lead(): void {
        $players = [
            1 => new WhistPlayer(1, "John"),
            2 => new WhistPlayer(2, "Jane"),
            3 => new WhistPlayer(3, "Mark"),
            4 => new WhistPlayer(4, "Laura"),
        ];
        $trick = new SimpleTrick($players, 1);
        $this->assertEquals($players[1], $trick->currentPlayer());
    }

    /**
     * @test
     */
    public function not_complete_if_no_plays(): void {
        $players = [
            1 => new WhistPlayer(1, "John"),
            2 => new WhistPlayer(2, "Jane"),
            3 => new WhistPlayer(3, "Mark"),
            4 => new WhistPlayer(4, "Laura"),
        ];
        $trick = new SimpleTrick($players, 1);
        $this->assertFalse($trick->complete());
    }

    /**
     * @test
     */
    public function empty_if_no_plays(): void {
        $players = [
            1 => new WhistPlayer(1, "John"),
            2 => new WhistPlayer(2, "Jane"),
            3 => new WhistPlayer(3, "Mark"),
            4 => new WhistPlayer(4, "Laura"),
        ];
        $trick = new SimpleTrick($players, 1);
        $this->assertCount(0, $trick->trick());
    }

     /**
     * @test
     */
    public function is_complete_after_all_players_played() {
        $players = [
            1 => new WhistPlayer(1, "John"),
            2 => new WhistPlayer(2, "Jane"),
            3 => new WhistPlayer(3, "Mark"),
            4 => new WhistPlayer(4, "Laura"),
        ];
        $trick = new SimpleTrick($players, 1);
        $trick->play($players[1], new PokerCard(FrenchSuit::Clubs, 2), fn($a, $b) => true);
        $trick->play($players[2], new PokerCard(FrenchSuit::Clubs, 3), fn($a, $b) => true);
        $trick->play($players[3], new PokerCard(FrenchSuit::Clubs, 4), fn($a, $b) => true);
        $trick->play($players[4], new PokerCard(FrenchSuit::Clubs, 5), fn($a, $b) => true);
        $this->assertTrue($trick->complete());
    }

    /**
     * @test
     */
    public function play_throws_if_player_is_not_current() {
        $players = [
            1 => new WhistPlayer(1, "John"),
            2 => new WhistPlayer(2, "Jane"),
            3 => new WhistPlayer(3, "Mark"),
            4 => new WhistPlayer(4, "Laura"),
        ];
        $trick = new SimpleTrick($players, 1);
        $this->expectException(Exception::class);
        $trick->play($players[2], new PokerCard(FrenchSuit::Clubs, 3), fn($a, $b) => $a);
    }

     /**
     * @test
     */
    public function play_throws_if_trick_complete() {
        $players = [
            1 => new WhistPlayer(1, "John"),
            2 => new WhistPlayer(2, "Jane"),
            3 => new WhistPlayer(3, "Mark"),
            4 => new WhistPlayer(4, "Laura"),
        ];
        $trick = new SimpleTrick($players, 1);
        $trick->play($players[1], new PokerCard(FrenchSuit::Clubs, 2), fn($a, $b) => true);
        $trick->play($players[2], new PokerCard(FrenchSuit::Clubs, 3), fn($a, $b) => true);
        $trick->play($players[3], new PokerCard(FrenchSuit::Clubs, 4), fn($a, $b) => true);
        $trick->play($players[4], new PokerCard(FrenchSuit::Clubs, 5), fn($a, $b) => true);
        $this->expectException(Exception::class);
        $trick->play($players[2], new PokerCard(FrenchSuit::Clubs, 6), fn($a, $b) => true);
    }

    /**
     * @test
     */
    public function trick_returns_cards_played() {
        $players = [
            1 => new WhistPlayer(1, "John"),
            2 => new WhistPlayer(2, "Jane"),
            3 => new WhistPlayer(3, "Mark"),
            4 => new WhistPlayer(4, "Laura"),
        ];
        $trick = new SimpleTrick($players, 1);
        $trick->play($players[1], new PokerCard(FrenchSuit::Clubs, 2), fn($a, $b) => true);
        $played = $trick->trick();
        $this->assertNotNull($played);
        $this->assertCount(1, $played);
        $this->assertEquals($played[0]->card()->suit(), FrenchSuit::Clubs);
        $this->assertEquals($played[0]->card()->rank(), 2);
        $trick->play($players[2], new PokerCard(FrenchSuit::Diamonds, 3), fn($a, $b) => true);
        $played = $trick->trick();
        $this->assertCount(2, $played);
        $this->assertEquals($played[1]->card()->suit(), FrenchSuit::Diamonds);
        $this->assertEquals($played[1]->card()->rank(), 3);
    }
}