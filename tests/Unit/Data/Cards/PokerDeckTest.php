<?php
namespace Tests\Unit\Data\Cards;

use PHPUnit\Framework\TestCase;
use App\Data\Cards\FrenchSuit;
use App\Data\Cards\PokerDeck;

class PokerDeckTest extends TestCase {
        
    /**
     * @test
     */
    public function has_52_cards_after_reset(): void {
        $deck = new PokerDeck();
        $deck = $deck->reset();
        $this->assertEquals(52, $deck->left());
    }
    
    /**
     * @test
     */
    public function is_empty_before_reset(): void {
        $deck = new PokerDeck();
        $this->assertEquals(0, $deck->left());
    }
    
    /**
     * @dataProvider suits
     * @test
     */
    public function has_complete_suits_after_reset($suitName): void {
        $suit =  FrenchSuit::from($suitName);
        $deck = new PokerDeck();
        $deck = $deck->reset();
        $cards = [];
        while ($deck->left() > 0) {
            $card = $deck->drawOne();
            if ($card->suit() === $suit) {
                $cards[] = $card;
            }
        }
        $this->assertCount(13, $cards);
        $index = 2;
        // Suits are ordered
        foreach ($cards as $c) {
            $this->assertEquals($index, $c->rank());
            $index++;
        }
    }
    
    /**
     * @test
     */
    public function has_one_card_less_after_drawOne(): void {
        $deck = new PokerDeck();
        $deck = $deck->reset();
        $this->assertEquals(52, $deck->left());
        $deck->drawOne();
        $this->assertEquals(51, $deck->left());
    }
    
    /**
     * @dataProvider drawN
     * @test
     */
    public function has_n_cards_less_after_draw_n($n): void {
        $deck = new PokerDeck();
        $deck = $deck->reset();
        $this->assertEquals(52, $deck->left());
        $deck->draw($n);
        $this->assertEquals(52-$n, $deck->left());
    }
    
    public static function suits(): array {
        $result = [];
        $result["Hearts"] = ["H"];
        $result["Diamonds"] = ["D"];
        $result["Clubs"] = ["C"];
        $result["Spades"] = ["S"];
        return $result;
    }
    
    public static function drawN(): array {
        $result = [];
        $result["One"] = [1];
        $result["Five"] = [5];
        $result["Twenty"] = [20];
        $result["Fifty - Two"] = [52];
        return $result;
    }
}

