<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Contracts\OpenAI\Response createResponse(\App\Contracts\OpenAI\ResponseInput $input, \App\Contracts\OpenAI\ResponseOptions|null $options = null)
 * @method static \App\Contracts\OpenAI\Response getResponse(string $responseId)
 * @method static \App\Contracts\OpenAI\Response cancelResponse(string $responseId)
 * @method static \App\Contracts\OpenAI\Response deleteResponse(string $responseId)
 * @method static \App\Contracts\OpenAI\OpenAIClient driver(string|null $driver = null)
 *
 * @see \App\Services\OpenAI\OpenAIManager
 */
class OpenAI extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'openai.manager';
    }
}
