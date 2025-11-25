<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\ReadyToWork;
use App\Models\UserDetail;
use App\Models\Charge;
use Illuminate\Support\Facades\DB;
use App\Models\DropdownList;

class Ready_work_api extends Controller
{
    // ready to work functions will be here

    public function add_ready_to_work(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'job_type' => 'required|array',
                'job_type.*' => 'string|max:255',
                'work_type' => 'required|array',
                'work_type.*' => 'string|max:255',
                'location' => 'required|array',
                'location.*' => 'string|max:255',
                'exp' => 'required|numeric',
                'payment' => 'required|numeric',
                'resume' => 'nullable|mimes:doc,docx,pdf|max:10240', // max 10MB
                // Add other necessary validations
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $path = $file->store('resumes', 'public'); // Store in 'resumes' directory in 'public' disk
        } else {
            $path = null;
        }

        try {
            $ready_work = ReadyToWork::create([
                'job_titles' => $request->job_type,
                'work_types' => $request->work_type,
                'locations' => $request->location,
                'experience' => $request->exp,
                'payments' => $request->payment,
                'resume_path' => $path ? $path : null,
                'created_by' => Auth::id(),
            ]);
            // Logic to add ready to work entry
            return response()->json(['message' => 'Ready to work entry added successfully'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to upload resume', 'messsage' => $e->getMessage()], 500);
        }
    }

    // function to get created ready to work entries
    public function ready_to_work_list_api(Request $request)
    {
        $ready_works = ReadyToWork::with('user:id,name')->latest()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'job_titles' => $item->job_titles,
                'job_titles_text' => $item->job_titles_text,
                'work_types' => $item->work_types,
                'work_types_text' => $item->work_types_text,
                'locations' => $item->locations,
                'locations_text' => $item->locations_text,
                'experience' => $item->experience,
                'resume_path' => $item->resume_path ? asset('storage/' . $item->resume_path) : null,
                'status' => $item->status,
                // 'created_by' => $item->user ? $item->user->name : null,
                // 'created_at' => $item->created_at ? $item->created_at->format('d-m-Y') : null,
            ];
        });

        return response()->json(['data' => $ready_works], 200);
    }

    // function for project count
    public function readytowork_charge()
    {
       
        $user_details =UserDetail::where('id', Auth::id())->first();
        // $ready_work_status =ReadyToWork::where('created_by', Auth::id())->first();
        $ready_work_category = DB::table('dropdown_lists')
            ->where('dropdown_id', 4)
            ->select('id', 'value')
            ->get();

        // if( $ready_work_status){
        //     $ready_work_status=$ready_work_status->status;
        // }

        if (!$user_details) {
            return response()->json(['error' => 'User not found'], 404);
        }

            $listingCharge = Charge::where('category', 'ready_to_work')->latest()->value('charge');
            $readyCharge = round($listingCharge * 1.18, 1); // round to 1 decimal place

        return response()->json([
            'success' => true,
            'user_balance' => $user_details->balance ?? 0,
            'work_charge' => $readyCharge ?? 0,
            // 'ready_work_status' =>$ready_work_status??null,
            'ready_work_tilte'=> $ready_work_category 
        ], 200);
    }
    public function getreadywork(Request $request)
   {
        $id = Auth::id();
        if (!$id) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $readyToWork = ReadyToWork::where('created_by', $id)->first();

        if ($readyToWork) {
          
            $locationIds = is_array($readyToWork->locations) 
                ? $readyToWork->locations 
                : json_decode($readyToWork->locations, true);

            
            $locationDetails = DropdownList::whereIn('id', $locationIds)->get();
            $readyToWork->location_details = $locationDetails;
        }

        return response()->json([
            'success' => true,
            'ready to work details' => $readyToWork,
        ], 200);
   }
}
