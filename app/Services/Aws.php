<?php

namespace App\Services;

use Aws\Rekognition\RekognitionClient;
use Aws\S3\S3Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class Aws
{
    protected $rekognition;

    protected $s3;

    public function __construct()
    {
        $this->s3 = env('AWS_BUCKET');

        $this->rekognition = new RekognitionClient([
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    public function video_search(string $videoPath)
    {
        $result = $this->rekognition->startContentModeration([
            'Video' => [
                'S3Object' => [
                    'Bucket' => $this->s3,
                    'Name' => $videoPath,
                ],
            ],
        ]);

        return $jobid = $result['JobId'];
    }

    public function image_search(array $imagePaths)
    {
        $results = [];
        // $labels = [];

        foreach ($imagePaths as $path) {
            try {
                $rekognitionResult = $this->rekognition->detectModerationLabels([
                    // $rekognitionResult = $this->rekognition->startContentModeration([
                    'Image' => [
                        'S3Object' => [
                            'Bucket' => env('AWS_BUCKET'),
                            'Name' => $path,
                        ],
                    ],
                ]);

                // $labels = $rekognitionResult['ModerationLabels'] ?? [];

                // log::info('Rekognition Result: '.json_encode($rekognitionResult, JSON_PRETTY_PRINT));

                $labels = $rekognitionResult['ModerationLabels'] ?? [];

                if (empty($labels)) {
                    $results[] = [
                        'image' => $path,
                        'level' => 'safe',
                        'reason' => 'No moderation labels detected',
                        'confidence' => null,
                    ];

                    continue;
                }

                // pick the highest confidence label
                $topLabel = collect($labels)->sortByDesc('Confidence')->first();

                if ($topLabel['Confidence'] >= 70) {
                    $results[] = [
                        'image' => $path,
                        'level' => 'sensitive',
                        'reason' => $topLabel['Name'].' ('.round($topLabel['Confidence'], 2).'%)',
                        'confidence' => round($topLabel['Confidence'], 2),
                    ];
                } else {
                    $results[] = [
                        'image' => $path,
                        'level' => 'safe',
                        'reason' => $topLabel['Name'].' ('.round($topLabel['Confidence'], 2).' - below threshold)',
                        'confidence' => round($topLabel['Confidence'], 2),
                    ];
                }

            } catch (\Aws\Exception\AwsException $e) {
                // in case of AWS error
                $results[] = [
                    'image' => $path,
                    'level' => 'error',
                    'reason' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    // public function upload_to_s3($file

    public function upload_to_s3($file)
    {
        $s3Client = new S3Client([
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        // Generate unique filename (instead of original name to avoid overwrite)
        $fileName = 'images/'.uniqid().'.'.$file->getClientOriginalExtension();

        $result = $s3Client->putObject([
            'Bucket' => env('AWS_BUCKET'),
            'Key' => $fileName,
            'Body' => fopen($file->getPathname(), 'r'),
            // 'ACL'    => 'public-read',
        ]);

        // return $result['ObjectURL'];
        return $fileName;
    }

    public function common_upload_to_s3(array $files, string $folderName)
    {
        $s3Client = new S3Client([
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $uploadedPaths = [];

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue; // skip if it's not a valid uploaded file
            }

            $fileName = $folderName.'/'.uniqid().'.'.strtolower($file->getClientOriginalExtension());
            $mimeType = $file->getMimeType();
            $extension = strtolower($file->getClientOriginalExtension());

            // $s3Client->putObject([
            //     'Bucket' => env('AWS_BUCKET'),
            //     'Key'    => $fileName,
            //     'Body'   => fopen($file->getPathname(), 'r'),
            //     'ContentType' => 'video/mp4',
            //     // 'ACL' => 'public-read', // uncomment if needed
            // ]);

            // Default S3 params
            $params = [
                'Bucket' => env('AWS_BUCKET'),
                'Key' => $fileName,
                'Body' => fopen($file->getPathname(), 'r'),
                // 'ACL' => 'public-read', // uncomment if needed
            ];

            // Check if the file is a video
            $videoExtensions = ['mp4', 'mov', 'avi', 'mkv', 'webm'];
            if (in_array($extension, $videoExtensions)) {
                $params['ContentType'] = 'video/'.$extension; // set content type only for videos
            }

            $s3Client->putObject($params);

            $uploadedPaths[] = $fileName;
        }

        return $uploadedPaths; // returns an array of uploaded S3 keys (paths)
    }

    public static function get_level(array $moderationLabels)
    {
        if (empty($moderationLabels)) {
            return [
                'name' => null,
                'level' => null,
                'confidence' => null,
                'action' => 'SAFE',
            ];
        }

        // Sort by highest level, then confidence
        usort($moderationLabels, function ($a, $b) {
            if ($a['TaxonomyLevel'] == $b['TaxonomyLevel']) {
                return $b['Confidence'] <=> $a['Confidence'];
            }

            return $b['TaxonomyLevel'] <=> $a['TaxonomyLevel'];
        });

        $top = $moderationLabels[0];

        // Decide action based on confidence threshold
        $confidence = $top['Confidence'];
        $action = $confidence >= 70 ? 'BLOCK' : ($confidence >= 50 ? 'REVIEW' : 'SAFE');

        return [
            'name' => $top['Name'],
            'level' => $top['TaxonomyLevel'],
            'confidence' => $confidence,
            'action' => $action,
        ];
    }

    public function get_level_image(array $labels)
    {
        if (empty($labels)) {
            return [
                'level' => 'safe',
                'reason' => 'No moderation labels detected',
            ];
        }

        // pick the highest confidence label
        $topLabel = collect($labels)->sortByDesc('Confidence')->first();

        if ($topLabel['Confidence'] >= 70) {
            return [
                'level' => 'sensitive',
                'reason' => $topLabel['Name'].' ('.round($topLabel['Confidence'], 2).'%)',
            ];
        }

        return [
            'level' => 'safe',
            'reason' => $topLabel['Name'].' ('.round($topLabel['Confidence'], 2).'% - below threshold)',
        ];
    }

    // function for video details status
    // public function get_video_details(string $jobId)
    // {

    //     $status = 'IN_PROGRESS';
    //     $maxRetries = 20; // avoid infinite loop
    //     $retryCount = 0;

    //     do {
    //         sleep(5); // wait 5 seconds before checking
    //         $result_of = $this->rekognition->getContentModeration([
    //             'JobId' => $jobId
    //         ]);

    //         $status = $result_of['JobStatus'];
    //         $retryCount++;
    //     } while ($status === 'IN_PROGRESS' && $retryCount < $maxRetries);

    //     if ($status !== 'SUCCEEDED') {
    //         throw new \Exception("Video moderation did not complete.");
    //     }

    //     // Flatten all ModerationLabels
    //     $allLabels = [];
    //     foreach ($result_of['ModerationLabels'] as $frame) {
    //         if (isset($frame['ModerationLabel'])) {
    //             $allLabels[] = $frame['ModerationLabel'];
    //         }
    //     }

    //     return $this->get_level($allLabels);
    // }

    // get video level for  video serach

    // public static function get_level_video(array $moderationLabels)
    // {
    //     if (empty($moderationLabels)) {
    //         return [
    //             'labels_by_level' => [],
    //             'most_frequent_levels' => [],
    //             'action' => 'SAFE'
    //         ];
    //     }

    //     // 1️⃣ Group labels by TaxonomyLevel
    //     $labelsByLevel = [];
    //     foreach ($moderationLabels as $label) {
    //         $level = $label['TaxonomyLevel'];
    //         $labelsByLevel[$level][] = $label;
    //     }

    //     // 2️⃣ Count labels per level
    //     $levelCounts = [];
    //     foreach ($labelsByLevel as $level => $labels) {
    //         $levelCounts[$level] = count($labels);
    //     }

    //     // 3️⃣ Find max count and all levels with that count (handle ties)
    //     $maxCount = max($levelCounts);
    //     $mostFrequentLevelsKeys = array_keys($levelCounts, $maxCount);

    //     // 4️⃣ Collect names for the most frequent levels
    //     $mostFrequentLevels = [];
    //     foreach ($mostFrequentLevelsKeys as $level) {
    //         $names = array_map(fn($label) => $label['Name'], $labelsByLevel[$level]);
    //         $mostFrequentLevels[$level] = implode(', ', array_unique($names));
    //     }

    //     // 5️⃣ Determine overall action based on confidence
    //     $overallAction = 'SAFE';
    //     foreach ($mostFrequentLevelsKeys as $level) {
    //         foreach ($labelsByLevel[$level] as $label) {
    //             $confidence = $label['Confidence'];
    //             if ($confidence >= 70) {
    //                 $overallAction = 'BLOCK';
    //                 break 2; // BLOCK overrides everything
    //             } elseif ($confidence >= 50 && $overallAction !== 'BLOCK') {
    //                 $overallAction = 'REVIEW';
    //             }
    //         }
    //     }

    //     // 6️⃣ Return full structured JSON
    //     return [
    //         'key' => $mostFrequentLevelsKeys,
    //         'most_frequent_levels' => $mostFrequentLevels,
    //         'action' => $overallAction,
    //         'labels_by_level' => $labelsByLevel,
    //         'level_counts' => $levelCounts,
    //     ];
    // }

    public static function get_level_video(array $allLabelsPerFrame): array
    {
        // Define risky categories (taxonomy level 1 names)
        // $riskyCategories = [
        //     'Drugs & Tobacco',
        //     'Alcohol',
        //     'Weapons',
        //     'Violence',
        //     'Sexual Content',
        //     'Pills',
        //     'Nudity',
        //     'Products',
        //     'Cigarettes'
        // ];

        // log::info('Frames: ' . json_encode($allLabelsPerFrame, JSON_PRETTY_PRINT));

        // Aggregate counts and confidence per risky category
        $categoryData = [];
        $categoryName = [];

        try {

            foreach ($allLabelsPerFrame as $frameLabels) {
                // log::info('Frames: ' . json_encode($frameLabels, JSON_PRETTY_PRINT));
                foreach ($frameLabels as $label) {
                    // Use ParentName if exists, otherwise Name
                    $categoryName = (string) $label['ParentName'] ? $label['ParentName'] : $label['Name'];

                    // log::info('Category Data: ' . json_encode($categoryName, JSON_PRETTY_PRINT));

                    // // Only care about risky categories
                    // if (!in_array($categoryName, $riskyCategories)) {
                    //     continue;
                    // }

                    // if (!isset($categoryData[$categoryName]) || !is_array($categoryData[$categoryName])) {
                    //     $categoryData[$categoryName] = [
                    //         'count' => 0,
                    //         'totalConfidence' => 0.0,
                    //     ];
                    // }

                    $categoryData[$categoryName]['count']++;
                    $categoryData[$categoryName]['totalConfidence'] += (float) $label['Confidence'];
                }
            }
        } catch (\Exception $e) {
            log::info('Category Data: '.json_encode($categoryName, JSON_PRETTY_PRINT));
            Log::error('Error in get_level_video: '.$e->getMessage());
        }

        // Determine the category with max impact
        $maxCategory = null;
        $maxAvgConfidence = 0;

        foreach ($categoryData as $category => $data) {
            $avgConfidence = $data['totalConfidence'] / $data['count'];
            if ($avgConfidence > $maxAvgConfidence) {
                $maxAvgConfidence = $avgConfidence;
                $maxCategory = $category;
            }
        }

        // Decide action based on average confidence thresholds
        $action = 'SAFE';
        if ($maxAvgConfidence >= 70) {
            $action = 'BLOCK';
        } elseif ($maxAvgConfidence >= 50) {
            $action = 'REVIEW';
        }

        return [
            'most_frequent_category' => $maxCategory,
            'average_confidence' => $maxAvgConfidence,
            'action' => $action,
        ];
    }

    // level description

    // function level(array $moderationLabels)
    // {
    //     if (empty($moderationLabels)) {
    //         return [
    //             'all_labels' => [],
    //             'highest_label' => null,
    //             'highest_level' => null,
    //             'level_description' => null,
    //             'conclusion' => 'SAFE'
    //         ];
    //     }

    //     $allLabels = [];
    //     $maxLevel = 0;
    //     $topLabel = null;

    //     // Loop through each label
    //     foreach ($moderationLabels as $label) {
    //         $name  = $label['Name'];
    //         $level = $label['TaxonomyLevel'];
    //         $conf  = $label['Confidence'];

    //         $allLabels[] = [
    //             'name'       => $name,
    //             'level'      => $level,
    //             'confidence' => $conf
    //         ];

    //         // Track the label with the highest level
    //         if ($level > $maxLevel) {
    //             $maxLevel = $level;
    //             $topLabel = $label;
    //         }
    //     }

    //     // Level descriptions
    //     $levelDescriptions = [
    //         1 => 'Top-level category (broad)',
    //         2 => 'Mid-level category (more specific)',
    //         3 => 'Lowest-level category (most specific)'
    //     ];

    //     // Map conclusions based on top label
    //     $conclusion = 'Unknown content';
    //     if ($topLabel) {
    //         $name = $topLabel['Name'];
    //         $level = $topLabel['TaxonomyLevel'];

    //         if ($name === "Smoking" && $level == 3) {
    //             $conclusion = "Contains smoking content";
    //         } elseif ($name === "Drugs & Tobacco" && $level == 1) {
    //             $conclusion = "Drug/Tobacco related content";
    //         } elseif ($name === "Nudity" && $level == 1) {
    //             $conclusion = "Contains nudity";
    //         } elseif ($name === "Violence" && $level == 1) {
    //             $conclusion = "Contains violence";
    //         } else {
    //             $conclusion = "Unknown content";
    //         }
    //     }

    //     return [
    //         'all_labels'       => $allLabels,
    //         'highest_label'    => $topLabel['Name'] ?? null,
    //         'highest_level'    => $maxLevel,
    //         'level_description' => $levelDescriptions[$maxLevel] ?? 'Unknown level',
    //         'conclusion'       => $conclusion
    //     ];
    // }
}
