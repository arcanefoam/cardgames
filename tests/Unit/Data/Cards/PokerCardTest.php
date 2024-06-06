<?php
namespace Tests\Unit\Data\Cards;

use PHPUnit\Framework\TestCase;
use App\Data\Cards\PokerCard;
use App\Data\Cards\FrenchSuit;
use App\Data\Cards\CardException;

class PokerCardTest extends TestCase {
    
    /**
     * @test
     */
    public function has_correct_suit(): void {
        $card = new PokerCard(FrenchSuit::Clubs, 3);
        $this->assertEquals(FrenchSuit::Clubs, $card->suit());
    }
    
    /**
     * @test
     */
    public function has_correct_rank(): void {
        $card = new PokerCard(FrenchSuit::Clubs, 2);
        $this->assertEquals(2, $card->rank());
    }
    
    /**
     * @dataProvider negativeRanks
     * @test
     */
    public function throws_if_rank_lt_one($rank): void {
        $this->expectException(CardException::class);
        new PokerCard(FrenchSuit::Clubs, $rank);
    }
    
    /**
     * @dataProvider higherRanks
     * @test
     */
    public function throws_if_rank_gt_fourteen($rank): void {
        $this->expectException(CardException::class);
        new PokerCard(FrenchSuit::Clubs, $rank);
    }
    
    public static function negativeRanks(): array {
        $random_number_array = range(1, -8, -1);
        shuffle($random_number_array );
        $ids = array_map(function($value) {return "rank= ".strval($value);}, $random_number_array);
        return array_combine($ids, array_chunk($random_number_array, 1));
    }
    
    public static function higherRanks(): array {
        $random_number_array = range(15, 100, 1);
        shuffle($random_number_array );
        $sample = array_slice($random_number_array, 0, 10);
        $ids = array_map(function($value) {return "rank= ".strval($value);}, $sample);
        return array_combine($ids, array_chunk($sample, 1));
    }
}
