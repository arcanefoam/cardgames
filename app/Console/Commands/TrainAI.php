<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use App\Logic\Games\TrickTaking\WhistPlayer;
use App\Logic\Games\TrickTaking\CliWhistPlayer;
use App\Logic\AI\TrickTaking\AgentWhistPlayer;
use App\Logic\AI\TrickTaking\TrainingEnvironment;
use App\Logic\AI\TrickTaking\WhistModelProvider;
use App\Logic\AI\Logger;

use RL\DQN\RandomBatchExperienceReplayer;
use RL\Agent;

class TrainAI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:trainai {epochs=100} {games=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Train the AI';

    /**
     * Execute the console command.
     */
    public function handle() {
        // Clear the log
        exec('rm -f ' . storage_path('logs/*.log'));;
        define('GAMES_PER_EPOCH', (int) $this->argument('games'));
        define('EPSILON_DECAY', 0.9995);
        define('EPSILON_MIN', 0.001);
        $env = new TrainingEnvironment();
        $agent = new AgentWhistPlayer(
            2,
            new WhistModelProvider($env),
            0.99,
            1.0,
            new RandomBatchExperienceReplayer(32, 50000),
            5000,
            $env,
            true,
            new Logger()
        );

        $players = [
            1 => new WhistPlayer(1, "John"),
            2 => $agent,
            3 => new WhistPlayer(3, "Mark"),
            4 => new WhistPlayer(4, "Ana"),
        ];
        $env->players($players[2], $players);
        $epochs = (int) $this->argument('epochs');
        $games = GAMES_PER_EPOCH;
        info("training against a random opponent for $epochs epoch(s) and $games games");
        $this->train($agent, $env, $epochs);
    }

    private function train(
        Agent $agent,
        TrainingEnvironment $env,
        int $epochs) {
        $episode = 0;
        while ($episode < $epochs) {
            echo "Episode $episode\n";
            $episode++;
            $games = GAMES_PER_EPOCH;
            $w = 0;
            while ($games--) {
                echo "Game $games\n";
                info("=== New Game");
                $env->reset();
                while (!$env->isDone()) {
                    $agent->act($env);
                }
                if ($env->gameComplete()) {
                    $w++;
                }
                info("=== End Game");
            }
            file_put_contents(__DIR__ . '/dqn.model', serialize($agent));
            $agent->setEpsilon($agent->getEpsilon() * EPSILON_DECAY);
            
            info("episode #$episode".
            " success=".(($w) / GAMES_PER_EPOCH * 100) . "%".
            " epsilon=".$agent->getEpsilon());
        }
    }
}
