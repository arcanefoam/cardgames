<?php

namespace App\Data\Game;

interface Player {

    function name(): string;

    function id(): int;
}