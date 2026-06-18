# Camb.ai PHP SDK

<div id="top" align="center">

   ![Banner](assets/banner5_720.jpg)
   <h3>
   <a href="https://camb.ai/"> Camb AI Website </a></h3>

</div>

The official PHP SDK for interacting with Camb AI's powerful voice and audio generation APIs. Create expressive speech, unique voices, and rich soundscapes with just a few lines of PHP.

## ✨ Features

- **Dubbing**: Dub your videos into multiple languages with voice cloning!
- **Expressive Text-to-Speech**: Convert text into natural-sounding speech using a wide range of pre-existing voices.
- **Generative Voices**: Create entirely new, unique voices from text prompts and descriptions.
- **Soundscapes from Text**: Generate ambient audio and sound effects from textual descriptions.
- Access to voice cloning, translation, and more (refer to full API documentation).

## 📦 Installation

To install the bindings via [Composer](http://getcomposer.org/), add the following to `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/Camb-ai/cambai-php-sdk.git"
    }
  ],
  "require": {
    "camb-ai/cambai-php-sdk": "*@dev"
  }
}
```

Then run `composer install`.

## 🔑 Authentication & Accessing Clients

To use the Camb AI SDK, you'll need an API key.

```php
require_once __DIR__ . '/vendor/autoload.php';

use Camb.ai\CambAIClient;

$client = new CambAIClient("YOUR_CAMB_API_KEY");
```

## 🚀 Getting Started: Examples

NOTE: For more examples, refer to the `examples/` directory.

#### Streaming TTS request options

`$client->tts()->tts(...)` accepts the core request fields plus optional controls for model behavior and output format:

| Field | Description |
| :--- | :--- |
| `setText(...)` | Text to synthesize. For MARS Instruct, you can include inline emotion or pacing tags. |
| `setLanguage(...)` | Locale such as `CreateStreamTTSRequestPayload::LANGUAGE_EN_US`. |
| `setVoiceId(...)` | Voice profile ID from the voice list APIs. |
| `setSpeechModel(...)` | Model to use, such as `SPEECH_MODEL_MARS_PRO`, `SPEECH_MODEL_MARS_INSTRUCT`, or `SPEECH_MODEL_MARS_FLASH`. |
| `setUserInstructions(...)` | Adds style, tone, pronunciation, or delivery guidance for the request. Available only with MARS Instruct. |
| `setOutputConfiguration(...)` | Output settings such as audio format, duration, and enhancement. |
| `setVoiceSettings(...)` | Voice behavior controls such as reference enhancement or accent preservation. |
| `setInferenceOptions(...)` | Advanced generation controls for supported models. |
| `setEnhanceNamedEntitiesPronunciation(...)` | Improves pronunciation for names and other named entities when supported. |

```php
use Camb\Ai\Model\CreateStreamTTSRequestPayload;
use Camb\Ai\Model\StreamTTSOutputConfiguration;

$outputConfig = new StreamTTSOutputConfiguration();
$outputConfig->setFormat('wav');

$payload = new CreateStreamTTSRequestPayload();
$payload->setText("[warm, friendly] Great to meet you!");
$payload->setVoiceId(20303);
$payload->setLanguage(CreateStreamTTSRequestPayload::LANGUAGE_EN_US);
$payload->setSpeechModel(CreateStreamTTSRequestPayload::SPEECH_MODEL_MARS_INSTRUCT);
$payload->setUserInstructions("Speak warmly and with enthusiasm.");
$payload->setOutputConfiguration($outputConfig);

$audioStream = $client->tts()->tts($payload);
```

### 1. Text-to-Speech (TTS)

```php
use Camb.ai\Model\CreateTTSRequestPayload;
use Camb.ai\Model\Languages;

$payload = new CreateTTSRequestPayload();
$payload->setText("Hello from Camb AI!");
$payload->setVoiceId(20303);
$payload->setLanguage(Languages::EN_US);

$response = $client->tts()->createTts($payload);
echo "Task ID: " . $response->getTaskId();
```

### 2. Text-to-Voice (Generative Voice)

```php
use Camb.ai\Model\CreateTextToVoiceRequestPayload;

$payload = new CreateTextToVoiceRequestPayload();
$payload->setText("Creating a unique voice.");
$payload->setVoiceDescription("A deep, resonant male voice.");

$result = $client->getTextToVoiceApi()->createTextToVoiceTextToVoicePost($payload);
```

### 3. Text-to-Audio (Sound Generation)

```php
use Camb.ai\Model\CreateTextToAudioRequestPayload;

$payload = new CreateTextToAudioRequestPayload();
$payload->setPrompt("A gentle breeze.");
$payload->setDuration(5.0);

$result = $client->getTextToAudioApi()->createTextToAudioTextToSoundPost($payload);
```

### 4. End-to-End Dubbing

```php
use Camb.ai\Model\EndToEndDubbingRequestPayload;
use Camb.ai\Model\Languages;

$payload = new EndToEndDubbingRequestPayload();
$payload->setVideoUrl("https://example.com/video.mp4");
$payload->setSourceLanguage(Languages::EN_US);
$payload->setTargetLanguages([Languages::FR_FR]);

$result = $client->getDubApi()->endToEndDubbingDubPost($payload);
```

### 5. Custom Provider (Baseten) support

You can swap the backend TTS provider while keeping the same interface (e.g. for Baseten).

```php
require_once 'examples/BasetenTtsProvider.php';

$basetenProvider = new BasetenTtsProvider("baseten_key", "https://model-url...");
$client->setTtsProvider($basetenProvider);

// Calls Baseten provider instead of default Camb.ai API
$client->tts()->createTts($payload);
```

## License

This project is licensed under the MIT License.
