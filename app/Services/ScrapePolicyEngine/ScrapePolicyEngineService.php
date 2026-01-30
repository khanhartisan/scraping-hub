<?php

namespace App\Services\ScrapePolicyEngine;

use App\Contracts\ScrapePolicyEngine\PolicyResult;
use App\Contracts\ScrapePolicyEngine\ScrapePolicyEngine as ScrapePolicyEngineContract;
use App\Models\Entity;
use Carbon\Carbon;

abstract class ScrapePolicyEngineService implements ScrapePolicyEngineContract
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Evaluate the scraping policy for an entity.
     */
    public function evaluate(Entity $entity, ?Carbon $baseTime = null): PolicyResult
    {
        $baseTime = $baseTime ?? Carbon::now();

        return $this->performEvaluation($entity, $baseTime);
    }

    /**
     * Perform the actual policy evaluation.
     * This method must be implemented by child classes.
     */
    abstract protected function performEvaluation(Entity $entity, Carbon $baseTime): PolicyResult;
}
