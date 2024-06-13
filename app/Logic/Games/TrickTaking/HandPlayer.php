<?php

namespace App\Logic\Games\TrickTaking;


use App\Data\Cards\Card;
use App\Data\Cards\CardPlayer;

interface HandPlayer extends CardPlayer {

    function play(Hand $hand): Card;

}