<?php

namespace App\Logic\AI\TrickTaking;

use RL\DQN\Model;
use RL\DQN\ModelProvider;
use RL\Environment;
use Rubix\ML\Persisters\Filesystem;

class WhistModelProvider implements ModelProvider
{
    public function createModel(Environment $env): WhistModel
    {
        return new WhistModel($env);
    }

    public function createFromModel(Model $source): WhistModel
    {
        if (!$source instanceof WhistModel) {
            throw new \Exception("Cannot create Model from this source");
        }

        return new WhistModel($source->getEnv(), unserialize(serialize($source->getNN())));
    }
}