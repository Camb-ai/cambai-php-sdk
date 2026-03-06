<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Camb\Ai\Provider\TtsProviderInterface;
use Camb\Ai\Model\CreateTTSRequestPayload;
use Camb\Ai\Model\CreateTTSOut;

class BasetenTtsProvider implements TtsProviderInterface
{
    private $apiKey;
    private $basetenUrl;

    public function __construct($apiKey, $basetenUrl)
    {
        $this->apiKey = $apiKey;
        $this->basetenUrl = $basetenUrl;
    }

    public function createTts($payload, $run_id = null)
    {
        echo "[BasetenProvider] Calling Baseten API at " . $this->basetenUrl . "\n";
        echo "[BasetenProvider] Payload content: " . $payload->getText() . "\n";

        $out = new CreateTTSOut();
        $out->setTaskId("baseten-task-" . rand(1000, 9999));

        return $out;
    }

    public function tts($payload)
    {
        echo "[BasetenProvider] Calling Baseten Streaming API at " . $this->basetenUrl . "\n";

        // This would typically return a stream or a response object that can be read as a stream
        return "[BasetenProvider] Simulated Audio Stream";
    }
}
