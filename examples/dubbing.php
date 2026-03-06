<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Camb\Ai\CambAIClient;
use Camb\Ai\Model\EndToEndDubbingRequestPayload;
use Camb\Ai\Model\Languages;
use Camb\Ai\Model\TaskStatus;

$apiKey = $argv[1] ?? getenv('CAMBAI_API_KEY');
if (!$apiKey) {
    echo "Usage: php dubbing.php <API_KEY>\n";
    exit(1);
}

$client = new CambAIClient($apiKey);

$payload = new EndToEndDubbingRequestPayload();
$payload->setVideoUrl("https://github.com/Camb-ai/cambai-python-sdk/raw/main/tests/data/test_video.mp4");
$payload->setSourceLanguage(Languages::EN_US);
$payload->setTargetLanguages([Languages::FR_FR]);

try {
    echo "Starting dubbing task...\n";
    $result = $client->dub->endToEndDubbingDubPost($payload);
    $taskId = $result->getTaskId();
    echo "Task ID: " . $taskId . "\n";

    // Polling for status
    while (true) {
        sleep(5);
        $statusResult = $client->dub->getEndToEndDubbingStatusDubTaskIdGet($taskId);
        $status = $statusResult->getStatus();
        echo "Status: " . $status . "\n";

        if ($status === TaskStatus::SUCCESS) {
            $runId = $statusResult->getRunId();
            $dubResult = $client->dub->getDubbedRunInfoDubResultRunIdGet($runId);

            $videoUrl = $dubResult->getDubbingResult()->getVideoUrl();
            if ($videoUrl) {
                echo "✓ Success! Dubbed Video URL: " . $videoUrl . "\n";
            } else {
                echo "✓ Success, but no video URL found in result.\n";
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
