<?php
namespace App\Data\Cards;

interface FrenchCard extends Card {
    
    function suit(): FrenchSuit;
    function rank(): int;
}

