<?php

namespace App\Logic\AI\TrickTaking;

use RL\DQN\Model as DQNModel;
use RL\Environment;

use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\NeuralNet\ActivationFunctions\LeakyReLU;
use Rubix\ML\NeuralNet\ActivationFunctions\ReLU;
use Rubix\ML\NeuralNet\CostFunctions\HuberLoss;
use Rubix\ML\NeuralNet\CostFunctions\LeastSquares;
use Rubix\ML\NeuralNet\FeedForward;
use Rubix\ML\NeuralNet\Initializers\He;
use Rubix\ML\NeuralNet\Initializers\Xavier2;
use Rubix\ML\NeuralNet\Layers\Activation;
use Rubix\ML\NeuralNet\Layers\Continuous;
use Rubix\ML\NeuralNet\Layers\Dense;
use Rubix\ML\NeuralNet\Layers\Placeholder1D;
use Rubix\ML\NeuralNet\Optimizers\Adam;
use Rubix\ML\NeuralNet\Optimizers\RMSProp;

class WhistModel implements DQNModel {

    use LegalActionTrait;

    const INPUT_SIZE = 104;

    private FeedForward $nn;
    private Environment $env;

    public function __construct(Environment $env, ?FeedForward $nn = null)
    {
        $this->env = $env;
        if ($nn == null) {
            $this->nn = new FeedForward(
                new Placeholder1D(self::INPUT_SIZE),
                [
                    new Dense(50, 0.0, true, new He()),
                    new Activation(new ReLU()),
                    new Dense(50, 0.0, true, new He()),
                    new Activation(new ReLU()),
                    new Dense(1, 0.0, true, new Xavier2()),
                ],
                new Continuous(
                    new HuberLoss()
                ),
                new RMSProp(0.001)
            );
            $this->nn->initialize();
        } else {
            $this->nn = $nn;
        }
        
    }

    /**
     * Computes a prediction according to the given State
     * @return array a Q array indexed by action ids
     */
    public function predict(\RL\State $state): array
    {
        $q = [];
        foreach ($this->env->getActionSpace()->getActionIds() as $actionId) {
            // a little hack here : we do not want to consider illegal actions at all
            // for performance reasons and noise, mainly
            if (!$this->isActionLegal($actionId, $state)) {
                $q[$actionId] = -100;
            } else {
                $features = $this->stateToFeatures($state, $actionId, null);
                $output = $this->nn->infer(Unlabeled::quick([$features]))->columnAsVector(0);
                $q[$actionId] = $output[0];
            }
        }

        return $q;
    }

    public function predictOne(\RL\State $state, int $actionId): float
    {
        if (!$this->isActionLegal($actionId, $state)) {
            return -100;
        }
        $features = $this->stateToFeatures($state, $actionId, null);
        $output = $this->nn->infer(Unlabeled::quick([$features]))->columnAsVector(0);
        return $output[0];
    }

    /**
     * Updates the model Q prediction for the given action
     * @return float the loss
     */
    public function fit(\RL\State $state, int $actionId, float $reward): float
    {
        return $this->fitBatch([$state], [$actionId], [$reward]);
    }

    public function fitBatch(array $states, array $actionIds, array $rewards): float
    {
        $samples = [];
        $targets = [];
        foreach ($states as $k => $state) {
            $actionId = $actionIds[$k];
            $targets[] = $rewards[$k];
            $samples[] = $this->stateToFeatures($state, $actionId);
        }

        $loss = $this->nn->roundtrip(Labeled::quick($samples, $targets));
        return $loss;
    }

    private function stateToFeatures(WhistState $state, int $actionId): array
    {
        $actions = array_fill(2, 52, 0.0);
        $actions[$actionId] = 1.0;
        $features = array_merge($state->table(), $actions);
        return $features;
    }

    public function getNN(): FeedForward
    {
        return $this->nn;
    }

    public function setNN(FeedForward $nn): void
    {
        $this->nn = $nn;
    }

    public function getEnv(): Environment
    {
        return $this->env;
    }

}