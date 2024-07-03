<?php

namespace App\Logic\AI\TrickTaking;

trait LegalActionTrait
{
    public function isActionLegal(int $actionId, WhistState $state): bool {
        //info("Is action legal: $actionId. ", ['hand' => $state->playerHand()]);
        return !empty($state->playerHand()[$actionId]);
    }
}