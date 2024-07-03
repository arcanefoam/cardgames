<?php

namespace App\Logic\AI\TrickTaking;

use App\Data\Game\Player;
use App\Logic\Games\TrickTaking\WhistGame;
use App\Logic\AI\PokerActionSpace;
use RL\ActionSpace;
use RL\Environment;

class TrainingEnvironment implements Environment {

    public function __construct() {
        $this->gameOver = false;
    }

    public function players(
        Player $agentPlayer,
        array $players) {
        $this->players = $players;
        $this->agentPlayer = $agentPlayer;
    }

    public function reset(): void {
        $this->game = new WhistGame($this->players);
        $this->actionSpace = new PokerActionSpace();
        $this->gameOver = false;
    }


    public function getActionSpace(): PokerActionSpace {
        return $this->actionSpace;
    }

    public function getState(): WhistState {
        return new WhistState($this->game, $this->agentPlayer);
    }

    public function act(int $actionId): float {
        for($i=0; $i<4; $i++) {
            $currentPlayer = $this->game->currentPlayer();
            info("TE Next turn", ['player' => $currentPlayer->name()]);
            if ($currentPlayer === $this->agentPlayer) {
                try {
                    $card = $this->actionSpace->getAction($actionId);
                    $this->agentPlayer->checkCard($card);
                    $this->game->play($currentPlayer, $card);
                }
                catch (AgentException $e) {
                    $this->gameOver = true;
                    // A TrickException means that the player didn't have the card, but it can be the correct suit
                    $hand = $this->game->currentHand();
                    $trick = $hand->currentTrick();
                    if (count($trick) == 0) {
                        info("Agent played first, but didn't have the card.");
                        return -1.0;
                    }
                    $leadSuit = $trick[0]->card()->suit();
                    if ($card->suit() == $leadSuit) {
                        info("Agent played, didn't have the card but played the correct suit");
                        return 1.0;
                    }
                    info("Agent play was invalid.", ['error' => $e->getMessage()]);
                    return -1.0;
                }
                catch (\Exception $e) {
                    // an exception means an invalid action : negative reward
                    info("Agent play was invalid.", ['error' => $e->getMessage()]);
                    $this->gameOver = true;
                    return -1.0;
                }
            } else {
                $this->game->play($currentPlayer, $currentPlayer->play($this->game->currentHand()));
            }
        } 
        $this->gameOver = $this->game->done();
        info("Game: ", ["over" => $this->gameOver]);
        return 2.0;
    }

    public function isDone(): bool {
        return $this->gameOver;
    }

    public function gameComplete(): bool {
        return $this->game->done();
    }

    private WhistGame $game;
    private PokerActionSpace $actionSpace;
    private bool $gameOver;
    private array $players;
    private Player $player;

}