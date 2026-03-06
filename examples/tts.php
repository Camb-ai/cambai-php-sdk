<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Camb\Ai\CambAIClient;
use Camb\Ai\Model\CreateStreamTTSRequestPayload;
use Camb\Ai\Model\StreamTTSOutputConfiguration;

$apiKey = $argv[1] ?? getenv('CAMBAI_API_KEY');
if (!$apiKey) {
    echo "Usage: php stream_tts.php <API_KEY>\n";
    exit(1);
}

$client = new CambAIClient($apiKey);

$outputConfig = new StreamTTSOutputConfiguration();
$outputConfig->setFormat('wav');

$payload = new CreateStreamTTSRequestPayload();
$payload->setText("Hello from Camb AI Streaming TTS! This is live audio.");
$payload->setVoiceId(20303);
$payload->setLanguage(CreateStreamTTSRequestPayload::LANGUAGE_EN_US);
$payload->setSpeechModel(CreateStreamTTSRequestPayload::SPEECH_MODEL_MARS_FLASH);
$payload->setOutputConfiguration($outputConfig);

try {
    echo "Starting Stream TTS...\n";
    $audioStream = $client->tts()->tts($payload);

    $outputFile = "stream_tts_output.wav";
    $file = fopen($outputFile, "w");
    if ($file) {
        while (!$audioStream->eof()) {
            fwrite($file, $audioStream->fread(8192));
        }
        fclose($file);
        echo "✓ Success! Audio saved to $outputFile\n";
    }

} catch (Exception $e) {
    echo 'Error: ', $e->getMessage(), PHP_EOL;
}
