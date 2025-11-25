<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use App\Models\Click;
use App\Models\JobApplied;
use App\Models\JobBoost;
use App\Models\Jobs;
use App\Models\SavedJobs;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class Job_api extends Controller
{
    // function for job created list via api
    public function jobcreated_list_api(Request $request)
    {
        try {

            $today = Carbon::today()->toDateString();
            $jobs = Jobs::with('job_cat:id,value', 'job_loc:id,value')
                ->when(request()->has('user_id'), function ($query) {

                    $query->where('created_by', request('user_id'))
                        ->where('approvalstatus', 'approved');
                }, function ($query) {
                    $query->where('created_by', Auth::id());
                })
                ->latest()
                ->get()
                ->map(function ($job) use ($today) {
                    $job->hasBoost = JobBoost::where('job_id', $job->id)
                        ->where('status', 'active')
                        ->whereDate('from', '<=', $today)
                        ->whereDate('to', '>=', $today)
                        ->exists();

                    return [

                        'id' => $job->id,
                        'title' => $job->title,
                        'job_cat' => $job->job_cat->value ?? null,
                        'job_loc' => $job->job_loc->value ?? null,
                        'job_sublocality' => $job->sublocality ?? null,
                        'salary' => $job->salary,
                        'experience' => $job->experience,
                        'skills' => $job->skills,
                        'job_status' => $job->approvalstatus ?? null,
                        'is_boosted' => $job->hasBoost,
                        'created_at' => $job->created_at,

                    ];
                });

            return response()->json(['status' => 'success',
                'data' => $jobs], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // function for job profile api for admin
    public function job_profile_api(Request $req)
    {
        try {
            $job = Jobs::with('job_cat:id,value', 'job_loc:id,value')->find($req->job_id);

            if (! $job) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Job not found',
                ], 404);
            }

            // Filtered job data
            $base_url = 'https://onstru-social.s3.ap-south-1.amazonaws.com/';
            $today = now()->toDateString();

            $hasBoost = JobBoost::where('job_id', $job->id)
                ->where('status', 'active')
                ->whereDate('from', '<=', $today)
                ->whereDate('to', '>=', $today)
                ->exists();
            $applicants = JobApplied::with('user')
                ->where('job_id', $req->job_id)
                ->get()
                ->map(function ($apply) use ($base_url) {  // <-- pass $base_url here
                    return [
                        'user_id' => $apply->user->id ?? null,
                        'name' => $apply->user->name ?? null,
                        'email' => $apply->user->email ?? null,
                        'location' => $apply->location,
                        'experience' => $apply->experience,
                        'contact' => $apply->user->number ?? null,

                        'resumeUrl' => $apply->resume
                    ? $base_url.$apply->resume
                    : asset('assets/images/NoResume.png'),
                        'profile_image' => $apply->user->profile_img
                        ? $base_url.$apply->user->profile_img
                        : asset('assets/images/NoResume.png'),
                    ];
                });
            $applicantCount = $applicants->count();
            $jobData = [
                'job_id' => $job->id,
                'title' => $job->title,
                'job_cat' => $job->job_cat->value ?? null,
                'experience' => $job->experience,
                'salary' => $job->salary,
                'job_loc' => $job->job_loc->value ?? null,
                'job_sublocality' => $job->sublocality,
                'description' => $job->description,
                'job_type' => $job->shift,
                'skills' => $job->skills,
                'qualification' => $job->qualification,
                'benefit' => $job->benfit,
                'job_approval_status' => $job->approvalstatus ?? null,
                'no_openings' => $job->no_of_openings,
                'created_at' => $job->created_at,
                'remarks' => $job->remarks,
                'is_boosted' => $hasBoost,
                'job_applied_count' => $applicantCount,
                'job_applicants' => $applicants,
                'job_status' => $job->status,
                // 'created_at' => $job->created_at->toDateTimeString(),
            ];

            return response()->json([
                'success' => true,
                'job' => $jobData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // job applicant details for admin view
    public function job_applicant_details(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'job_id' => 'required|exists:job_post,id',
            'user_id' => 'required|exists:user_detail,id'],

            [
                'job_id.required' => 'job id is required.',
                'user_id.required' => 'User id is required.',
            ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $base_url = 'https://onstru-social.s3.ap-south-1.amazonaws.com/';
        $user_data = JobApplied::with(['user.typeOfValue', 'job.job_cat'])
            ->where('job_id', $request->job_id)
            // ->left_join('dropdown_lists',user->type_of,'dropdown_lists.id')
            ->where('created_by', $request->user_id)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                // 'id' => $user_data->id,
                'user_id' => $user_data->user->id,
                'user_name' => $user_data->user->name,
                'user_contact' => $user_data->user->number,
                'you_are' => $user_data->user->you_are,
                'user_type' => $user_data->user->typeOfValue->value,
                'job_category' => $user_data->job->job_cat->value,
                'applied_on' => $user_data->created_at->toDateString() ?? null,
                'experience' => $user_data->experience,
                'user_loction' => $user_data->location,
                'qualification' => $user_data->qualification,
                'current_salary' => $user_data->current_salary,
                'expected_salary' => $user_data->expected_salary,
                'skills' => $user_data->skills,
                'notes' => $user_data->notes,
                'resumeUrl' => $user_data->resume
                ? $base_url.$user_data->resume
                : asset('assets/images/NoResume.png'),

            ],
        ], 200);

    }

    // charge amount
    public function charges(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'charge_type' => 'required',
        ], [
            'charge_type.required' => 'Charge type is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Use switch or if-else to handle different charge types
        if ($request->charge_type == 'job_boost') {
            // $baseCharge = Charge::where('category', 'job_boost')->value('charge');
            $boostCharge = Charge::where('category', 'job_boost')->latest()->value('charge') * 1.18;
            // $taxRate = 0.18;            //18%=18/100=0.18
            // $taxAmount = round($baseCharge * $taxRate, 2); // Rounded to 2 decimals
            // $boostCharge = round($baseCharge + $taxAmount, 2);

            return response()->json([
                'success' => true,
                'charge_type' => 'job_boost',
                'charge' => $boostCharge,
            ], 200);
        } elseif ($request->charge_type == 'service_list') {
            $serviceCharge = Charge::where('category', 'service_list')->value('charge');

            return response()->json([
                'success' => true,
                'charge_type' => 'service_list',
                'charge' => $serviceCharge,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid charge type',
            ], 400);
        }

    }
    // job booste store

    public function job_boost_store(Request $request)
    {
         // Log::info($request->all());
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|exists:job_post,id',
            'from' => 'required',
            'to' => 'required',
        ],

            [
                'job_id.required' => 'job id is required.',
                //    'user_id.required' => 'User id is required.',
            ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $job_day_charge = Charge::where('category', 'job_boost')
        ->where('status', 'active')
        ->latest()
        ->value('charge') * 1.18;

        $from = Carbon::parse($request->from);
        $to = Carbon::parse($request->to);

        if ($from->gt($to)) {
            return back()->withErrors(['to' => 'End date must be after or equal to start date.']);
        }

        $days = $from->diffInDays($to) + 1;
        $total_amount = $job_day_charge * $days;
        // $id=Auth::id();
        // dd($id);

        JobBoost::create([
            'job_id' => $request->job_id,
            'from' => $request->from,
            'to' => $request->to,
            'amount' => $total_amount,
            'day_charge' => $job_day_charge,
            'click' => 0,
            'status' => 'active',
        ]);
        Jobs::where('id', $request->job_id)->update(['highlighted' => 1]);
        UserDetail::where('id', Auth::id() ?? 5)
            ->where('balance', '>=', $total_amount) // ensure balance is sufficient
            ->decrement('balance', $total_amount);

        return response()->json([
            'success' => true,
            'message' => 'data boosted successfuly',
        ], 200);

    }

    // job list-viewer side
    public function job_list_api(Request $request)
    {
        $location = Auth::user()->location ?? 0;

        // dd($location);

        try {
            $today = Carbon::today()->toDateString();
            $jobs = Cache::remember('job_list_api', 2, function () use ($location) {
                return Jobs::with('user.gst', 'job_cat:id,value', 'job_loc:id,value')
                    ->where('approvalstatus', 'approved')
                    // ->latest()
                    ->where('status', 'active')
                    // ->latest()
                    // ->take(200) // 🔥 LIMIT to 200 jobs
                    ->where('created_at', '>=', now()->subMonths(6)) // ✅ last 6 months
                    ->orderByRaw("
                CASE 
                    WHEN highlighted = 1 AND location = '{$location}' THEN 1
                    WHEN highlighted = 1 THEN 2
                    WHEN location = '{$location}' THEN 3
                    ELSE 4
                END, created_at DESC
                    ")
                    ->get()
                    ->map(function ($job) {

                        $hasBoost = ($job->highlighted == 1) ? true : false;

                        $user_job_status = SavedJobs::where('jobs_id', $job->id)
                            ->where('c_by', Auth::id())
                            ->exists();

                        return [
                            'job_id' => $job->id,
                            'title' => $job->title,
                            'legal_name' => $job->user->gst->business_legal ?? $job->user->name,
                            'job_cat' => $job->job_cat->value ?? null,
                            'highlight' => $job->highlighted ?? null,
                            'job_loc' => $job->job_loc->value ?? null,
                            'job_sublocality' => $job->sublocality ?? null,
                            'job_experiece' => $job->experience,
                            'job_salary' => $job->salary ?? null,
                            'job_skills' => $job->skills ?? null,
                            'created_at' => $job->created_at,
                            'save_status' => false,
                            'has_boost' => $hasBoost,
                            'user_job_status' => $user_job_status,
                            'highlighted'=>$job->highlighted
                        ];
                    });
            });

            return response()->json(['success' => true,
                'jobs' => $jobs], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // function for job profile api-view
    public function view_job_profile(Request $req)
    {
        try {
            $job = Jobs::with('job_cat:id,value', 'job_loc:id,value', 'user.gst')->find($req->job_id);

            if (! $job) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Job not found',
                ], 404);
            }
            $userid = Auth::id();
            // dd($userid);
            $applied_status = JobApplied::where('job_id', $req->job_id)
                ->where('created_by', $userid)
                ->first();
            if ($applied_status) {
                $job_apply_staus = true;
            } else {
                $job_apply_staus = false;
            }
                            
            if ($job->highlighted) {
                $boostId = JobBoost::where('job_id', $job->id)
                    // ->where('type', 'click')
                    ->where('status', 'active')
                    ->latest()
                    ->value('id');
                    //dd($boostId);
                $flag = Click::where('category', 'Jobs')
                    ->where('category_id', $job->id)
                    ->where('boost_id', $boostId)
                    ->where('created_by', Auth::id())->exists();
                //dd($flag);
              if (!$flag) {
                $job->increment('click');
                Click::create([
                    'category' => 'Jobs',
                    'category_id' => $job->id,
                    'boost_id' => JobBoost::where('job_id', $job->id)
                        ->where('from', '<=', today())
                        ->where('to', '>=', today())
                        ->value('id'),
                    'status' => 'active',
                    'created_by' => Auth::id() ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
              }
            }

            $recommends = Jobs::with('user:id,name,you_are')
                ->where('category', $job->category)
                ->where('id', '!=', $job->id)
                ->latest()
                ->take(50)
                ->get();

            // Filtered job data
            $base_url = 'https://onstru-social.s3.ap-south-1.amazonaws.com/';
            $today = now()->toDateString();

            //  $hasBoost = JobBoost::where('job_id', $job->id)
            //  ->where('status', 'active')
            //  ->whereDate('from', '<=', $today)
            //  ->whereDate('to', '>=', $today)
            //  ->exists();

            // $recommends = Jobs::with('user:id,name,you_are')
            // ->where('category', $job->category)
            // ->where('id', '!=', $job->id)
            // ->latest()
            // ->take(50)
            // ->get();

            $jobData = [
                'job_id' => $job->id,
                'title' => $job->title,
                'legal_name' => $job->user->gst->business_legal,
                'job_cat' => $job->job_cat->value ?? null,
                'job_loc' => $job->job_loc->value ?? null,
                'experience' => $job->experience,
                'salary' => $job->salary,
                'job_sublocality' => $job->sublocality,
                'job_type' => $job->shift,
                'skills' => $job->skills,
                'description' => $job->description,
                'qualification' => $job->qualification,
                'benefit' => $job->benfit,
                'job_status' => $job->approvalstatus ?? null,
                'no_openings' => $job->no_of_openings,
                'created_at' => $job->created_at,
                'remarks' => $job->remarks,
                'applied_status' => $job_apply_staus,
                'created_by' => $job->created_by,
                //  'is_boosted' => $hasBoost,
                //toDateString(),

                // 'created_at' => $job->created_at->toDateTimeString(),
            ];

            return response()->json([
                'success' => true,
                'job' => $jobData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // function for saved jobs and applied jobs via api
    public function saved_applied_jobs_api(Request $request)
    {
        $userId = Auth::id();

        $savedJobIds = SavedJobs::where('c_by', $userId)->where('status', 'saved')->pluck('jobs_id')->toArray();
        $appliedJobIds = JobApplied::where('created_by', $userId)->pluck('job_id')->toArray();

        $s_jobs = Jobs::with('job_cat:id,value', 'job_loc:id,value')->whereIn('id', $savedJobIds)->get()->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'job_cat' => $job->job_cat->value ?? null,
                'job_loc' => $job->job_loc->value ?? null,
                'created_at' => $job->created_at->toDateTimeString(),
            ];
        });
        $a_jobs = Jobs::with('job_cat:id,value', 'job_loc:id,value')->whereIn('id', $appliedJobIds)->get()->map(function ($jobs) {
            return [
                'id' => $jobs->id,
                'title' => $jobs->title,
                'job_cat' => $jobs->job_cat->value ?? null,
                'job_loc' => $jobs->job_loc->value ?? null,
                'created_at' => $jobs->created_at->toDateTimeString(),
            ];
        });

        return response()->json(['saved_jobs' => $s_jobs, 'applied_jobs' => $a_jobs], 200);
    }

    public function togglejobStatus(Request $request)
    {
        try {
            $job = Jobs::findOrFail($request->job_id);

            // Toggle status
            $job->status = $job->status === 'active' ? 'inactive' : 'active';
            $job->save();

            return response()->json([
                'success' => true,
                'new_status' => $job->status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }   
    }

    public function jobs_status_update(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'job_id' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $job = Jobs::where('id', $req->job_id)
            ->first();

        if (! $job) {
            return response()->json(['error' => 'Job not found'], 404);
        }

        if ($job->status === 'active') {
            $job->status = 'inactive';
            $job->save();

            return response()->json([
                'success' => true,
                'data' => 'Status set to inactive successfully',
                'status' => 'inactive',
            ], 200);
        }

        if ($job->status === 'inactive') {
            $job->status = 'active';
            $job->save();

            return response()->json([
                'success' => true,
                'data' => 'Status set to active successfully',
                'status' => 'active',
            ], 200);
        }

    }

    public function get_sublocation(Request $req)
    {
        $locationId = $req->location_id;

        if (! $locationId) {
            return response()->json([
                'status' => 'error',
                'message' => 'location_id is required',
            ], 400);
        }

        $sublocations = Jobs::where('location', $locationId)
            ->whereNotNull('sublocality')
            ->distinct()
            ->pluck('sublocality');

        if ($sublocations->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No sublocalities found for this location',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'sublocation_list' => $sublocations,
        ], 200);

    }
}
