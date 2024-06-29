<?php

namespace App\Logic\Games\TrickTaking;

use App\Data\Cards\PokerCard;

trait PrintHand {

    public function handToBash(array $hand) {
        $result = "";
        foreach($hand as $c) {
            $result .= " ".$c;
        }
        return $result;
    }

    public function handToLog(array $hand) {
        $result = "";
        foreach($hand as $c) {
            $result .= " ".$this->cardToLog($c);
        }
        return $result;
    }

    public function cardToLog(PokerCard $card) {
        $rep = match($card->rank()) {
            2,3,4,5,6,7,8,9,10 => $card->rank(),
            11 => "J",
            12 => "Q",
            13 => "K",
            14 => "A",
        };
        return str_pad($rep, 2, " ", STR_PAD_LEFT).$card->suit()->unicode();
    }


}