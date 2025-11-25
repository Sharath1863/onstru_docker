<?php

namespace App\Jobs;

use App\Services\Aws;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Video_process implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $aws;

    protected array $videoKey;

    protected $s3Bucket;

    // Assign a queue name
    // public $queue = 'video_check';

    public function __construct(array $videoKey)
    {
        $this->aws = new Aws;
        // $this->s3Bucket = $s3Bucket;
        $this->videoKey = $videoKey;

        $this->s3Bucket = env('AWS_BUCKET');

        // log::info('Job initialized with videoKey', ['videoKey' => $this->videoKey]);
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $result = new \Aws\Rekognition\RekognitionClient([
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        // $jobId = $result['JobId'];

        $status = 'IN_PROGRESS';
        $maxRetries = 10; // avoid infinite loop
        $retryCount = 0;

        do {
            sleep(5); // wait 5 seconds before checking
            $result_of = $result->getContentModeration([
                'JobId' => $this->videoKey['job_id'],
            ]);

            $status = $result_of['JobStatus'];
            $retryCount++;
        } while ($status === 'IN_PROGRESS' && $retryCount < $maxRetries);

        if ($status !== 'SUCCEEDED') {
            throw new \Exception('Video moderation did not complete.');
        }

        // Flatten all ModerationLabels
        $allLabels = [];
        foreach ($result_of['ModerationLabels'] as $frame) {
            if (isset($frame['ModerationLabel'])) {
                $allLabels[] = $frame['ModerationLabel'];
            }
        }

        try {
            $analysis = $this->aws->get_level_image($allLabels);
        } catch (\Exception $e) {
            Log::error('Error in get_level: '.$e->getMessage());
        }

        // Log::info('videoKey type', ['videoKey' => ($allLabels)]);
        // Log::info('videoKey contents', ['videoKey' => $this->videoKey]);
        // Log::info('post_id type', ['post_id' => gettype($this->videoKey['post_id'] ?? null)]);
        // Log::info('post_id value', ['post_id' => $this->videoKey['post_id'] ?? null]);
        // Log::info('analysis type', ['analysis' => ($analysis)]);

        Log::info('analysis type: '.json_encode($analysis, JSON_PRETTY_PRINT));
    }
}
