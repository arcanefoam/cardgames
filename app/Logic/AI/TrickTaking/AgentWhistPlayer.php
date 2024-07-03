<?php

namespace App\Logic\AI\TrickTaking;

use Psr\Log\LoggerInterface;
use Illuminate\Support\Facades\Log;

use App\Data\Cards\Card;
use App\Data\Cards\FrenchSuit;
use App\Logic\Games\TrickTaking\Hand;
use App\Logic\Games\TrickTaking\HandPlayer;
use App\Logic\Games\TrickTaking\PrintHand;
use RL\DQN\EGreedyAgent;
use RL\DQN\ModelProvider;
use RL\DQN\ExperienceReplayer;
use RL\Environment;
use RL\State as State;

class AgentWhistPlayer extends EGreedyAgent implements HandPlayer {

    use LegalActionTrait;

    use PrintHand;

    public function __construct(
        private int $id,
        ModelProvider $modelProvider,
        float $discountFactor,
        float $epsilon,
        ExperienceReplayer $replayer,
        int $updateTargetModelInterval,
        Environment $env,
        bool $useDoubleDQN = true,
        ?LoggerInterface $logger = null) {
        parent::__construct(
            $modelProvider,
            $discountFactor,
            $epsilon,
            $replayer,
            $updateTargetModelInterval,
            $env,
            $useDoubleDQN,
            $logger
        );
        $this->name = "Agent";
    }

    public function id(): int {
        return $this->id;
    }

    public function name(): string {
        return $this->name;
    }

    public function play(Hand $hand): Card {
        throw new \Exception("Unsupported operation AgentWhistPlayer::play");
    }

    public function hasOfSuit(FrenchSuit $suit): bool {
        $ofSuit = array_filter($this->hand, function($c) use ($suit) {
            return $c->suit() == $suit;
        });
        return count($ofSuit) > 0;
    }

    public function startHand(array $cards) {
        $cs = $this->bySuit($cards, FrenchSuit::Clubs);
        $ds = $this->bySuit($cards, FrenchSuit::Diamonds);
        $hs = $this->bySuit($cards, FrenchSuit::Hearts);
        $ss = $this->bySuit($cards, FrenchSuit::Spades);
        $this->hand = array_merge($ds, $ss, $hs, $cs);
        info("Player $this->name start hand ", ['cards' => $this->handToLog($this->hand)]);
    }

    public function take(Card $card): void {
        throw new \Exception("Unsupported operation AgentWhistPlayer::take");
    }

    public function hand(): array {
        return $this->hand;
    }

    /*
     * Since the AI picks any card, we need to check that the player actually has the card.
       The card is checked after the AI has picked a card, so if the agent has is, we asume
       the card has been played  */
    public function checkCard(Card $card): bool {
        $key = array_search($card, $this->hand);
        if($key !== false) {
            unset($this->hand[$key]);
            return true;
        }
        Log::error("Agent does not have card", ['hand' =>  $this->handToLog($this->hand), 'card' => $this->cardToLog($card)]);
        throw new AgentException("Agent player does not have the card");
    }

    /**
     * override to pick only legal moves, for faster training
     */
    protected function chooseRandomAction(State $state): int
    {
        $availableActions = [];
        foreach ($this->env->getActionSpace()->getActionIds() as $actionId) {
            if ($this->isActionLegal($actionId, $state)) {
                $availableActions[] = $actionId;
            }
        }
        return $availableActions[array_rand($availableActions)];
    }

    private array $hand;

    private function bySuit(array $cards, FrenchSuit $suit) {
        $result = array_filter($cards, fn($c) => $c->suit() == $suit);
        usort($result, fn($a, $b) => $a->rank() <=> $b->rank());
        return $result;
    }

}