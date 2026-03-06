<?php
/**
 * Example: Baseten Mars8-Flash custom hosting provider via the Camb.ai PHP SDK.
 *
 * Required environment variables:
 *   CAMB_API_KEY            - Your Camb.ai API key
 *   BASETEN_API_KEY         - Your Baseten API key
 *   BASETEN_URL             - Your Baseten model prediction endpoint URL
 *   BASETEN_REFERENCE_AUDIO - Reference voice audio: public URL or base64-encoded file
 *                             e.g. https://github.com/Camb-ai/mars6-turbo/raw/refs/heads/master/assets/example.wav
 *
 * Optional:
 *   BASETEN_REFERENCE_LANGUAGE - ISO locale of the reference audio (default: en-us)
 *
 * API reference: https://www.baseten.co/library/mars8-flash/
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/BasetenTtsProvider.php';

use Camb\Ai\CambAIClient;
use Camb\Ai\Model\CreateStreamTTSRequestPayload;

// --- Loud fail on missing env vars ---
$cambApiKey = getenv('CAMB_API_KEY');
$basetenApiKey = getenv('BASETEN_API_KEY');
$basetenUrl = getenv('BASETEN_URL');
$referenceAudio = getenv('BASETEN_REFERENCE_AUDIO');
$referenceLanguage = getenv('BASETEN_REFERENCE_LANGUAGE') ?: 'en-us';

$missing = [];
if (!$cambApiKey)
    $missing[] = 'CAMB_API_KEY';
if (!$basetenApiKey)
    $missing[] = 'BASETEN_API_KEY';
if (!$basetenUrl)
    $missing[] = 'BASETEN_URL (your Baseten model prediction endpoint)';
if (!$referenceAudio)
    $missing[] = 'BASETEN_REFERENCE_AUDIO (public URL or base64-encoded audio file)';

if (!empty($missing)) {
    fwrite(STDERR, "Error: Missing required environment variables:\n");
    foreach ($missing as $var) {
        fwrite(STDERR, "  - $var\n");
    }
    exit(1);
}

// --- Initialise client and Baseten provider ---
$client = new CambAIClient(['apiKey' => $cambApiKey]);

$basetenProvider = new BasetenTtsProvider(
    $basetenApiKey,
    $basetenUrl,
    $referenceAudio,
    $referenceLanguage
);

$client->setTtsProvider($basetenProvider);

// --- Build the TTS request ---
// voice_id is required by the Camb SDK payload validation but is ignored
// when a custom hosting provider is active.
$payload = new CreateStreamTTSRequestPayload();
$payload->setText('Hello. This is speech generated via a Baseten Mars8-Flash custom hosting provider.');
$payload->setLanguage(CreateStreamTTSRequestPayload::LANGUAGE_EN_US);

echo "Generating speech via Baseten Mars8-Flash...\n";

try {
    $audioStream = $client->tts()->tts($payload);

    $outputFile = 'baseten_output.flac';
    file_put_contents($outputFile, $audioStream);

    echo "✓ Success! Audio saved to {$outputFile}\n";
} catch (\Exception $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}
