<?php

namespace Camb\Ai;

use \Camb\Ai\Api\TextToSpeechApi;
use \Camb\Ai\Api\TextToVoiceApi;
use \Camb\Ai\Api\TextToAudioApi;
use \Camb\Ai\Api\DubApi;
use \Camb\Ai\Configuration;
use \Camb\Ai\Provider\TtsProviderInterface;
use \Camb\Ai\Provider\DefaultTtsProvider;

use GuzzleHttp\Client;

class CambAIClient
{
    private $config;
    private $httpClient;

    public $textToSpeech;
    public $textToVoice;
    public $textToAudio;
    public $dub;

    private $textToSpeechApi;
    private $textToVoiceApi;
    private $textToAudioApi;
    private $dubApi;

    private $ttsProvider;

    public function __construct(string $apiKey)
    {
        $this->config = Configuration::getDefaultConfiguration()->setApiKey('x-api-key', $apiKey);
        $this->httpClient = new Client(['timeout' => 300.0]);

        $this->textToSpeech = $this->getTextToSpeechApi();
        $this->textToVoice = $this->getTextToVoiceApi();
        $this->textToAudio = $this->getTextToAudioApi();
        $this->dub = $this->getDubApi();
    }

    public function setTtsProvider(TtsProviderInterface $provider)
    {
        $this->ttsProvider = $provider;
    }

    public function tts()
    {
        if ($this->ttsProvider) {
            return $this->ttsProvider;
        }
        return new DefaultTtsProvider($this->getTextToSpeechApi());
    }

    public function getTextToSpeechApi()
    {
        if (!$this->textToSpeechApi) {
            $this->textToSpeechApi = new TextToSpeechApi($this->httpClient, $this->config);
        }
        return $this->textToSpeechApi;
    }

    public function getTextToVoiceApi()
    {
        if (!$this->textToVoiceApi) {
            $this->textToVoiceApi = new TextToVoiceApi($this->httpClient, $this->config);
        }
        return $this->textToVoiceApi;
    }

    public function getTextToAudioApi()
    {
        if (!$this->textToAudioApi) {
            $this->textToAudioApi = new TextToAudioApi($this->httpClient, $this->config);
        }
        return $this->textToAudioApi;
    }

    public function getDubApi()
    {
        if (!$this->dubApi) {
            $this->dubApi = new DubApi($this->httpClient, $this->config);
        }
        return $this->dubApi;
    }
}
