<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Camb\Ai\CambAIClient;
use Camb\Ai\Model\CreateTextToAudioRequestPayload;
use Camb\Ai\Model\TaskStatus;

$apiKey = $argv[1] ?? getenv('CAMBAI_API_KEY');
if (!$apiKey) {
    echo "Usage: php text_to_audio.php <API_KEY>\n";
    exit(1);
}

$client = new CambAIClient($apiKey);

$payload = new CreateTextToAudioRequestPayload();
$payload->setPrompt("A futuristic sci-fi laser sound effect");
$payload->setDuration(3.0);

try {
    echo "Creating sound effect task...\n";
    $result = $client->textToAudio->createTextToAudioTextToSoundPost($payload);
    $taskId = $result->getTaskId();
    echo "Task ID: " . $taskId . "\n";

    // Polling for status
    while (true) {
        sleep(2);
        $statusResult = $client->textToAudio->getTextToAudioStatusTextToSoundTaskIdGet($taskId);
        $status = $statusResult->getStatus();
        echo "Status: " . $status . "\n";

        if ($status === TaskStatus::SUCCESS) {
            echo "Task completed! Downloading result...\n";
            $runId = $statusResult->getRunId();

            $audioStream = $client->textToAudio->getTextToAudioResultTextToSoundResultRunIdGet($runId);

            $outputFile = "text_to_audio_output.wav";
            $file = fopen($outputFile, "w");
            if ($file) {
                while (!$audioStream->eof()) {
                    fwrite($file, $audioStream->fread(8192));
                }
                fclose($file);
                echo "✓ Success! Audio saved to $outputFile\n";
            }
            break;
        } elseif ($status === TaskStatus::ERROR) {
            echo "Task failed.\n";
            break;
        }
    }

} catch (Exception $e) {
    echo 'Error: ', $e->getMessage(), PHP_EOL;
}
