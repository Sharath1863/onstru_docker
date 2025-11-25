<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use Illuminate\Support\Facades\Storage;
use App\Models\Project;
use App\Models\DropdownList;
use App\Models\UserDetail;
use App\Services\Aws;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Project_cnt extends Controller
{

    // function for project count
    public function project_count()
    {
        $user = Auth::user();
        $projectCount = Project::where('created_by', $user->id ?? 1)->count();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

         if ($projectCount >= 5) {

            $listingCharge = Charge::where('category', 'project_list')->latest()->value('charge') * 1.18;
        }
        return response()->json([
            'success' => true,
            'project_count' => $projectCount,
            'user_balance' => $user->balance ?? 0,
            'listing_charge' => $listingCharge ?? 0,
        ], 200);
    }
    public function deleteProject($id)
    {
        try {
            $project = Project::where('id', $id)->where('created_by', Auth::id())->firstOrFail();

            // Delete image if exists
            if ($project->image && file_exists(public_path($project->image))) {
                unlink(public_path($project->image));
            }

            $project->delete();

            return redirect()->back()->with('success', 'Project Deleted Successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete project: ' . $e->getMessage());
        }
    }

    // Store Project
    public function storeProject(Request $request, Aws $aws)
    {
        try {
            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'location' => 'required|exists:dropdown_lists,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'key_outcomes' => 'required|string',
                'prjt_budget' => 'required|string|max:255',
                'job_role' => 'required|string|max:255',
                'responsibilities' => 'required|string',
            ];

            foreach (['image-1', 'image-2', 'image-3', 'image-4'] as $field) {
                $rules[$field] = [
                    $field === 'image-1' ? 'required' : 'nullable',
                    'file',
                    // 'max:5120', // 5MB limit
                ];
            }

            $request->validate($rules);

            $user = Auth::user();
            $projectCount = Project::where('created_by', $user->id)->count();
            $listingCharge = 0;

            if ($projectCount >= 5) {
                $listingCharge = Charge::where('category', 'project_list')->latest()->value('charge') * 1.18;

                if ($user->balance < $listingCharge) {

                    if ($request->header('Authorization')) {
                        return response()->json([
                            'message' => 'Insufficient balance to add more projects',
                            'success' => true
                        ], 200);
                    }
                    return back()->withErrors([
                        'projectPay' => 'Insufficient balance to add more projects.'
                    ])->withInput();
                }

                UserDetail::where('id', Auth::id() ?? 1)
                    ->where('balance', '>=', $listingCharge)
                    ->decrement('balance', $listingCharge);
            }

            $folder = 'project_images';
            $s3Keys = [];
            // if ($request->hasFile('image-1')) {
            //     $file = $request->file('image-1');
            //     if (!is_array($file)) {
            //         $file = [$file];
            //     }
            //     $s3Result = $aws->common_upload_to_s3($file, $folder);
            //     $s3Key = is_array($s3Result) ? $s3Result[0] : $s3Result;
            // }

            foreach (['image-1', 'image-2', 'image-3', 'image-4'] as $field) {
                if ($request->hasFile($field)) {
                    $files = $request->file($field);

                    if (!is_array($files)) {
                        $files = [$files];
                    }
                    $s3Results = $aws->common_upload_to_s3($files, $folder);
                    $s3Keys[] = is_array($s3Results) ? $s3Results[0] : $s3Results;
                }
            }

            Project::create([
                'created_by' => Auth::id() ?? 1,
                'title' => $request->title,
                'description' => $request->description,
                'location' => $request->location,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'key_outcomes' => $request->key_outcomes,
                'prjt_budget' => $request->prjt_budget,
                'job_role' => $request->job_role,
                'responsibilities' => $request->responsibilities,
                'image' => $s3Keys[0] ?? null,
                'sub_image' => json_encode($s3Keys),
                'amount' => $listingCharge,
                'status' => 'active',
            ]);

            $newProjectCount = Project::where('created_by', Auth::id() ?? 1)->count();

            $badge = null;
            if ($newProjectCount == 6) {
                $badge = '5P';
            } elseif ($newProjectCount == 11) {
                $badge = '10P';
            } elseif ($newProjectCount == 16) {
                $badge = '15P';
            }

            if ($badge) {
                UserDetail::where('id', $user->id)
                    ->whereIn('as_a', ['Contractor', 'Consultant'])
                    ->update(['badge' => $badge]);
            }

            if ($request->header('Authorization')) {
                return response()->json([
                    'message' => 'Project Created Successfully',
                    'success' => true
                ], 200);
            }

            return redirect()->route('profile')->with('success', 'Project Added Successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->header('Authorization')) {
                return response()->json([
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }

    // Update Project
    public function updateProject(Request $request, $id, Aws $aws)
    {
        try {
            $project = Project::where('id', $id)
                ->where('created_by', Auth::id())
                ->firstOrFail();

            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'location' => 'required|exists:dropdown_lists,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'key_outcomes' => 'required|string',
                'prjt_budget' => 'required|string|max:255',
                'job_role' => 'required|string|max:255',
                'responsibilities' => 'required|string',
            ];

            foreach (['image-1', 'image-2', 'image-3', 'image-4'] as $field) {
                $rules[$field] = ['nullable', 'file'];
            }

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

            $s3Keys = json_decode($project->sub_image ?? '[]', true); // Existing images
            $oldMainImage = $project->image ?? null;
            $folder = 'project_images';
            $fields = ['image-1', 'image-2', 'image-3', 'image-4'];

            for ($i = 0; $i < count($fields); $i++) {
                $field = $fields[$i];
                if ($request->hasFile($field)) {
                    // Delete old image if exists
                    if (!empty($s3Keys[$i])) {
                        Storage::disk('s3')->delete($s3Keys[$i]);
                    }
                    $uploaded = $aws->common_upload_to_s3([$request->file($field)], $folder);
                    $s3Keys[$i] = $uploaded[0];
                }
            }

            $data['image'] = $s3Keys[0] ?? $oldMainImage;
            $data['sub_image'] = json_encode($s3Keys);
            $project->update($data);

            return redirect()->back()->with('success', 'Project Updated Successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update project: ' . $e->getMessage());
        }
    }
    


   
}
