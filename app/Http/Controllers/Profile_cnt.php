<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Charge;
use App\Models\DropdownList;
use App\Models\GstDetails;
use App\Models\Jobs;
use App\Models\Lead;
use App\Models\Notification;
use App\Models\OrderProducts;
use App\Models\Posts;
use App\Models\Products;
use App\Models\Project;
use App\Models\ReadyToWork;
use App\Models\ReadyToWorkBoost;
use App\Models\Report;
use App\Models\Service;
use App\Models\UserDetail;
use App\Models\UserProfile;
use App\Services\Aws;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Profile_cnt extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function profile(Request $request)
    {

        $id = Auth::id();
        $readyToWork = ReadyToWork::where('created_by', $id)->first();

        $readyToWork_count = ReadyToWork::where('created_by', $id)->count();

        if ($request->header('Authorization')) {
            return response()->json(
                [
                    'message' => 'ready to work details',
                    'success' => true,
                    'data' => $readyToWork,
                ],
                200
            );
        }
        $jobs = Jobs::with('boosts')->where('created_by', $id)->latest()->get();
        $products = Products::where('created_by', $id)
            ->withCount('reviews')
            ->withAvg('reviews', 'stars')
            ->latest()
            ->get();
        $services = Service::where('created_by', $id)
            ->withCount('reviews')
            ->withAvg('reviews', 'stars')
            ->latest()
            ->get();
        $projects = Project::with('locationDetails')->where('created_by', $id)->latest()->get();
        $leads = Lead::where('created_by', $id)
            ->withCount('reviews')
            ->withAvg('reviews', 'stars')
            ->latest()
            ->get();
        $post_count = Posts::where('created_by', Auth::id())->where('status', 'active')->count();

        $serviceTypes = DropdownList::where('dropdown_id', 5)->pluck('value', 'id');
        $locations = DropdownList::where('dropdown_id', 1)->pluck('value', 'id');
        $jobTitles = DropdownList::where('dropdown_id', 4)->pluck('value', 'id');

        $posts = Posts::with(['likedByAuth', 'post_save', 'post_report'])->where('created_by', Auth::id())
            ->where('status', 'active')
            ->latest()
            ->get();

        $posts->transform(function ($pt) {
            $pt->is_liked = $pt->likedByAuth !== null;
            $pt->is_saved = $pt->post_save !== null;
            $pt->is_reported = $pt->post_report !== null;

            return $pt;
        });

        $following = Auth::user()->following()->latest('follows.created_at')->paginate(20);
        $followers = Auth::user()->followers()->latest('follows.created_at')->paginate(20);

        $job_charge = Charge::where('category', 'job_boost')->latest()->value('charge') * 1.18;
        $service_list_pay = Charge::where('category', 'service_list')->latest()->value('charge') * 1.18;
        $service_click_charge = Charge::where('category', 'service_highlight')->latest()->value('charge') * 1.18;
        $product_click_charge = Charge::where('category', 'product_highlight')->latest()->value('charge') * 1.18;
        $project_list_charge = Charge::where('category', 'project_list')->latest()->value('charge') * 1.18;
        $readyto_work_charge = Charge::where('category', 'ready_to_work')->latest()->value('charge') * 1.18;
        $users = UserDetail::where('id', '!=', Auth::id())->take(10)->get();
        $gstverified = GstDetails::where('user_id', Auth::id())->where('gst_verify', 'yes')->first();
        if ($gstverified === null) {
            $gstverified = 'no';
        } else {
            $gstverified = 'yes';
        }

        return view(
            'profile.index',
            compact(
                'jobs',
                'products',
                'services',
                'projects',
                'leads',
                'readyToWork',
                'readyToWork_count',
                'serviceTypes',
                'locations',
                'jobTitles',
                'following',
                'followers',
                'posts',
                'post_count',
                'job_charge',
                'service_list_pay',
                'service_click_charge',
                'product_click_charge',
                'project_list_charge',
                'readyto_work_charge',
                'users',
                'gstverified',

            )
        );
    }

    public function storeReadyToWork(Request $request, Aws $aws)
    {
        // dd($request->all());
        try {
            $request->validate([
                'job_titles' => 'required|array|min:1',
                'work_types' => 'required|array|min:1',
                'locations' => 'required|array|min:1',
                'experience' => 'required|string',
                'days' => 'required',
                'resume' => 'required',
            ]);
            // dd($request->all());
            // dd($request->locations);
            // dd($request->locations);
            $userId = Auth::id() ?? 3;

            // Check if user already has ready to work entry
            $existing = ReadyToWork::where('created_by', $userId)->first();

            // if ($request->header('Authorization')) {
            //     return response()->json(['message' => 'You already have a Ready to Work entry. Please edit or delete the existing one.'], 201);
            // }

            // return back()->with('error', 'You already have a Ready to Work entry. Please edit or delete the existing one.');

            if ($request->hasFile('resume')) {
                $file = $request->file('resume');
                if (! is_array($file)) {
                    $file = [$file];
                }
                $folder = 'ready_resume';
                $s3Result = $aws->common_upload_to_s3($file, $folder);
                $s3Key = is_array($s3Result) ? $s3Result[0] : $s3Result;
            }
            $readyto_work_charge = Charge::where('category', 'ready_to_work')->latest()->value('charge') * 1.18;
            $total = $request->days * $readyto_work_charge;

            $expriesAt = \Carbon\Carbon::now()->addDays((int) $request->days)->toDateString();

            if ($existing) {

                ReadyToWork::where('created_by', $userId)->update([
                    'job_titles' => $request->job_titles,
                    'work_types' => $request->work_types,
                    'locations' => $request->locations,
                    'experience' => $request->experience,
                    'days' => $request->days,
                    'amount' => $readyto_work_charge,
                    'resume_path' => $s3Key ?? $existing->resume_path,
                    'expiry' => $expriesAt,
                    'status' => 'active',
                ]);

            } else {
                ReadyToWork::create([
                    'created_by' => $userId,
                    'job_titles' => $request->job_titles,
                    'work_types' => $request->work_types,
                    'locations' => $request->locations,
                    'experience' => $request->experience,
                    'days' => $request->days,
                    'amount' => $readyto_work_charge,
                    'resume_path' => $s3Key,
                    'expiry' => $expriesAt,
                    'status' => 'active',
                ]);
            }
            UserDetail::where('id', Auth::id() ?? 3)
                ->where('balance', '>=', $total) // ensure balance is sufficient
                ->decrement('balance', $total);
            ReadyToWorkBoost::create([
                'user_id' => $userId,
                'days' => $request->days,
                'amount' => $total,
                'click' => 0,
            ]);

            if ($request->header('Authorization')) {
                if ($existing) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Ready to Work details  updated successfully!',
                    ], 200);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Ready to Work details added successfully!',
                ], 200);
            }

            return back()->with('success', 'Ready to Work details Added Successfully!');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateReadyToWork(Request $request, Aws $aws, $id = null)
    {
        if ($request->ready_id) {
            $id = $request->ready_id;
        }
        $request->validate([
            'job_titles' => 'required|array|min:1',
            'work_types' => 'required|array|min:1',
            'locations' => 'required|array|min:1',
            'experience' => 'required|string',
            // 'payments' => 'required|string|min:1',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB max
        ]);

        $readyToWork = ReadyToWork::where('id', $id)->where('created_by', Auth::id())->firstOrFail();

        $data = [
            'job_titles' => $request->job_titles,
            'work_types' => $request->work_types,
            'locations' => $request->locations,
            'experience' => $request->experience,
            // 'payments' => $request->payments,
        ];

        $resume_path = $readyToWork->resume_path;
        if ($request->hasFile('resume')) {

            if (! empty($resume_path)) {
                Storage::disk('s3')->delete($resume_path);
            }

            $file = $request->file('resume');
            if (! is_array($file)) {
                $file = [$file];
            }
            $folder = 'ready_resume';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $s3Key = is_array($s3Result) ? $s3Result[0] : $s3Result;
            $data['resume_path'] = $s3Key;
            $data['resume_path'] = $s3Key;
        }

        // if ($request->hasFile('resume')) {
        //     // Delete old resume if exists
        //     if ($readyToWork->resume_path && file_exists(public_path($readyToWork->resume_path))) {
        //         unlink(public_path($readyToWork->resume_path));
        //     }

        //     $resume = $request->file('resume');
        //     $resumeName = time() . '_' . $resume->getClientOriginalName();
        //     $resume->move(public_path('hire_resume'), $resumeName);

        //     $data['resume_path'] = 'hire_resume/' . $resumeName;
        // }

        $readyToWork->update($data);
        if ($request->header('Authorization')) {
            return response()->json([
                'success' => true,
                'message' => 'Ready to Work details updated successfully!',

            ]);
        }

        return back()->with('success', 'Ready to Work Details Updated Successfully!');
    }

    public function toggleReadyToWorkStatus(Request $request, $id = null)
    {
        if ($request->ready_id) {
            $id = $request->ready_id;

        }
        $readyToWork = ReadyToWork::where('id', $id)->where('created_by', Auth::id())->firstOrFail();

        $newStatus = $readyToWork->status === 'active' ? 'inactive' : 'active';
        $readyToWork->update(['status' => $newStatus]);

        $message = $newStatus === 'active' ? 'Ready to Work activated successfully!' : 'Ready to Work deactivated successfully!';
        if ($request->header('Authorization')) {
            return response()->json([
                'success' => true,
                'message' => $message,

            ]);
        }

        return back()->with('success', $message);
    }

    public function deleteReadyToWork($id)
    {
        $readyToWork = ReadyToWork::where('id', $id)->where('created_by', Auth::id())->firstOrFail();

        // Delete resume file if exists
        if ($readyToWork->resume_path && file_exists(public_path($readyToWork->resume_path))) {
            unlink(public_path($readyToWork->resume_path));
        }

        $readyToWork->delete();

        return back()->with('success', 'Ready to Work Details Deleted Successfully!');
    }

    public function userProfile($id, $post_id = null, $type = null)
    {
        $open_post = $post_id;
        $users = UserDetail::where('id', '!=', Auth::id())->take(10)->get();
        $user = UserDetail::withCount(['followers', 'following'])->findOrFail($id);

        $jobs = Jobs::where('created_by', $id)->get();
        $products = Products::where('created_by', $id)
            ->withCount('reviews')
            ->withAvg('reviews', 'stars')
            ->latest()
            ->get();
        $services = Service::where('created_by', $id)
            ->withCount('reviews')
            ->withAvg('reviews', 'stars')
            ->latest()
            ->get();
        $projects = Project::with('locationDetails')->where('created_by', $id)->get();
        $posts = Posts::where('created_by', $id)
            ->where('status', 'active')
            ->latest()
            ->get();
        $post_count = Posts::where('created_by', $id)->where('status', 'active')->count();
        // Get dropdown options
        $serviceTypes = DropdownList::where('dropdown_id', 5)->pluck('value', 'id');
        $locations = DropdownList::where('dropdown_id', 1)->pluck('value', 'id');
        $gstverified = GstDetails::where('user_id', Auth::id())->where('gst_verify', 'yes')->first();
        if ($gstverified === null) {
            $gstverified = 'no';
        } else {
            $gstverified = 'yes';
        }
        $following = $user->following()->latest('follows.created_at')->paginate(20);
        $followers = $user->followers()->latest('follows.created_at')->paginate(20);

        $reports = Report::where('user_id', Auth::id())->where('f_id', $id)->exists();

        return view('users.index', compact('jobs', 'products', 'services', 'projects', 'serviceTypes', 'locations', 'user', 'reports', 'following', 'followers', 'users', 'posts', 'post_count', 'open_post', 'gstverified'));
    }

    public function follow(Request $request, UserDetail $user)
    {
        // Log::info($request->all());
        $me = $request->user() ?? UserDetail::find(Auth::id() ?? 2);
        if (! $me) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Prevent self-follow (only if user is authenticated, not fallback)
        if ($me->id === $user->id) {
            return response()->json(['error' => 'You cannot follow yourself'], 422);
        }

        // Safe against duplicates even under race conditions
        $me->following()->syncWithoutDetaching([$user->getKey()]);

        $this->notificationService->create([
            'category' => 'user',
            'category_id' => $me->id,
            'reciever' => $user->id, // assuming 'reciever' is a user ID
            'title' => 'New Follower!',
            'body' => 'started following you.',
            'status' => 'active',
            'c_by' => $me->id,
            'remainder' => null,
        ]);
        if ($user && ($user->web_token || $user->mob_token)) {
            // Log::info("Inside notification block");
            // Log::info('[NOTIFY] Sending notification to user', [
            //     'user_id' => $job->user->id,
            //     'mob_token' => $job->user->mob_token,
            //     'web_token' => $job->user->web_token
            // ]);[YourUsername] started following you.

            $data = [

                'web_token' => $user->web_token,
                'mob_token' => $user->mob_token ?? null,
                'title' => 'New Follower! ',
                'body' => 'started following you.',
                'id' => $me->id,
                // 'link' => route('job.details', ['id' => $job->id]),
            ];

            $this->notificationService->token($data);
            // Log::info("Sending notification to user ID: " . $job->user->mob_token);

        }

        return response()->json([
            'success' => true,
            'status' => 'followed',
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->following()->count(),
            'my_followers_count' => $me->followers()->count(),
            'my_following_count' => $me->following()->count(),
        ]);
    }

    public function unfollow(Request $request, UserDetail $user)
    {

        $me = $request->user() ?? UserDetail::find(Auth::id() ?? 2);

        if (! $me) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $me->following()->detach($user->getKey());

        return response()->json([
            'success' => true,
            'status' => 'unfollowed',
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->following()->count(),
            'my_followers_count' => $me->followers()->count(),
            'my_following_count' => $me->following()->count(),
        ]);
    }

    public function update_profile_image(Request $request, Aws $aws)
    {
        // Log::info($request->all());
        $request->validate([
            'profile_img' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($request->hasFile('profile_img')) {
            $file = $request->file('profile_img');
            if (! is_array($file)) {
                $file = [$file];
            }
            $folder = 'profile_images';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $s3Key = is_array($s3Result) ? $s3Result[0] : $s3Result;
            $user = UserDetail::where('id', Auth::id())->first();

            if ($user) {

                $user->profile_img = $s3Key;
                $user->save();
                if ($request->header('Authorization')) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Profile Image Updated Successfully.',
                        'user_profile' => $user->profile_img,
                    ], 200);
                }

                return redirect()->route('edit-profile')->with('success', 'Profile Image Updated Successfully!');
            } else {
                if ($request->header('Authorization')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found.',
                    ], 404);
                }

                return response()->json(['error' => 'User not found'], 404);
            }

        }

    }

    public function myProfile(Request $request)
    {
        $user_basic_details = UserDetail::with('user_location')->where('id', Auth::id())->first();

        if ($user_basic_details) {
            $typeIds = is_array($user_basic_details->type_of)
                ? $user_basic_details->type_of
                : explode(',', $user_basic_details->type_of);

            $typeDetails = DropdownList::whereIn('id', $typeIds)->get();
            $user_basic_details->type_of_details = $typeDetails;
        }
        $profile = UserProfile::with('projectCatRelation', 'purposeRelation', 'designationRelation')->where('c_by', Auth::id())->first();
        $locations = DropdownList::where('dropdown_id', 1)->get();
        $gst = GstDetails::where('user_id', Auth::id())->first();
        $project_category = Cache::remember('project_category_cache', 2, function () {
            return DropdownList::where('dropdown_id', 10)->get();
        });
        $your_purpose = Cache::remember('your_purpose_cache', 2, function () {
            return DropdownList::where('dropdown_id', 11)->get();
        });
        $services_offered = Cache::remember('services_offered_cache', 2, function () {
            return DropdownList::where('dropdown_id', 12)->get();
        });
        $designations = Cache::remember('designation_cache', 2, function () {
            return DropdownList::where('dropdown_id', 13)->get();
        });

        $gstverified = GstDetails::where('user_id', Auth::id())->where('gst_verify', 'yes')->first();
        if ($gstverified === null) {
            $gstverified = 'no';
        } else {
            $gstverified = 'yes';
        }
        $profileParts = [];
        $users_details = UserDetail::select('id', 'name', 'user_name', 'gender', 'bio', 'email', 'location')
            ->where('id', Auth::id())
            ->first();
        $fields = ['name', 'user_name', 'gender', 'bio', 'email', 'location'];
        $filled = 0;

        foreach ($fields as $field) {
            if (! empty($users_details->$field)) {
                $filled++;
            }
        }
        $profileCompletion = round(($filled / count($fields)) * 100);
        $profileParts[] = $profileCompletion;

        // additional detils contractor and consultant
        $additionalProfileCompletion = 0; // Default value
        if (Auth::user()->as_a === 'Contractor' || Auth::user()->as_a === 'Consultant') {
            $users_addition_details = UserProfile::where('c_by', Auth::id())->first();
            $fields = [
                'bank_name',
                'acct_holder',
                'acct_no',
                'acct_type',
                'ifsc_code',
                'branch_name',
                'project_category',
                'your_purpose',
                'services_offered',
                'projects_ongoing',
                'ongoing_details',
                'labours',
                'mobilization',
                'strength',
                'client_tele',
                'customer',
                'income_tax',
            ];

            $filled = 0;

            foreach ($fields as $field) {
                if (! empty($users_addition_details->$field)) {
                    $filled++;
                }
            }

            $additionalProfileCompletion = round(($filled / count($fields)) * 100);
            $profileParts[] = $additionalProfileCompletion;
        }

        // vendor additional details for progress
        $vendor_details = 0;
        if (Auth::user()->as_a === 'Vendor') {
            $vendor_addtional_details = UserProfile::where('c_by', Auth::id())->first();
            // Additional profile fields to check
            $fields = [
                'bank_name',
                'acct_holder',
                'acct_no',
                'acct_type',
                'ifsc_code',
                'branch_name',
                'your_purpose',
                'strength',
                'client_tele',
                'customer',
                'delivery_timeline',
                'location_catered',
            ];

            $filled = 0;
            foreach ($fields as $field) {
                if (! empty($vendor_addtional_details->$field)) {
                    $filled++;
                }
            }
            $vendor_details = round(($filled / count($fields)) * 100);
            $profileParts[] = $vendor_details;
        }

        // Student additional details for progress
        $student_completion = 0;
        if (Auth::user()->you_are == 'Professional' && Auth::user()->type_of_names[0] === 'Student') {
            $student_details = UserProfile::where('c_by', Auth::id())->first();
            // Additional profile fields to check
            $fields = [
                'professional_status',
                'education',
                'college',
                'aadhar_no',
                'pan_no',
            ];
            $filled = 0;
            foreach ($fields as $field) {
                if (! empty($student_details->$field)) {
                    $filled++;
                }
            }
            $student_completion = round(($filled / count($fields)) * 100);
            $profileParts[] = $student_completion;
        }

        // Student additional details for progress
        $working_completion = 0;
        if (Auth::user()->you_are == 'Professional' && Auth::user()->type_of_names[0] === 'Working') {
            $working_details = UserProfile::where('c_by', Auth::id())->first();

            // Additional profile fields to check
            $fields = [
                'professional_status',
                'education',
                'designation',
                'employment_type',
                'experience',
                'projects_handled',
                'expertise',
                'current_ctc',
                'notice_period',
                'aadhar_no',
                'pan_no',
            ];
            $filled = 0;
            foreach ($fields as $field) {
                if (! empty($working_details->$field)) {
                    $filled++;
                }
            }
            $working_completion = round(($filled / count($fields)) * 100);
            $profileParts[] = $working_completion;
        }

        // gst detail verification
        $gst_details = 0;
        if (Auth::user()->you_are === 'Business') {
            $gst_addtional_details = GstDetails::where('user_id', Auth::id())->first();
            // Additional profile fields to check
            $fields = [
                'gst_verify',
                'gst_number',
                'name',
                'business_legal',
                'contact_no',
                'email_id',
                'pan_no',
                'register_date',
                'gst_address',
                'nature_business',
                'annual_turnover',
            ];

            $filled = 0;
            foreach ($fields as $field) {
                if (! empty($gst_addtional_details->$field)) {
                    $filled++;
                }
            }
            $gst_details = round(($filled / count($fields)) * 100);
            $profileParts[] = $gst_details;
        }
        $profile_image = Auth::user()->profile_img ? 100 : 0;
        $profileParts[] = $profile_image;

        // if (Auth::user()->as_a === 'Contractor' || Auth::user()->as_a === 'Consultant') {
        //     $totalSections = 4;
        //     $profile_complete= ($profileCompletion+ $additionalProfileCompletion+ $gst_details+$profile_image/$totalSections)*100;
        // }
        if (count($profileParts) > 0) {
            $profile_complete = round(array_sum($profileParts) / count($profileParts));
        }

        $notificationCount = Notification::where('reciever', Auth::id())
            ->where('status', 'active')
            ->count();

        if ($request->header('Authorization')) {
            return response()->json([
                'success' => true,
                'user_basic_details' => $user_basic_details,
                'additional_profile' => $profile,
                'gstdetails' => $gst,
                //  'profileCompletion' =>$profileCompletion,
                //  'contra_consul_data'=>$additionalProfileCompletion,
                //  'vendor_details'    =>$vendor_details,
                //  'student_completion'=>$student_completion,
                //  'working_completion'=>$working_completion,
                'notificationCount' => $notificationCount,
                'gstverified' => $gstverified,
                'profile_complete' => $profile_complete,
                //  'gst_details'

            ], 200);
        }

        return view('myprofile.details.index', compact('profile', 'gstverified', 'locations', 'gst', 'project_category', 'your_purpose', 'services_offered', 'designations'));
    }

    public function editProfile(Request $request)
    {
        $user_basic_details = UserDetail::with('user_location')->where('id', Auth::id())->first();
        $locations = DropdownList::where('dropdown_id', 1)->get();
        $profile = UserProfile::with('projectCatRelation', 'purposeRelation')->where('c_by', Auth::id())->first();
        $gstverified = GstDetails::where('user_id', Auth::id())->where('gst_verify', 'yes')->first();
        if ($gstverified === null) {
            $gstverified = 'no';
        } else {
            $gstverified = 'yes';
        }
        if ($user_basic_details) {
            $typeIds = is_array($user_basic_details->type_of)
                ? $user_basic_details->type_of
                : explode(',', $user_basic_details->type_of);
            $typeDetails = DropdownList::whereIn('id', $typeIds)->get();
            $user_basic_details->type_of_details = $typeDetails;
        }
        $users_details = UserDetail::select('id', 'name', 'user_name', 'gender', 'bio', 'email', 'location')
            ->where('id', Auth::id())
            ->first();
        $fields = ['name', 'user_name', 'gender', 'bio', 'email', 'location'];
        $filled = 0;
        foreach ($fields as $field) {
            if (! empty($users_details->$field)) {
                $filled++;
            }
        }
        $profileCompletion = round(($filled / count($fields)) * 100);

        $additionalProfileCompletion = 0;
        if (Auth::user()->as_a === 'Contractor' || Auth::user()->as_a === 'Consultant') {
            $users_addition_details = UserProfile::where('c_by', Auth::id())->first();
            $fields = [
                'bank_name',
                'acct_holder',
                'acct_no',
                'acct_type',
                'ifsc_code',
                'branch_name',
                'project_category',
                'your_purpose',
                'services_offered',
                'projects_ongoing',
                'ongoing_details',
                'labours',
                'mobilization',
                'strength',
                'client_tele',
                'customer',
                'income_tax',
            ];
            $filled = 0;
            foreach ($fields as $field) {
                if (! empty($users_addition_details->$field)) {
                    $filled++;
                }
            }
            $additionalProfileCompletion = round(($filled / count($fields)) * 100);
        }

        $vendor_details = 0;
        if (Auth::user()->as_a === 'Vendor') {
            $vendor_addtional_details = UserProfile::where('c_by', Auth::id())->first();
            $fields = [
                'bank_name',
                'acct_holder',
                'acct_no',
                'acct_type',
                'ifsc_code',
                'branch_name',
                'your_purpose',
                'strength',
                'client_tele',
                'customer',
                'delivery_timeline',
                'location_catered',

            ];
            $filled = 0;
            foreach ($fields as $field) {
                if (! empty($vendor_addtional_details->$field)) {
                    $filled++;
                }
            }
            $vendor_details = round(($filled / count($fields)) * 100);
        }

        $student_completion = 0;
        if (Auth::user()->you_are == 'Professional' && Auth::user()->type_of_names[0] === 'Student') {
            $student_details = UserProfile::where('c_by', Auth::id())->first();
            $fields = [
                'professional_status',
                'education',
                'college',
                'aadhar_no',
                'pan_no',
            ];
            $filled = 0;
            foreach ($fields as $field) {
                if (! empty($student_details->$field)) {
                    $filled++;
                }
            }
            $student_completion = round(($filled / count($fields)) * 100);
        }

        $working_completion = 0;
        if (Auth::user()->you_are == 'Professional' && Auth::user()->type_of_names[0] === 'Working') {
            $working_details = UserProfile::where('c_by', Auth::id())->first();
            $fields = [
                'professional_status',
                'education',
                'designation',
                'employment_type',
                'experience',
                'projects_handled',
                'expertise',
                'current_ctc',
                'notice_period',
                'aadhar_no',
                'pan_no',
            ];
            $filled = 0;
            foreach ($fields as $field) {
                if (! empty($working_details->$field)) {
                    $filled++;
                }
            }
            $working_completion = round(($filled / count($fields)) * 100);
        }

        $gst_details = 0;
        if (Auth::user()->you_are === 'Business') {
            $gst_addtional_details = GstDetails::where('user_id', Auth::id())->first();
            $fields = [
                'gst_verify',
                'gst_number',
                'name',
                'business_legal',
                'contact_no',
                'email_id',
                'pan_no',
                'register_date',
                'gst_address',
                'nature_business',
                'annual_turnover',

            ];
            $filled = 0;
            foreach ($fields as $field) {
                if (! empty($gst_addtional_details->$field)) {
                    $filled++;
                }
            }
            $gst_details = round(($filled / count($fields)) * 100);
        }
        $profile = UserProfile::where('c_by', Auth::id())->first();
        $vendor_type = DropdownList::where('dropdown_id', 6)->get();
        $contractor_type = DropdownList::where('dropdown_id', 7)->get();
        $consultant_type = DropdownList::where('dropdown_id', 8)->get();
        $professional_type = DropdownList::where('dropdown_id', 9)->get();
        $project_category = DropdownList::where('dropdown_id', 10)->get();
        $your_purpose = DropdownList::where('dropdown_id', 11)->get();
        $services_offered = DropdownList::where('dropdown_id', 12)->get();
        $designations = DropdownList::where('dropdown_id', 13)->get();
        $resources_strength = DropdownList::where('dropdown_id', 14)->get();
        $selectedTypes = explode(',', Auth::user()->type_of);
        if (Auth::user()->as_a == 'Vendor') {
            $type_dropdown = DropdownList::where('dropdown_id', 6)->get();
        }
        if (Auth::user()->as_a == 'Contractor') {
            $type_dropdown = DropdownList::where('dropdown_id', 7)->get();
        }
        if (Auth::user()->as_a == 'Consultant') {
            $type_dropdown = DropdownList::where('dropdown_id', 8)->get();
        }
        if (in_array(Auth::user()->as_a, ['Technical', 'Non-Technical'])) {
            $type_dropdown = DropdownList::where('dropdown_id', 9)->get();
        }
        if ($request->header('Authorization')) {
            return response()->json([
                'success' => true,
                'user_basic_details' => $user_basic_details,
                'additional_profile' => $profile,
                'gstverified' => $gstverified,
                'type_of_dropdown' => $type_dropdown ?? null,
                'project_category' => $project_category,
                'your_purpose' => $your_purpose,
                'resources_strength' => $resources_strength,
                'profileCompletion' => $profileCompletion,
                'contra_consul_data' => $additionalProfileCompletion,
                'vendor_details' => $vendor_details,
                'student_completion' => $student_completion,
                'working_completion' => $working_completion,
                'gst_details' => $gst_details,
                'profile_image' => Auth::user()->profile_img ? 100 : 0,
                'designations' => $designations,
            ], 200);
        }

        return view('myprofile.update.index', compact(
            'profile',
            'project_category',
            'locations',
            'your_purpose',
            'services_offered',
            'designations',
            'vendor_type',
            'contractor_type',
            'consultant_type',
            'professional_type',
            'selectedTypes',
            'profileCompletion',
            'additionalProfileCompletion',
            'vendor_details',
            'student_completion',
            'working_completion',
            'gst_details'
        ));
    }

    public function store(Request $request, Aws $aws)
    {
        try {
            $validTypeOfIds = DropdownList::pluck('id')->toArray();
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'user_name' => 'required|string|max:255',
                'bio' => 'nullable|string|max:255',
                'gender' => 'required|string|max:255',
                'email' => 'nullable|string|max:100',
                'location' => 'required|string|max:255',
                'type_of.*' => ['nullable', Rule::in($validTypeOfIds)],
                'bank_name' => 'nullable|string|max:255',
                'acct_holder' => 'nullable|string|max:255',
                'acct_no' => 'nullable|string|max:255',
                'acct_type' => 'nullable|string|max:255',
                'ifsc_code' => 'nullable|string|max:255',
                'branch_name' => 'nullable|string|max:255',
                'project_category' => 'nullable|string|max:255',
                'your_purpose' => 'nullable|string|max:255',
                'services_offered' => 'nullable|string|max:255',
                'projects_ongoing' => 'nullable|string|max:100',
                'ongoing_details' => 'nullable|string|max:255',
                'labours' => 'nullable|integer',
                'mobilization' => 'nullable|string|max:255',
                'strength' => 'nullable|string|max:255',
                'client_tele' => 'nullable|numeric',
                'customer' => 'nullable|string|max:255',
                'delivery_timeline' => 'nullable|string|max:255',
                'location_catered' => 'nullable|string|max:255',
                'professional_status' => 'nullable|string|max:50',
                'education' => 'nullable|string|max:255',
                'college' => 'nullable|string|max:255',
                'designation' => 'nullable|string|max:255',
                'employment_type' => 'nullable|string|max:255',
                'experience' => 'nullable|string|max:255',
                'projects_handled' => 'nullable|string|max:255',
                'expertise' => 'nullable|string|max:255',
                'current_ctc' => 'nullable|numeric',
                'notice_period' => 'nullable|string|max:50',
                'aadhar_no' => 'nullable|numeric|digits:12',
                'pan_no' => 'nullable|string|max:10',
                'income_tax' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx,doc,csv|max:2048',
            ], [
                'name.required' => 'Name is required.',
                'user_name.required' => 'Username is required.',
                'gender.required' => 'Gender is required.',
                'location.required' => 'Location is required.',
            ]);

            if ($validator->fails()) {
                if ($request->header('Authorization')) {
                    return response()->json([
                        'errors' => $validator->errors(),
                    ], 422);
                }

                return redirect()->back()->withErrors($validator)->withInput();
            }

            $userData = $request->only([
                'name',
                'user_name',
                'bio',
                'gender',
                'email',
                'location',
                'type_of',
            ]);
            if (Auth::user()->you_are == 'Professional') {
                $cat = DropdownList::where('value', $request->professional_status)->value('id');
                // dd($request->type_of);
                $userData['type_of'] = $cat;
            } else {
                $userData['type_of'] = $request->type_of ? implode(',', $request->type_of) : null;
            }

            $profileData = $request->only([
                'bank_name',
                'acct_holder',
                'acct_no',
                'acct_type',
                'ifsc_code',
                'branch_name',
                'project_category',
                'your_purpose',
                'services_offered',
                'projects_ongoing',
                'ongoing_details',
                'labours',
                'mobilization',
                'strength',
                'client_tele',
                'customer',
                'delivery_timeline',
                'location_catered',
                'professional_status',
                'education',
                'college',
                'designation',
                'employment_type',
                'experience',
                'projects_handled',
                'expertise',
                'current_ctc',
                'notice_period',
                'aadhar_no',
                'pan_no',
            ]);

            if ($request->hasFile('income_tax')) {
                $file = $request->file('income_tax');
                if (! is_array($file)) {
                    $file = [$file];
                }
                $folder = 'incometax_returns';
                $s3Result = $aws->common_upload_to_s3($file, $folder);
                $filename = is_array($s3Result) ? $s3Result[0] : $s3Result;
                $profileData['income_tax'] = $filename;
            }

            $profileData['c_by'] = Auth::id();
            $profileData['status'] = 'active';

            UserDetail::updateOrCreate(
                ['id' => Auth::id()],
                $userData
            );

            UserProfile::updateOrCreate(
                ['c_by' => Auth::id()],
                $profileData
            );

            if ($request->header('Authorization')) {
                return response()->json([
                    'success' => true,
                    'meassage' => 'Profile Updated Successfully!',

                ], 200);
            }

            return redirect()
                ->route('my-profile')
                ->with('success', 'Profile Updated Successfully!');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function gst_verify(Request $request)
    {
        // Validate input
        $request->validate([
            'gst_no' => 'required',
        ]);

        // Extract data
        $group_id = 'ec33ab9a-6ebb-46a7-b87d-3966673f1214';
        $task_id = '8abc6431-fc08-4594-bc8d-090df206f15c';
        $gst_number = $request->gst_no;
        $is_details = true;

        // Prepare payload for Idfy API
        $payload = [
            'group_id' => $group_id,
            'task_id' => $task_id,
            'data' => [
                'gstnumber' => $gst_number,
                'isdetails' => $is_details,
            ],
        ];
        $client = new \GuzzleHttp\Client;
        try {
            $response = $client->post('https://eve.idfy.com/v3/tasks/async/retrieve/gst_info', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'api-key' => 'b4bf8650-cd17-43a4-9139-1eb7a6c5ca83',
                    'account-id' => '6f29b17f07fd/d2aecc09-60b4-452b-ba48-debf18233d3e',
                ],
                'json' => $payload,
            ]);
            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);
            $requestId = $responseData['request_id'] ?? null;
            if (! $requestId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request ID not found in first response',
                ], 400);
            }
            sleep(7);
            // Second request using the request_id (adjust method and endpoint as needed)
            $response2 = $client->get('https://eve.idfy.com/v3/tasks', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'api-key' => 'b4bf8650-cd17-43a4-9139-1eb7a6c5ca83',
                    'account-id' => '6f29b17f07fd/d2aecc09-60b4-452b-ba48-debf18233d3e',
                ],
                'query' => [
                    'request_id' => $requestId,
                ],
            ]);

            $responseBody2 = $response2->getBody()->getContents();
            $responseData2 = json_decode($responseBody2, true);
            sleep(7);

            return response()->json([
                'success' => true,
                'data' => $responseData2,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // gst_details store
    public function gst_store(Request $request)
    {
        try {
            $request->validate([
                'gst_number' => 'nullable',
                'name' => 'nullable',
                'business_legal' => 'nullable',
                'contact_no' => 'nullable',
                'pan_no' => 'nullable',
                'email_id' => 'nullable',
                'register_date' => 'nullable',
                'gstaddress' => 'nullable',
                'nature_business' => 'nullable',
                'annual_turnover' => 'nullable',
            ]);

            $userId = Auth::id();
            GstDetails::create([
                'user_id' => $userId,
                'gst_verify' => 'yes',
                'gst_number' => $request->gst_number,
                'name' => $request->name,
                'business_legal' => $request->business_legal,
                'contact_no' => $request->contact_no,
                'email_id' => $request->email_id,
                'pan_no' => $request->pan_no,
                'register_date' => $request->register_date,
                'gst_address' => $request->gstaddress,
                'nature_business' => $request->nature_business,
                'annual_turnover' => $request->annual_turnover,
                'status' => 'active',
                'c_by' => $userId,

            ]);

            if ($request->header('Authorization')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Gst details added successfully!',
                ], 200);
            }

            return back()->with('success', 'GST Details Added Successfully!');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function buyBadge(Request $request)
    {
        $request->validate([
            'badge' => 'required|in:5,10,15',
        ]);

        $user = Auth::user();
        $badge = (int) $request->badge;

        // Define badge prices
        $prices = [
            5 => charge::where('category', '5L_badge')->latest()->value('charge'),
            10 => charge::where('category', '10L_badge')->latest()->value('charge'),
            15 => charge::where('category', '15L_badge')->latest()->value('charge'),
        ];

        $price = $prices[$badge] ?? 0;

        // Validate sales achievements
        $totalEarnings = $this->getUserSales($user->id);

        if (
            ($badge == 5 && ! ($totalEarnings >= 500000 && $totalEarnings < 1000000)) ||
            ($badge == 10 && ! ($totalEarnings >= 1000000 && $totalEarnings < 1500000)) ||
            ($badge == 15 && ! ($totalEarnings >= 1500000))
        ) {
            if ($request->header('Authorization')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not eligible for this badge.',
                ], 403);
            }

            return back()->with('error', 'You are not eligible for this badge.');
        }

        // Check wallet balance
        if ($user->balance < $price) {
            // dd($price);

            return back()->with('error', 'Insufficient wallet balance. Please recharge.');
        }
        $badge = (string) $badge;
        // Atomic transaction
        $affected = UserDetail::where('id', $user->id)
            ->update([
                'balance' => $user->balance - $price,  // Deduct price from balance
                'badge' => $badge,                     // Set the new badge
            ]);
        Badge::create([
            'badge' => $request->badge,
            'amount' => $price,
            'status' => 'active',
            'created_by' => Auth::id(),
        ]);
        if ($affected) {
            return back()->with('success', 'Congratulations! Your badge has been activated successfully.');
        } else {
            return back()->with('error', 'Something went wrong! Please try again later.');
        }
    }

    private function getUserSales($userId)
    {
        if (Auth::user()->you_are === 'Business') {
            $start = Carbon::now()->startOfMonth();       // 1st of month 00:00
            $end = Carbon::now()->startOfMonth()->addDays(14)->endOfDay(); // 15th 23:59
            $totalEarnings = OrderProducts::join('products', 'order_products.product_id', '=', 'products.id')
                ->where('products.created_by', Auth::id())
                ->whereBetween('order_products.created_at', [$start, $end])
                ->selectRaw('SUM(order_products.quantity * products.sp) as total')
                ->value('total') ?? 0;
        }

        return $totalEarnings;
    }

    public function checkUsername(Request $request)
    {
        $username = $request->input('username');
        $exists = UserDetail::where('user_name', $username)
            ->where('id', '!=', Auth::id()) // optional: exclude current user
            ->exists();

        return response()->json(['exists' => $exists]);
    }
}
