<?php
namespace App\Data\Cards;


/**
 * A standard 52-card French-suited deck comprises 13 ranks in each of the four suits: clubs (♣), 
 * diamonds (♦), hearts (♥) and spades (♠). Each suit includes three court cards (face cards), King,
 * Queen and Jack, with reversible (i.e. double headed) images. Each suit also includes ten numeral
 * cards or pip cards, from one (Ace) to ten. The card with one pip is known as an Ace. Each pip
 * card displays the number of pips (symbols of the suit) corresponding to its number, as well as
 * the appropriate numeral (except "A" for the Ace) in at least two corners.
 * 
 * @author hoyos
 *
 */
class PokerDeck implements Deck {
    
    public function __construct(private array $cards = []) {
        
    }

    public function reset(): Deck {
        $cards = array();
        foreach (FrenchSuit::cases() as $suit) {
            foreach (range(1, 13) as $rank ) {
                $cards[] = new PokerCard($suit, $rank);
            }
        }
        return new PokerDeck($cards);
    }

    public function drawOne(): Card {
        $card = $this->cards[0];
        unset($this->cards[0]);
        $this->cards = array_values($this->cards);
        return $card;
    }
    
    public function draw(int $count): array {
        return array_splice($this->cards, 0, $count);
    }

    public function place(Card $card): Deck {
        
    }

    public function shuffle(): Deck {
        
    }
    
    public  function left(): int {
        return count($this->cards);
    }

}

