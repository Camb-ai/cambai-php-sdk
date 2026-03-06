<?php

namespace Camb\Ai\Provider;

use \Camb\Ai\Api\TextToSpeechApi;

class DefaultTtsProvider implements TtsProviderInterface
{
    private $api;

    public function __construct(TextToSpeechApi $api)
    {
        $this->api = $api;
    }

    public function createTts($payload, $run_id = null)
    {
        return $this->api->createTtsTtsPost($payload, $run_id);
    }

    public function tts($payload)
    {
        return $this->api->ttsTtsStreamPost($payload);
    }
}
