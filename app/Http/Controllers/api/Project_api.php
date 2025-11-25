<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;
use App\Services\Aws;
use Illuminate\Support\Facades\Log;
use App\Models\ReadyToWork;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class Project_api extends Controller
{
    //project created list
    public function project_created_list(Request $request)
    {
        if($request->user_id){
            $userId=$request->user_id;
        }
        
        try {
            //$userId = $userId ?? Auth::id() ;
            $projects = Project::with('locationDetails')
            ->where('created_by', $userId ?? Auth::id())
            ->latest()
            ->get();

        // Add 'status' to each project
        // $projects->transform(function ($project) {
        //     $project->project_status = $project->end_date > now() ? 'ongoing' : 'completed';
        //     return $project;
        // });

            
        $projects->transform(function ($project) {
            $endDate = Carbon::parse($project->end_date);
            $startDate = Carbon::parse($project->start_date);

            if ($startDate->isFuture()) {
                $project->project_status = 'upcoming';
            } elseif ($endDate->isToday() || $endDate->isFuture()) {
                $project->project_status = 'ongoing';
            } else {
                $project->project_status = 'completed';
            }


            return $project;
        });

            return response()->json(['success' => true,
                'data' => $projects], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //project profile api
    public function project_profile_api(Request $req, $id = null)
    {

        $validator = Validator::make($req->all(), [
            'project_id' => 'required|exists:projects,id',
            ],

            [
                'project_id.required' => 'job id is required.',
               
            ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $projectId = $id ?? $req->input('project_id');
        try {

            $project = Project::with('locationDetails:id,value')->find($projectId);

            if (!$project) {
                return response()->json(['error' => 'Project not found'], 404);
            }
          

            $sub_images = json_decode($project->sub_image, true);

            $fullImagePaths = [];
            
            if (is_array($sub_images)) {
                foreach ($sub_images as $index => $img) {
                    if ($index === 0) {
                        continue; // Skip the first image (0th index)
                    }
                    $fullImagePaths[] = $img;
                }
            }
            
            // if (!empty($service->video)) {
            //     $fullImagePaths[] = $service->video; // or asset($service->video)
            // }

            $data = [
                'project_id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'location' => $project->locationDetails->value ?? null,
                'start_date' => $project->start_date->format('d-m-Y'),
                'end_date' => $project->end_date->format('d-m-Y'),
                'key_outcomes' => $project->key_outcomes ?? null,
                'prjt_budget' => $project->prjt_budget ?? null,
                'job_role' => $project->job_role ?? null,
                'responsible' => $project->responsibilities ?? null,
                'cover_image' => $project->image ?? null,
                'sub_images'=> $fullImagePaths,
                'project_amount'=>$project->amount
            ];

            return response()->json(['success'=>true,
            'project_data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Project not found'], 404);
        }
    }

    //get project details
     public function getProject(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'project_id' => 'required|exists:projects,id',
                ],
                [
                    'project_id.required' => 'job id is required.',      
                ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            if ($request->project_id) {
                $id = $request->project_id;
            }

            $project = Project::with('locationDetails:id,value')
                ->where('id', $id)
                ->where('created_by', Auth::id())
                ->firstOrFail();

            if (!$project) {
                return response()->json(['error' => 'Project not found for the person'], 404);
            }

            $sub_images = json_decode($project->sub_image, true);
            // Convert image paths to full URLs
            $fullImagePaths = [];
            if (is_array($sub_images)) {
                foreach ($sub_images as $index => $img) {
                    if ($index === 0) continue; // Skip first image
                    $fullImagePaths[] = $img;  
                }
            }
            $data = [
                'project_id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'location' => $project->locationDetails,
                'start_date' => $project->start_date->format('d-m-Y'),
                'end_date' => $project->end_date->format('d-m-Y'),
                // 'service_image' => asset('image/product-bricks.jpg'),
                'key_outcomes' => $project->key_outcomes ?? null,
                'prjt_budget' => $project->prjt_budget ?? null,
                'job_role' => $project->job_role ?? null,
                'responsible' => $project->responsibilities ?? null,
                'cover_image' => $project->image ?? null,
                'sub_images'=> $fullImagePaths
               
            ];
            if ($request->header('Authorization')) {
                return response()->json(['message' => 'Project Details',
                    'success' => true,
                    'data' => $data,
                   
                ], 200);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Project not found'], 404);
        }
    }


    public function projects_update(Request $request, Aws $aws)
    {
        //Log::info($request->all());
        try {
            $validator = Validator::make($request->all(), [
                'project_id' => 'required|exists:projects,id',
                ],
                [
                    'project_id.required' => 'job id is required.',      
                ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            if ($request->project_id) {
                $id = $request->project_id;
            }
            $project = Project::where('id', $id)
                ->where('created_by', Auth::id())
                ->firstOrFail();

            $images = json_decode($project->sub_image, true) ?? [];
            $cover_image = $project->image;
        

            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'location' => 'required|exists:dropdown_lists,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'key_outcomes' => 'required|string',
                'prjt_budget' => 'required|string|max:255',
                'job_role' => 'required|string|max:255',
                'responsibile' => 'required|string',
                'image-1'   => 'nullable|file|max:5120',
                'image-2'   => 'nullable|file|max:5120',
                'image-3'   => 'nullable|file|max:5120',
                'image-4'   => 'nullable|file|max:5120',
            ];

           
            $request->validate($rules);

            $data = [
                'title' => $request->title,
                'description' => $request->description,
                'location' => $request->location,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'key_outcomes' => $request->key_outcomes,
                'prjt_budget' => $request->prjt_budget,
                'job_role' => $request->job_role,
                'responsibilities' => $request->responsibilities,
            ];

              // Normalize and handle delete_images[]
              $rawDeleteImages = $request->input('delete_images', []);

              // Delete by field name like 'service-image-2'$index = (int)$matches[1] - 1;
              foreach ($rawDeleteImages as $field) {
                  if (preg_match('/image-(\d+)/', $field, $matches)) {
                      $index = (int)$matches[1] - 1; // service-image-1 => index 0, service-image-2 => index 1//
                      if (isset($images[$index])) {
                          unset($images[$index]);
                      }
                  }
              }
            $images = array_values($images); // Reindex
    
            // 🔄 Upload new images
            $uploaded = [];
            $folder = 'project_images';
            $s3Keys = [];
    
            foreach (['image-1', 'image-2', 'image-3', 'image-4'] as $index => $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $uploaded = $aws->common_upload_to_s3([$file], $folder);
                    $uploadedUrl = is_array($uploaded) ? $uploaded[0] : $uploaded;
    
                    // If it's the first image, treat it as cover image
                    if ($field === 'image-1') {
                        $cover_image = $uploadedUrl;
                    
                        // If sub_images has at least one image, remove the first one (old cover image)
                        if (!empty($images)) {
                            array_shift($images);
                        }
                    
                        // Insert the new cover image at the beginning
                        array_unshift($images, $cover_image);
                    }
                     else {
                        $images[] = $uploadedUrl;
                    }
                }
            }

           
            $data = [
                'title' => $request->title,
                'description' => $request->description,
                'location' => $request->location,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'key_outcomes' => $request->key_outcomes,
                'prjt_budget' => $request->prjt_budget,
                'job_role' => $request->job_role,
                'responsibilities' => $request->responsibile,
                'image'         => $cover_image,
                'sub_image'    => json_encode($images),
            ];
    
            $project->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Project  updated successfully',
            ], 200);
        } catch (\Exception $er) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error'   => $er->getMessage(),
            ], 500);
        }
    }
    
   
}
