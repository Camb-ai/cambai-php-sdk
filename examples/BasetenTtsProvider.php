<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Camb\Ai\Provider\TtsProviderInterface;
use Camb\Ai\Model\CreateTTSOut;
use GuzzleHttp\Client;

/**
 * Baseten TTS Provider using the Mars8-Flash model.
 *
 * API reference: https://www.baseten.co/library/mars8-flash/
 *
 * Required constructor params:
 *   $apiKey          - Baseten API key
 *   $basetenUrl      - Your Baseten model prediction endpoint
 *   $referenceAudio  - Reference audio: public URL or base64-encoded file
 *   $referenceLanguage - ISO locale of the reference audio (e.g. "en-us")
 */
class BasetenTtsProvider implements TtsProviderInterface
{
    private $apiKey;
    private $basetenUrl;
    private $referenceAudio;
    private $referenceLanguage;
    private $client;

    public function __construct(string $apiKey, string $basetenUrl, string $referenceAudio, string $referenceLanguage = 'en-us')
    {
        $this->apiKey = $apiKey;
        $this->basetenUrl = $basetenUrl;
        $this->referenceAudio = $referenceAudio;
        $this->referenceLanguage = $referenceLanguage;
        $this->client = new Client();
    }

    /**
     * Baseten does not support async TTS. This method is a stub.
     */
    public function createTts($payload, $run_id = null)
    {
        throw new \RuntimeException('Baseten custom hosting provider does not support async createTts(). Use tts() for streaming.');
    }

    /**
     * Calls the Baseten Mars8-Flash endpoint and returns the audio stream.
     *
     * Reference audio (reference_audio) can be:
     *   - A publicly accessible URL (recommended for caching/speed)
     *   - A base64-encoded audio file (e.g. wav, flac, m4a)
     */
    public function tts($payload)
    {
        // Normalise language: SDK may return the enum value, force lowercase ISO string
        $language = strtolower(str_replace('_', '-', (string) ($payload->getLanguage() ?? 'en-us')));

        // Build the Mars8-Flash payload.
        // Docs: https://www.baseten.co/library/mars8-flash/
        $body = [
            'text' => $payload->getText(),
            'language' => $language,
            'output_duration' => null,          // null = model infers duration
            'reference_audio' => $this->referenceAudio,
            'reference_language' => $this->referenceLanguage,
            'output_format' => 'flac',        // flac is the default; wav also supported
            'apply_ner_nlp' => false,         // disable NER (faster; pass a pronunciation_dictionary instead)
        ];

        $response = $this->client->post($this->basetenUrl, [
            'headers' => [
                'Authorization' => 'Api-Key ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $body,
            'stream' => true,
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException(
                'Baseten API error: ' . $response->getStatusCode() . ' ' . $response->getBody()
            );
        }

        return $response->getBody();
    }
}
