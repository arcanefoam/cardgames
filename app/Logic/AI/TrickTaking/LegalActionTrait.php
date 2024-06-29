<?php

namespace App\Logic\AI\TrickTaking;

trait LegalActionTrait
{
    public function isActionLegal(int $actionId, WhistState $state): bool {
        return empty($state->playerHand()[$actionId]);
    }
}