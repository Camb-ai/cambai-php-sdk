<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Camb\Ai\CambAIClient;
use Camb\Ai\Model\CreateTextToVoiceRequestPayload;
use Camb\Ai\Model\TaskStatus;

$apiKey = $argv[1] ?? getenv('CAMBAI_API_KEY');
if (!$apiKey) {
    echo "Usage: php text_to_voice.php <API_KEY>\n";
    exit(1);
}

$client = new CambAIClient($apiKey);

$payload = new CreateTextToVoiceRequestPayload();
$payload->setText("This is a sample text for voice generation.");
$payload->setVoiceDescription("A calm, deep male voice suitable for meditation.");

try {
    echo "Generating voice task...\n";
    $result = $client->textToVoice->createTextToVoiceTextToVoicePost($payload);
    $taskId = $result->getTaskId();
    echo "Task ID: " . $taskId . "\n";

    // Polling for status
    while (true) {
        sleep(2);
        $statusResult = $client->textToVoice->getTextToVoiceStatusTextToVoiceTaskIdGet($taskId);
        $status = $statusResult->getStatus();
        echo "Status: " . $status . "\n";

        if ($status === TaskStatus::SUCCESS) {
            $runId = $statusResult->getRunId();
            $voiceResult = $client->textToVoice->getTextToVoiceResultTextToVoiceResultRunIdGet($runId);
            echo "✓ Success! Generated Voice Previews: " . json_encode($voiceResult->getPreviews()) . "\n";
            break;
        } elseif ($status === TaskStatus::ERROR) {
            echo "Task failed.\n";
            break;
        }
    }

} catch (Exception $e) {
    echo 'Error: ', $e->getMessage(), PHP_EOL;
}
