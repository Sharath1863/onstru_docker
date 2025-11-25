<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Click;
use App\Models\DropdownList;
use App\Models\GstDetails;
use App\Models\JobApplied;
use App\Models\JobBoost;
use App\Models\Jobs;
use App\Models\Notification;
use App\Models\Posts;
use App\Models\SavedJobs;
use App\Models\UserDetail;
use App\Services\Aws;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Job_cnt extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    private function notifyUser($userId, $title, $body, $category_id)
    {
        $this->notificationService->create([
            'category' => 'Job',
            'category_id' => $category_id,
            'reciever' => $userId,
            'title' => $title,
            'body' => $body,
            'status' => 'active',
            'seen' => false,
            'c_by' => Auth::id(),
            'remainder' => null,
        ]);
    }

    public function index(Request $request)
    {
        $locations = Cache::remember('locations_cache', 2, function () {
            return DropdownList::where('dropdown_id', 1)->get();
        });

        $sublocation = Jobs::select('sublocality')
            ->distinct()
            ->get();

        $category = Cache::remember('category_cache', 2, function () {
            return DropdownList::where('dropdown_id', 4)->get();
        });
        $gst = GstDetails::where('user_id', Auth::id())->first();
        $users = UserDetail::where('id', '!=', Auth::id())->take(10)->get();
        $page = $request->input('page', 1);
        $jobsCacheKey = "jobs_page_{$page}";

        $location = Auth::user()->location ?? '';
        $jobs = Cache::remember($jobsCacheKey, 2, function () use ($location) {
            return Jobs::with([
                'user:id,name,you_are',
                'user.gst:id,user_id,business_legal',
            ])
                ->where('created_by', '!=', Auth::id())
                ->where('approvalstatus', 'approved')
                ->where('status', 'active')
                ->where('created_at', '>=', now()->subMonths(6)) // ✅ last 6 months
                ->orderByRaw("
                CASE 
                    WHEN highlighted = 1 AND location = '{$location}' THEN 1
                    WHEN highlighted = 1 THEN 2
                    WHEN location = '{$location}' THEN 3
                    ELSE 4
                END, created_at DESC
                    ")
                ->cursorPaginate(100);
        });

        $next_page_url = $jobs->nextPageUrl();
        $savedJobIds = [];
        if (Auth::check()) {
            $savedJobIds = SavedJobs::where('c_by', Auth::id())->pluck('jobs_id')->toArray();
        }

        if ($request->ajax()) {
            return view('jobs.listing', compact('jobs', 'savedJobIds'))->render();
        }

        return view('jobs.jobs', compact('jobs', 'locations', 'category', 'savedJobIds', 'users', 'gst', 'sublocation', 'next_page_url'));
    }

    // function for job location

    public function job_location(Request $req)
    {
        $sub = Jobs::whereIn('location', $req->loc)->select('sublocality')->distinct()->get();
        return response()->json(['data' => $sub]);
    }

    public function jobs_applied()
    {
        $id = Auth::id();
        $jobs = JobApplied::with(['job', 'user'])->where('created_by', $id)->latest()->paginate(10);
        $locations = DropdownList::where('dropdown_id', 1)->get();
        $category = DropdownList::where('dropdown_id', 4)->get();
        $sublocation = Jobs::select('sublocality')
            ->distinct()
            ->get();

        return view('jobs.applied', compact('jobs', 'locations', 'category', 'sublocation'));
    }

    public function job_post()
    {
        $locations = DropdownList::where('dropdown_id', 1)->get();
        $qualification = DropdownList::where('dropdown_id', 2)->get();
        $category = DropdownList::where('dropdown_id', 4)->get();

        return view('jobs.post', compact('locations', 'qualification', 'category'));
    }

    public function job_edit(Request $request, $id = null)
    {
        if ($request->job_id) {
            $id = $request->job_id;
        }

        $locations = DropdownList::where('dropdown_id', 1)->get();
        $qualification = DropdownList::where('dropdown_id', 2)->get();
        $category = DropdownList::where('dropdown_id', 4)->get();
        $job = Jobs::with('locationRelation')
            ->with('categoryRelation')
            ->where('id', $id)
            ->firstOrFail();

        $data = [
            'job_id' => $job->id,
            'title' => $job->title,
            'skills' => $job->skills,
            'description' => $job->description,
            'qualification' => $job->qualification,
            'job_type' => $job->shift,
            'job_experience' => $job->experience,
            'category' => $job->categoryRelation,
            'salary' => $job->salary,
            'no_of_openings' => $job->no_of_openings,
            'location' => $job->locationRelation,
            'sublocality' => $job->sublocality,
            'benefit' => $job->benfit,
            // 'created_at' => $service->created_at->toDateTimeString(),
        ];
        if ($request->header('Authorization')) {
            return response()->json([
                'message' => 'Service Details',
                'success' => true,
                'data' => $data,

            ], 200);
        }

        return view('jobs.edit', compact('job', 'locations', 'qualification', 'category'));
    }

    public function job_apply_submit()
    {
        return view('jobs.apply_submit');
    }

    public function applied_list($id, Request $request)
    {
        $job = Jobs::with([
            'job_cat:id,value',
            'job_loc:id,value',
            'boosts' => function ($q) {
                $q->where('from', '<=', now())
                    ->where('to', '>=', now());
            },
        ])->find($id);

        $list = JobApplied::with('user:id,name,number,you_are,as_a,type_of,email,profile_img')
            ->where('job_id', $id)->get();

        $isBoosted = $job && $job->boosts->isNotEmpty();
        $sublocation = Jobs::select('sublocality')
            ->distinct()
            ->get();

        if ($request->header('Authorization')) {
            return response()->json([
                'jobs' => $job,
                'list' => $list,
                'is_boosted' => $isBoosted,
            ], 200);
        }

        return view('profile.jobs_applied', compact('list', 'job', 'isBoosted', 'sublocation'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'job_type' => 'required|string',
            'skills' => 'nullable|string',
            'salary' => 'required|string',
            'no_of_openings' => 'required|integer',
            'location' => 'required|string',
            'sublocality' => 'required|string',
            'qualification' => 'nullable|string',
            'category' => 'required|string',
            'benefits' => 'nullable|string',
            'experience' => 'nullable|string',
            'remarks' => 'nullable|string',
        ], [
            'title.required' => 'Job title is required.',
            'job_type.required' => 'Job type is required.',
            'salary.required' => 'Salary information is required.',
            'no_of_openings.required' => 'Number of openings is required.',
            'location.required' => 'Location is required.',
            'category.required' => 'Job category is required.',
            'sublocality.required' => 'Sublocality is required.',
        ]);

        if ($validator->fails()) {
            if ($request->header('Authorization')) {
                return response()->json(['errors' => $validator->errors()], 422);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }
        // Log::info('Auth ID: ' . Auth::id());

        $job = new Jobs;
        $job->title = $request->title;
        $job->description = $request->description;
        $job->shift = $request->job_type;
        $job->skills = $request->skills;
        $job->salary = $request->salary;
        $job->no_of_openings = $request->no_of_openings;
        $job->location = $request->location;
        $job->sublocality = $request->sublocality;
        $job->qualification = $request->qualification;
        $job->category = $request->category;
        $job->benfit = $request->benefits;
        $job->experience = $request->experience;
        $job->created_by = $request->created_by ?? Auth::id() ?? 1;

        $job->approvalstatus = 'pending';
        $job->status = 'active';
        $job->save();

        if ($request->header('Authorization')) {
            return response()->json([
                'message' => 'Job Created Successfully',
                'success' => true,
                'job_id' => $job->id,

            ], 200);
        }

        return redirect()->route('profile')->with('success', 'Job Added Successfully!');
    }

    public function update_job(Request $request, $id = null)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'description' => 'nullable|string',
                'job_type' => 'required|string',
                'skills' => 'nullable|string',
                'salary' => 'required|string',
                'no_of_openings' => 'required|integer',
                'location' => 'required|string',
                'sublocality' => 'required|string',
                'qualification' => 'nullable|string',
                'category' => 'required|string',
                'benefits' => 'nullable|string',
                'experience' => 'nullable|string',
            ], [
                'title.required' => 'Job title is required.',
                'job_type.required' => 'Job type is required.',
                'salary.required' => 'Salary information is required.',
                'no_of_openings.required' => 'Number of openings is required.',
                'location.required' => 'Location is required.',
                'category.required' => 'Job category is required.',
                'sublocality.required' => 'Sublocality is required.',
            ]);


            if ($validator->fails()) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors(),
                    ], 422);
                }

                return redirect()->back()->withErrors($validator)->withInput();
            }
            if ($request->job_id) {
                $id = $request->job_id;
            }
            $job = Jobs::findOrFail($id);


            $job->title = $request->title;
            $job->description = $request->description;
            $job->shift = $request->job_type;
            $job->skills = $request->skills;
            $job->salary = $request->salary;
            $job->no_of_openings = $request->no_of_openings;
            $job->location = $request->location;
            $job->sublocality = $request->sublocality;
            $job->qualification = $request->qualification;
            $job->category = $request->category;
            $job->benfit = $request->benefits;
            $job->experience = $request->experience;
            $job->created_by = Auth::id();
            $job->approvalstatus = 'pending';

            $job->save();
            if ($request->header('Authorization')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Job Updated Successfully',
                    // 'job_id' => $job->id,

                ], 200);
            }

            return redirect()->to('applied-profiles/' . $job->id)->with('success', 'Job Updated Successfully!');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function togglejobStatus(Request $request, $id)
    {
        $job = Jobs::findOrFail($id);

        // Toggle status
        $job->status = $job->status === 'active' ? 'inactive' : 'active';
        $job->save();

        return response()->json([
            'success' => true,
            'new_status' => $job->status,
        ]);
    }


    public function toggleSave(Request $request, $id = null)
    {
        try {
            if ($request->job_id) {
                $id = $request->job_id;
            }
            $userId = Auth::id() ?? 5;
            // dd($userId);

            $saved = SavedJobs::where('jobs_id', $id)->where('c_by', $userId)->first();
            // Log::info('Saved job data:', $saved ? $saved->toArray() : ['empty' => true]); // ✅

            if ($saved) {
                $saved->delete();

                return response()->json([
                    'success' => false,
                    'message' => 'Job removed from saved list',
                    'key' => 'unsaved',
                ]);
            } else {
                SavedJobs::create   ([
                    'jobs_id' => $id,
                    'c_by' => $userId,
                    'status' => 'saved',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Job saved successfully',
                    'key' => 'saved',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function job_details(Request $req, $id = null)
    {

        if ($req->job_id) {
            $id = $req->job_id;
        }

        $job = Jobs::with(['user:id,name,you_are', 'user.gst:id,business_legal', 'categoryRelation:id,value', 'locationRelation:id,value'])->find($id);
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
            // dd($flag);
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
        // if ($job->highlighted) {
        //     $job->increment('click');
        //     Click::create([
        //         'category' => 'Jobs',
        //         'category_id' => $job->id,
        //         'boost_id' => JobBoost::where('job_id', $job->id)
        //             ->where('from', '<=', today())
        //             ->where('to', '>=', today())
        //             ->value('id'),
        //         'status' => 'active',
        //         'created_by' => Auth::id() ?? 0,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);
        // }

        $recommends = Jobs::with('user:id,name,you_are')
            ->where('category', $job->category)
            ->where('created_by', '!=', Auth::id())
            ->where('status', 'active')
            ->where('approvalstatus', 'approved')
            ->where('id', '!=', $job->id)
            ->latest()
            ->take(50)
            ->get();
        if (!$job) {
            abort(404, 'Job not found');
        }
        $isAppled = false;
        if (Auth::check() || $req->header('Authorization')) {
            $isAppled = JobApplied::where('job_id', $id)->where('created_by', Auth::id())->exists();
        }
        if ($req->header('Authorization')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'job' => $job,
                    'isAppled' => $isAppled,
                ]
            ]);
        }

        return view('jobs.details', compact('job', 'recommends', 'isAppled'));
    }

    public function applyForm($id)
    {
        $job = Jobs::findOrFail($id);
        $location = DropdownList::where('dropdown_id', 1)->get();

        return view('jobs.apply', compact('job', 'location'));
    }

    public function apply(Request $request, Aws $aws, $id = null)
    {
        // Log::info('Request Data:', $request->all());
        if ($request->job_id) {
            $id = $request->job_id;
        }   
        try {
            $validator = Validator::make($request->all(), [
                'skills' => 'required|string|max:255',
                'qualification' => 'required|string|max:255',
                'experience' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'current_salary' => 'required|string|max:255',
                'expected_salary' => 'required|string|max:255',
                'notes' => 'nullable|string|max:500',
                'resume' => 'required'
            ], [
                'skills.required' => 'Skills is required.',
                'qualification.required' => 'Qualification is required.',
                'experience.required' => 'Experience is required.',
                'location.required' => 'Location is required.',
                'current_salary.required' => 'Current salary is required.',
                'expected_salary.required' => 'Expected salary is required.',
                'resume.required' => 'Resume is required.',
            ]);

            if ($validator->fails()) {
                if ($request->header('Authorization')) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                return redirect()->back()->withErrors($validator)->withInput();
            }
            // $id=$request->job_id;
            $job = Jobs::findOrfail($id);

            $application = new JobApplied;
            $application->created_by = auth::id() ?? 2;
            $application->job_id = $id ?? 0;
            $application->skills = $request->skills;
            $application->qualification = $request->qualification;
            $application->experience = $request->experience;
            $application->location = $request->location;
            $application->current_salary = $request->current_salary;
            $application->expected_salary = $request->expected_salary;
            $application->notes = $request->notes;
            $application->status = 'active';
            $application->created_at = now();
            $application->updated_at = now();

            if ($request->hasFile('resume')) {
                $file = $request->file('resume');
                if (!is_array($file)) {
                    $file = [$file];
                }
                // $application->resume = Arr::wrap($file);
                $folder = 'resumes';
                $s3Result = $aws->common_upload_to_s3($file, $folder);
                $s3Key = is_array($s3Result) ? $s3Result[0] : $s3Result;
                $application->resume = $s3Key;
            } else {
                $application->resume = '';
            }

            $application->save();


            $this->notifyUser($job->created_by, 'Job Applied', Auth::user()->name . ' applied to your job "' . $job->title . '"', $job->id);

            if ($job->user && ($job->user->web_token || $job->user->mob_token)) {
                // Log::info("Inside notification block");
                // Log::info('[NOTIFY] Sending notification to user', [
                //     'user_id' => $job->user->id,
                //     'mob_token' => $job->user->mob_token,
                //     'web_token' => $job->user->web_token
                // ]);

                $data = [

                    'web_token' => $job->user->web_token,
                    'mob_token' => $job->user->mob_token ?? null,
                    'title' => 'Job Applied ' . ucfirst($request->status),
                    'body' => Auth::user()->name . ' has been applied your "' . $job->title . '" job.',
                    'id' => $job->id,
                    'link' => route('job.details', ['id' => $job->id]),
                ];
                $this->notificationService->token($data);
                // Log::info("Sending notification to user ID: " . $job->user->mob_token);
            }

            if ($request->header('Authorization')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Application Submitted Successfully',
                    'job_id' => $application->job_id
                ], 200);
            }

            // if ($request->header('Authorization')) {
            //     return response()->json(['message' => 'Job Created Successfully',
            //     'success' => true,
            //     // 'job_id' => $job->id,

            // ], 200);
            // }
            // if ($request->header('Authorization')) {
            //     return response()->json(['message' => 'Job Created Successfully',
            //     'success' => true,
            //     // 'job_id' => $job->id,

            // ], 200);
            // }

            return redirect()->route('jobs.apply_submit')->with('success', 'Application Submitted Successfully!')
                ->with('jobTitle', $job->title);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function downloadResume($id)
    {
        $application = JobApplied::findOrFail($id);

        if ($application->resume && Storage::disk('s3')->exists($application->resume)) {
            // Force download from S3
            // return Storage::disk('s3')->download($application->resume);
        }

        return back()->with('error', 'Resume file not found.');
    }

    // function for saved jobs and applied jobs via api
    public function saved_applied_jobs_api(Request $request)
    {
        $userId = Auth::id() ?? 0;

        //  dd($userId);

        $savedJobIds = SavedJobs::where('c_by', $userId)->where('status', 'saved')->pluck('jobs_id')->toArray();
        $appliedJobIds = JobApplied::where('created_by', $userId)->pluck('job_id')->toArray();

        $s_jobs = Jobs::with('job_cat:id,value', 'job_loc:id,value')->whereIn('id', $savedJobIds)
        ->where('status', 'active')
        ->where('approvalstatus', 'approved')
        ->orderBy('id', 'desc')
         ->get()->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'job_cat' => $job->job_cat->value ?? null,
                'job_loc' => $job->job_loc->value ?? null,
                'exp' => $job->experience ?? null,
                'salary' => $job->salary ?? null,
                'sublocality' => $job->sublocality ?? null,
                'description' => $job->description ?? null,
                'legal_name' => $job->user->gst->business_legal ? $job->user->gst->business_legal : $job->user->name ?? null,
                'created_at' => $job->created_at->toDateTimeString(),

            ];
        });
        $a_jobs = Jobs::with('job_cat:id,value', 'job_loc:id,value')->whereIn('id', $appliedJobIds)->orderBy('id', 'desc')->get()->map(function ($jobs) {
            return [
                'id' => $jobs->id,
                'title' => $jobs->title,
                'job_cat' => $jobs->job_cat->value ?? null,
                'job_loc' => $jobs->job_loc->value ?? null,
                'exp' => $jobs->experience ?? null,
                'salary' => $jobs->salary ?? null,
                'sublocality' => $jobs->sublocality ?? null,
                'description' => $jobs->description ?? null,
                'legal_name' => $jobs->user->gst->business_legal ? $jobs->user->gst->business_legal : $jobs->user->name ?? null,
                'created_at' => $jobs->created_at->toDateTimeString(),
            ];
        });

        return response()->json(['success' => true, 'saved_jobs' => $s_jobs, 'applied_jobs' => $a_jobs], 200);
    }

    // function for job list api

    public function job_list_api(Request $request)
    {
        $jobs = Cache::remember('job_list_api', 120, function () {
            return Jobs::with('user:id,name,you_are', 'job_cat:id,value', 'job_loc:id,value')
                ->where('status', 'approved')
                ->latest()
                ->take(200) // 🔥 LIMIT to 200 jobs
                ->get()
                ->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'job_cat' => $job->job_cat->value ?? null,
                        'job_loc' => $job->job_loc->value ?? null,
                        'job_sublocality' => $job->sublocality,
                        'created_at' => $job->created_at->toDateTimeString(),
                    ];
                });
        });

        return response()->json(['jobs' => $jobs], 200);
    }

    public function boost_job(Request $request)
    {
        $request->validate([
            'job_id' => 'required',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
            'jobvideo' => [
                'nullable',
                'file',
                function ($attribute, $value, $fail) use ($request) {
                    $file = $request->file('jobvideo'); // ✅ get the UploadedFile object

                    if (!$file || !$file->isValid()) {
                        $fail('The uploaded file is not valid.');

                        return;
                    }

                    $allowedImages = ['jpg', 'jpeg', 'png', 'webp'];
                    $allowedVideos = ['mp4', 'mov', 'avi', 'mkv'];
                    $extension = strtolower($file->getClientOriginalExtension());

                    if (!in_array($extension, array_merge($allowedImages, $allowedVideos))) {
                        $fail("The $attribute must be a valid image or video file.");
                    }
                },
            ],
        ]);

            $job_day_charge = Charge::where('category', 'job_boost')
            ->where('status', 'active')
            ->latest()
            ->value('charge') * 1.18;
        
        //dd($job_day_charge);

        $from = Carbon::parse($request->from);
        $to = Carbon::parse($request->to);

        if ($from->gt($to)) {
            return back()->withErrors(['to' => 'End date must be after or equal to start date.']);
        }

        $days = $from->diffInDays($to) + 1;
        $total_amount = $job_day_charge * $days;

        JobBoost::create([
            'job_id' => $request->job_id,
            'from' => $request->from,
            'to' => $request->to,
            'amount' => $total_amount,
            'day_charge' => $job_day_charge,
            'click' => 0,
            'status' => 'active',
        ]);

        if ($request->hasFile('jobvideo')) {
            $file = $request->file('jobvideo');
            $folder = 'job_videos';
            $aws = new Aws;
            $s3Result = $aws->common_upload_to_s3([$file], $folder);
            $s3Key = is_array($s3Result) ? $s3Result : [$s3Result];

            // Jobs::where('id', $request->job_id)->update(['video' => $s3Key]);
            Posts::create([
                'category' => 'job',
                'category_id' => $request->job_id,
                'file' => $s3Key,
                'caption' => 'Check out my boosted job!',
                'location' => null,
                'created_by' => Auth::id(),
                'status' => 'active',
            ]);
        }

        if ($from->isSameDay(today())) {
            Jobs::where('id', $request->job_id)->update(['highlighted' => 1]);
        }
        UserDetail::where('id', Auth::id())
            ->where('balance', '>=', $total_amount) // ensure balance is sufficient
            ->decrement('balance', $total_amount);

        // dd($s3Key);
        return redirect()->back()->with('success', 'Job Boosted Successfully!');
    }
}
