<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/BasetenTtsProvider.php';

use Camb\Ai\CambAIClient;
use Camb\Ai\Model\CreateTTSRequestPayload;
use Camb\Ai\Model\CreateStreamTTSRequestPayload;
use Camb\Ai\Model\Languages;

$apiKey = $argv[1] ?? getenv('CAMBAI_API_KEY');
$client = new CambAIClient($apiKey ?? "dummy_key");

// Implement a custom provider (e.g., Baseten)
$basetenProvider = new BasetenTtsProvider("baseten_key_123", "https://model-id.api.baseten.co/production/predict");

// Register the custom provider
$client->setTtsProvider($basetenProvider);

// 1. Standard TTS via Custom Provider
$payload = new CreateTTSRequestPayload();
$payload->setText("Hello from Baseten Provider!");
$payload->setLanguage(Languages::EN_US);

echo "Creating TTS via Custom Provider...\n";
$response = $client->tts()->createTts($payload);
echo "Result Task ID: " . $response->getTaskId() . "\n";

// 2. Streaming TTS via Custom Provider
$streamPayload = new CreateStreamTTSRequestPayload();
$streamPayload->setText("Streaming from Baseten!");
$streamPayload->setLanguage(CreateStreamTTSRequestPayload::LANGUAGE_EN_US);

echo "Streaming TTS via Custom Provider...\n";
$stream = $client->tts()->tts($streamPayload);
echo "Result: $stream\n";
