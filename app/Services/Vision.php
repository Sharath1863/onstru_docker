<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Vision
{
    protected $apiKey;
    // protected $apiurl;

    public function __construct()
    {
        $this->apiKey = env('GOOGLE_VISION_API_KEY');
        // $this->apiurl = env('GOOGLE_VISION_API_URL', 'https://vision.googleapis.com/v1/images:annotate');
    }

    public function detectSafeSearch(string $imagePath): array
    {
        $imageContent = base64_encode(file_get_contents($imagePath));

        try {

            $response = Http::post(
                "https://vision.googleapis.com/v1/images:annotate?key={$this->apiKey}",
                [
                    'requests' => [
                        [
                            'image' => [
                                'content' => $imageContent
                            ],
                            'features' => [
                                ['type' => 'SAFE_SEARCH_DETECTION']
                            ]
                        ]
                    ]
                ]
            );

            $data = $response->json();

            $safeSearch = $data['responses'][0]['safeSearchAnnotation'] ?? [];

            $levels = [
                'UNKNOWN' => 0,
                'VERY_UNLIKELY' => 1,
                'UNLIKELY' => 2,
                'POSSIBLE' => 3,
                'LIKELY' => 4,
                'VERY_LIKELY' => 5,
            ];


            return [
                'adult' => $levels[$safeSearch['adult']] ?? 0,
                'spoof' => $levels[$safeSearch['spoof']] ?? 0,
                'medical' => $levels[$safeSearch['medical']] ?? 0,
                'violence' => $levels[$safeSearch['violence']] ?? 0,
                'racy' => $levels[$safeSearch['racy']] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('Google Vision API Error: ' . $e->getMessage());
            return [
                'adult' => 0,
                'spoof' => 0,
                'medical' => 0,
                'violence' => 0,
                'racy' => 0,
            ];
        }
    }

    // video serach

    public function detect_video(string $gcsUri): array
    {

        $payload = [
            'inputUri' => $gcsUri,
            'features' => ['SAFE_SEARCH_DETECTION']
        ];


        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(
            "https://videointelligence.googleapis.com/v1/videos:annotate?key={$this->apiKey}",
            $payload
        );

        // Returns an operation object (long-running)
        $operation = $response->json();

        $operationName = $operation['name'] ?? null;

        log::info($response);

        if (!$operationName) {
            return [];
        }

        // Poll until operation completes
        do {
            sleep(5); // wait a few seconds
            $statusResponse = Http::get(
                "https://videointelligence.googleapis.com/v1/operations/{$operationName}?key={$this->apiKey}"
            );
            $status = $statusResponse->json();
        } while (!($status['done'] ?? false));

        // Get SafeSearch annotations
        $safeSearchFrames = [];
        $results = $status['response']['annotationResults'][0]['safeSearchAnnotations'] ?? [];

        foreach ($results as $annotation) {
            $safeSearchFrames[] = [
                'time_offset' => $annotation['timeOffset'] ?? null,
                'adult' => $annotation['adult'] ?? 'UNKNOWN',
                'violence' => $annotation['violence'] ?? 'UNKNOWN',
                'racy' => $annotation['racy'] ?? 'UNKNOWN',
                'medical' => $annotation['medical'] ?? 'UNKNOWN',
                'spoof' => $annotation['spoof'] ?? 'UNKNOWN',
            ];
        }

        return $safeSearchFrames;
    }
}
