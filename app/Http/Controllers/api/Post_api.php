<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Charge;
use App\Models\Chat;
use App\Models\Follow;
use App\Models\GstDetails;
use App\Models\Hashtag;
use App\Models\Jobs;
use App\Models\OrderProducts;
use App\Models\PostLike;
use App\Models\Posts;
use App\Models\Premium;
use App\Models\PremiumUser;
use App\Models\Products;
use App\Models\ReadyToWork;
use App\Models\Save_post;
use App\Models\Service;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Post_api extends Controller
{
    // post reels created by

    public function profile_card(Request $request)
    {
        // $completion = $this->getProfileCompletion();
        $user_id = $request->user_id;
        $percent = getProfileCompletion($user_id);

        $user = UserDetail::withCount(['following', 'followers', 'gst'])->with('profile_report')
            ->where('id', $user_id)
            ->first();
        $post = Posts::where('created_by', $user_id)->whereNull('category')->where('status', 'active')->count();

        $user->is_reported = $user->profile_report !== null;

        $user->post_count = $post;

        $today = Carbon::today();

        // $ready = DB::table('readytowork_boost')->where('user_id', $user_id)
        //     ->latest() // order by created_at DESC
        //     ->get()
        //     ->filter(function ($item) use ($today) {
        //         $endDate = Carbon::parse($item->created_at)->addDays($item->days);

        //         return $today->between(Carbon::parse($item->created_at), $endDate);
        //     })
        //     ->first(); // get the latest valid record

        $ready = ReadyToWork::where('created_by', $user_id)->first();

        // $ready->check = ($ready->expiry_date >= Carbon::now()) ? false : true;

        // Step 2: CHECK if the model was found before accessing properties (The fix!)
        if ($ready) {
            // If the record exists, check its expiry date property.
            // We also check $ready->expiry_date in case the column is NULL in the database.
            if ($ready->expiry_date) {
                // Line 55 (now safe to execute)
                $ready->check = ($ready->expiry_date >= Carbon::now()) ? false : true;
            } else {
                // Handle the case where the record exists but the expiry_date field is NULL
                $ready->check = true; // Assume expired/not ready if date is missing
            }
        } else {
            // Handle the case where NO ReadyToWork record exists for this user.
            // You could initialize an empty object or set a default status.
            $ready = (object) ['check' => false]; // Example: Create a temporary object to hold the status.
        }

        // $isActive = $ready ? true : false;
        // $user->profile_complete = 50;

        $user->ready = $ready->check;
        $user->ready_id = $ready->id ?? null;
        $user->ready_status = $ready->status ?? null;

        // ✅ Check if auth user follows this user
        $authUser = Auth::user();
        $user->is_follow = $authUser->isFollowing($user);

        // dd($user->is_follow);
        $user->profile_complete = $percent;

        return response()->json(['success' => true, 'data' => $user]);
    }

    // post reels created by other user

    public function profile_post_reel(Request $request)
    {
        // log::info(Auth::user()->name);
        $user_id = $request->user_id;

        // if ($request->header('Authorization')) {
        //     return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        //     // $user_id = $request->user_id;
        // }
        // else {
        //
        $posts = Posts::where('created_by', $user_id)
            ->where('status', 'active')
            ->select('id', 'file_type', 'file', 'sense')
            ->orderByDesc('id')
            ->cursorPaginate(15, ['*'], 'cursor', $request->input('cursor')); // ✅ replace get() with cursorPaginate

        $posts->setCollection(
            $posts->getCollection()->map(function ($post) {
                $files = $post->file;

                if (! is_array($files)) {
                    $files = json_decode($files, true);
                }

                $files = is_array($files) ? $files : [];

                $post->first_file = $files[0] ?? null;

                unset($post->file);

                return $post;

            })
        );
        // ->through(function ($post) {
        //     // Normalize "file" field into array
        //     // dd($post);
        //     $files = $post->file;

        //     if (! is_array($files)) {
        //         $files = json_decode($files, true);
        //     }

        //     $files = is_array($files) ? $files : [];

        //     // $post->files = $files;

        //     // dd($files);
        //     $post->first_file = $files[0] ?? null;

        //     // unset($post->file);
        // });

        // $posts->transform(function ($post) {
        //     $post->first_file = json_decode($post->file)[0] ?? null;
        //     // $post->is_saved = $post->post_save !== null;

        //     return $post;
        // });

        if ($posts) {
            return response()->json([
                'success' => true,
                'data' => $posts->items(),
                'next_cursor' => $posts->nextCursor()?->encode(), // ✅ needed for frontend
                'prev_cursor' => $posts->previousCursor()?->encode(),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No reels found']);
    }

    // function for post_feed

    public function post_feed(Request $request)
    {

        $postsCursor = $request->input('posts_cursor');
        $jobsCursor = $request->input('jobs_cursor');
        $productsCursor = $request->input('products_cursor');
        $serviceCursor = $request->input('service_cursor');
        $peopleCursor = $request->input('people_cursor');

        $location = 1;

        $user = Auth::user();

        // Initialize arrays to avoid "Undefined variable" errors if their corresponding 'if' blocks are skipped
        $postsArr = [];
        $jobsArr = []; // <-- THIS IS THE KEY FIX FOR YOUR ERROR
        $productsArr = [];
        $serviceArr = [];
        $suggestUsersArr = [];

        // if ($postsCursor != null) {

        //     // 🔹 Posts
        //     $posts = Posts::with(['likedByAuth', 'post_save', 'user:id,name,user_name,profile_img,badge'])
        //         // ->where('file_type', 'image')
        //         // ->where('created_by', '!=', Auth::id() ?? 0)
        //         ->orderByDesc('created_at')
        //         ->orderByDesc('id')
        //         ->cursorPaginate(24, ['*'], 'posts_cursor', $postsCursor);

        //     //  dd($posts->items());

        //     $posts->getCollection()->transform(function ($post) {
        //         $post->is_liked = $post->likedByAuth !== null;
        //         $post->is_saved = $post->post_save !== null;
        //         $post->is_follow = $post->user ? Auth::user()->isFollowing($post->user) : false;

        //         // $post->is_follow = $post->created_by;
        //         return $post;
        //     });

        //     // Convert collections → arrays
        //     $postsArr = $posts->getCollection()->isEmpty() ? [] : $posts->getCollection()->all();
        //     shuffle($postsArr);
        // }

        $postsPaginator = collect(); // ✅ Always defined as empty collection

        if ($postsCursor == 'empty' || $postsCursor != null) {
            if ($postsCursor == 'empty') {
                $postsCursor = Posts::min('id'); // set to max int for first load
            }
            log::info('Home Feed Cursors - Posts: '.$postsCursor.', Jobs: '.$jobsCursor.', Products: '.$productsCursor.', Services: '.$serviceCursor);
            $query = Posts::where('status', 'active')
                ->where('created_by', '!=', Auth::id())
                ->where('id', '>', $postsCursor); // 👈 only fetch older posts

            $posts = $query->inRandomOrder() // 👈 random each time within range
                ->take(24)
                ->get()
                ->map(function ($post) {
                    $post->is_liked = $post->likedByAuth !== null;
                    $post->is_saved = $post->post_save !== null;
                    $post->is_follow = $post->user ? Auth::user()->isFollowing($post->user) : false;
                    $post->is_reported = $post->post_report !== null;
                    $post->type = 'post';

                    return $post;
                });
            $postsArr = $posts->isEmpty() ? [] : $posts->all();

            // $postsPaginator = $posts;
            // $next['posts'] = $posts->random()->id; // 👈 next cursor is min ID of current batch
        }

        // dd($posts->getCollection()->toArray());

        if (($jobsCursor != null)) {        // 🔹 Jobs
            $jobs = Jobs::
                // where('created_by', '!=', Auth::id() ?? 0)
                // ->where('created_at', '>=', now()->subMonths(3))
                orderByDesc('created_at')
                    ->orderByDesc('id')
                // ->orderByRaw("
                // CASE
                //     WHEN highlighted = 1 AND location = '{$location}' THEN 1
                //     WHEN highlighted = 1 THEN 2
                //     WHEN location = '{$location}' THEN 3
                //     ELSE 4
                // END, created_at DESC, id DESC
                // ")
                    ->cursorPaginate(5, ['*'], 'jobs_cursor', $jobsCursor);

            $jobsArr = $jobs->isEmpty() ? [] : $jobs->items();
            shuffle($jobsArr);

        }

        if ($productsCursor != null) {
            // 🔹 Products
            $products = Products::withCount('reviews')
                ->withAvg('reviews', 'stars')
                // ->where('created_by', '!=', Auth::id() ?? 0)
                // ->where('created_at', '>=', now()->subMonths(6))
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                // ->orderByRaw("
                //     CASE
                //         WHEN highlighted = 1 AND location = '{$location}' THEN 1
                //         WHEN highlighted = 1 THEN 2
                //         WHEN location = '{$location}' THEN 3
                //         ELSE 4
                //     END, created_at DESC, id DESC
                // ")
                ->cursorPaginate(5, ['*'], 'products_cursor', $productsCursor);

            $productsArr = $products->isEmpty() ? [] : $products->items();
            shuffle($productsArr);

        }

        if ($serviceCursor != null) {

            // 🔹 Services
            $service = Service::with('serviceType:id')
                // ->where('created_at', '>=', now()->subMonths(6))
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                // ->where('created_by', '!=', Auth::id() ?? 0)
                // ->orderByRaw("
                //     CASE
                //         WHEN highlighted = 1 AND location = '{$location}' THEN 1
                //         WHEN highlighted = 1 THEN 2
                //         WHEN location = '{$location}' THEN 3
                //         ELSE 4
                //     END, created_at DESC, id DESC
                // ")
                ->cursorPaginate(5, ['*'], 'service_cursor', $serviceCursor);

            $serviceArr = $service->isEmpty() ? [] : $service->items();
            shuffle($serviceArr);

        }

        if ($peopleCursor != null) {

            // already followed users
            $followedIds = $user->following()->pluck('user_detail.id');

            $suggestUsers = UserDetail::where('id', '!=', $user->id)
                ->whereNotIn('id', $followedIds)
                ->orderByDesc('created_at')
                ->cursorPaginate(5, ['*'], 'people_cursor', $peopleCursor);

            $suggestUsersArr = $suggestUsers->isEmpty() ? [] : $suggestUsers->items();
            shuffle($suggestUsersArr);
        }

        // Merge
        $p = $j = $pr = $sr = $su = 0;
        $merged = [];

        while ($p < count($postsArr) || $j < count($jobsArr) || $pr < count($productsArr) || $sr < count($serviceArr) || $su < count($suggestUsersArr)) {
            if ($p < count($postsArr)) {
                $merged[] = ['post' => array_slice($postsArr, $p, 6)];
                $p += 6;
            }

            // if ($user->you_are !== 'Business') {
            if ($j < count($jobsArr)) {
                $merged[] = ['job' => array_slice($jobsArr, $j, 5)];
                $j += 5;
            }
            if ($p < count($postsArr)) {
                $merged[] = ['post' => array_slice($postsArr, $p, 6)];
                $p += 6;
            }
            // }
            if ($pr < count($productsArr)) {
                $merged[] = ['product' => array_slice($productsArr, $pr, 5)];
                $pr += 5;
            }
            if ($p < count($postsArr)) {
                $merged[] = ['post' => array_slice($postsArr, $p, 6)];
                $p += 6;
            }
            if ($sr < count($serviceArr)) {
                $merged[] = ['service' => array_slice($serviceArr, $sr, 5)];
                $sr += 5;
            }
            if ($p < count($postsArr)) {
                $merged[] = ['post' => array_slice($postsArr, $p, 6)];
                $p += 6;
            }
            if ($su < count($suggestUsersArr)) {
                $merged[] = ['people' => array_slice($suggestUsersArr, $su, 5)];
                $su += 5;
            }

        }

        $next = [
            // 'posts' => $posts->nextCursor()?->encode() ?? null,
            'posts' => $posts->random()->id ?? null,
            'jobs' => $jobs->nextCursor()?->encode() ?? null,
            'products' => $products->nextCursor()?->encode() ?? null,
            'services' => $service->nextCursor()?->encode() ?? null,
            'people' => $suggestUsers->nextCursor()?->encode() ?? null,
        ];

        $profile_data = getProfileCompletion();
        // $this->profile_comp();

        // // basic details for progress
        // $users_basic_details = UserDetail::select('id', 'name', 'user_name', 'gender', 'bio', 'email', 'location', 'profile_img')
        //     ->where('id', Auth::id())
        //     ->first();
        // $fields = ['name', 'user_name', 'gender', 'bio', 'email', 'location'];
        // $filled = 0;

        // foreach ($fields as $field) {
        //     if (! empty($users_basic_details->$field)) {
        //         $filled++;
        //     }
        // }
        // $profileCompletion = round(($filled / count($fields)) * 100);

        // // additional detils contractor and consultant
        // $additionalProfileCompletion = 0; // Default value

        // if (Auth::user()->as_a === 'Contractor' || Auth::user()->as_a === 'Consultant') {

        //     $users_addition_details = UserProfile::where('c_by', Auth::id())->first();

        //     $fields = [
        //         'project_category',
        //         'your_purpose',
        //         'services_offered',
        //         'projects_ongoing',
        //         'ongoing_details',
        //         'labours',
        //         'mobilization',
        //         'strength',
        //         'client_tele',
        //         'customer',
        //         'income_tax',
        //     ];

        //     $filled = 0;

        //     foreach ($fields as $field) {
        //         if (! empty($users_addition_details->$field)) {
        //             $filled++;
        //         }
        //     }

        //     $additionalProfileCompletion = round(($filled / count($fields)) * 100);
        // }

        // // vendor additional details for progress
        // $vendor_details = 0;
        // if (Auth::user()->as_a === 'Vendor') {

        //     $vendor_addtional_details = UserProfile::where('c_by', Auth::id())->first();

        //     // Additional profile fields to check
        //     $fields = [

        //         'your_purpose',
        //         'strength',
        //         'client_tele',
        //         'customer',
        //         'delivery_timeline',
        //         'location_catered',

        //     ];

        //     $filled = 0;

        //     foreach ($fields as $field) {
        //         if (! empty($vendor_addtional_details->$field)) {
        //             $filled++;
        //         }
        //     }

        //     $vendor_details = round(($filled / count($fields)) * 100);
        // }

        // // Student additional details for progress
        // $student_completion = 0;

        // if (Auth::user()->type_of_names[0] === 'Student') {

        //     $student_details = UserProfile::where('c_by', Auth::id())->first();

        //     // Additional profile fields to check
        //     $fields = [
        //         'professional_status',
        //         'education',
        //         'college',
        //         'aadhar_no',
        //         'pan_no',
        //     ];

        //     $filled = 0;

        //     foreach ($fields as $field) {
        //         if (! empty($student_details->$field)) {
        //             $filled++;
        //         }
        //     }

        //     $student_completion = round(($filled / count($fields)) * 100);
        // }

        // // Student additional details for progress
        // $working_completion = 0;

        // if (Auth::user()->type_of_names[0] === 'Working') {

        //     $working_details = UserProfile::where('c_by', Auth::id())->first();

        //     // Additional profile fields to check
        //     $fields = [
        //         'professional_status',
        //         'education',
        //         'designation',
        //         'employment_type',
        //         'experience',
        //         'projects_handled',
        //         'expertise',
        //         'current_ctc',
        //         'notice_period',
        //         'aadhar_no',
        //         'pan_no',
        //     ];

        //     $filled = 0;

        //     foreach ($fields as $field) {
        //         if (! empty($working_details->$field)) {
        //             $filled++;
        //         }
        //     }

        //     $working_completion = round(($filled / count($fields)) * 100);
        // }

        // // gst detail verification

        // $gst_details = 0;
        // if (Auth::user()->you_are === 'Business') {

        //     $gst_addtional_details = GstDetails::where('user_id', Auth::id())->first();

        //     // Additional profile fields to check
        //     $fields = [

        //         'gst_verify',
        //         'gst_number',
        //         'name',
        //         'business_legal',
        //         'contact_no',
        //         'email_id',
        //         'pan_no',
        //         'register_date',
        //         'gst_address',
        //         'nature_business',
        //         'annual_turnover',

        //     ];

        //     $filled = 0;

        //     foreach ($fields as $field) {
        //         if (! empty($gst_addtional_details->$field)) {
        //             $filled++;
        //         }
        //     }

        //     $gst_details = round(($filled / count($fields)) * 100);
        // }

        // $com_img = ($users_basic_details->profile_img != null) ? 100 : 0;
        // $profile_data = [

        //     'profile_completion' => $profileCompletion,
        //     'contractor_completion' => $additionalProfileCompletion,
        //     'vendor_completion' => $vendor_details,
        //     'student_completion' => $student_completion,
        //     'working_completion' => $working_completion,
        //     'gst_details' => $gst_details,
        //     'profile_img' => $com_img,
        // ];

        // $user_type = Auth::user()->you_are;
        // if ($user_type == 'Business') {
        //     if (Auth::user()->as_a == 'Contractor' || Auth::user()->as_a == 'Consultant') {
        //         $comp = ($profileCompletion + $additionalProfileCompletion + $gst_details) / 3;
        //     } elseif (Auth::user()->as_a == 'Vendor') {
        //         $comp = ($profileCompletion + $vendor_details + $com_img) / 3;
        //     }

        // } elseif ($user_type == 'Professional') {
        //     if (Auth::user()->type_of_names[0] == 'Working') {
        //         $comp = ($profileCompletion + $working_completion + $com_img) / 3;
        //     } elseif (Auth::user()->type_of_names[0] == 'Student') {
        //         $comp = ($profileCompletion + $student_completion + $com_img) / 3;
        //     }
        // } else {
        //     $comp = ($profileCompletion + $com_img) / 2;
        // }

        // badge details

        $totalEarnings = OrderProducts::whereHas('product', function ($query) {
            $query->where('created_by', Auth::id());
        })
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum(DB::raw('(base_price * quantity) * (1 + (tax / 100))'));

        $totalEarnings = 1500000;

        $badge_status = Badge::where('created_by', Auth::id())
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->latest()
            ->first();
        // dd($totalEarnings);
        // 🔹 Mobile API: always JSON
        if ($request->wantsJson()) {
            return response()->json([
                'data' => $merged,
                'next' => $next,
                'profile_data' => $profile_data,
                'total_earnings' => round($totalEarnings),
                'badge' => $badge_status->badge ?? 'No Badge',
                'success' => true,
            ]);
        }
    }

    // reels feeding

    public function reel_feed(Request $request)
    {
        $cursor = $request->input('reels_cursor'); // client pagination token
        $perPage = 10; // reels per request
        $hasAsset = false; // default

        $followedUserIds = Follow::where('follower_id', Auth::id())
            ->pluck('following_id')
            ->toArray();

        // 🔹 Real DB reels
        $posts = Posts::with(['likedByAuth', 'post_save', 'user:id,name,user_name,profile_img,badge'])
            ->where('created_by', '!=', Auth::id())
            ->where('status', 'active')
            // ->orderByDesc('id')
            ->cursorPaginate($perPage, ['*'], 'reels_cursor', $cursor);

        if ($request->filled('reel_id')) {

            $reel_id = $request->reel_id;

            $specificReel = Posts::with(['likedByAuth', 'post_save', 'user:id,name,user_name,profile_img,badge'])
                ->where('id', $reel_id)
                ->where('status', 'active')
                ->first();

            if ($specificReel) {
                // Prepend the reel to the collection if it's not already included
                $alreadyIncluded = $posts->getCollection()->contains('id', $specificReel->id);

                if (! $alreadyIncluded) {
                    $posts->setCollection(
                        collect([$specificReel])->concat($posts->getCollection())->values() // 🔹 this line is CRUCIAL
                    );
                }
            }

            // log::info("Specific reel ID {$reel_id} included in the feed.");
            // log::info($posts);

        }

        // transform each post
        $posts->getCollection()->transform(function ($post) use ($followedUserIds) {
            $post->is_liked = $post->likedByAuth !== null;
            $post->is_saved = $post->post_save !== null;
            $post->files = $post->file[0] ?? null;
            $post->is_reported = $post->post_report !== null;
            $post->is_followed = in_array($post->created_by, $followedUserIds);
            $post->type = 'db';
            unset($post->file);

            return $post;
        });

        // 🔹 20% chance → inject dummy reel inside the collection
        if (rand(1, 100) <= 20) {
            $assetVideo = (object) [
                'id' => 'asset_1',
                'file_type' => 'premium',
                'file' => asset('assets/images/dog.mp4'),
                'category' => null,
                'caption' => 'Premium Content',
                'like_cnt' => 0,
                'com_cnt' => 0,
                'category_id' => 0,
                'created_by' => 0,
                'user' => (object) [
                    'id' => 0,
                    'name' => 'Onstru Premium',
                    'user_name' => 'onstru',
                    'profile_img' => asset('assets/images/Favicon.png'),
                    'badge' => 0,
                ],
                'is_liked' => false,
                'is_saved' => false,
                'is_followed' => false,
                // 'likedByAuth' => null,
                // 'post_save'   => null,
                'type' => 'asset',
                'created_at' => now(),
            ];

            $collection = $posts->getCollection();
            $randomIndex = rand(0, $collection->count()); // random spot
            $collection->splice($randomIndex, 0, [$assetVideo]);

            $hasAsset = $collection->contains(function ($post) {
                return ($post->type ?? '') === 'asset';
            });

            $posts->setCollection($collection->values());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'reel' => $posts,
                'next_cursor' => $posts->nextCursor()?->encode(),
                'posts_type' => $hasAsset,
                'success' => true,
            ]);
        }
    }

    // function for individual post

    public function ind_post(Request $request)
    {
        $reel_id = $request->post_id;

        $followedUserIds = Follow::where('follower_id', Auth::id())
            ->pluck('following_id')
            ->toArray();

        $post = Posts::with(['likedByAuth', 'post_save', 'user:id,name,user_name,profile_img,badge'])
            ->where('id', $reel_id)
            ->where('status', 'active')
            ->first();

        if (! $post) {
            return response()->json(['success' => false, 'message' => 'Reel not found'], 404);
        }

        $post->is_liked = $post->likedByAuth !== null;
        $post->is_saved = $post->post_save !== null;
        $post->files = $post->file ?? null;
        $post->is_reported = $post->post_report !== null;
        $post->is_followed = in_array($post->created_by, $followedUserIds);
        unset($post->file);

        return response()->json(['success' => true, 'data' => $post]);
    }

    // function for explore scree with reels with shuffle

    public function explore_reel(Request $request)
    {
        $cursor = $request->input('reels_cursor');
        $perPage = 15;

        // $followedUserIds = Follow::where('follower_id', Auth::id())
        //     ->pluck('following_id')
        //     ->toArray();

        $auth = Auth::user()->id ?? 0;

        $search = $request->input('search');

        $query = Posts::query()
            ->where('status', 'active')
            ->whereNull('category')
            ->orderByDesc('id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                // dd($search);
                $hashtag = strtolower(ltrim($search, '#'));
                // Match captions containing this hashtag
                $q->whereRaw('LOWER(REPLACE(caption, "#", "")) LIKE ?', ["%{$hashtag}%"]);

            });
        } else {
            // Exclude current user's posts for default feed
            $query->where('created_by', '!=', $auth);
        }

        $posts = $query->cursorPaginate($perPage, ['*'], 'reels_cursor', $cursor);

        // work with collection
        $collection = collect($posts->items())->transform(function ($post) {
            $files = is_array($post->file) ? $post->file : json_decode($post->file, true);
            $post->files = $files[0] ?? null;
            // $post->is_followed = in_array($post->created_by, $followedUserIds);
            // unset($post->file);

            // return $post;
            return collect($post)->only([
                'id', 'title', 'created_by', 'status', 'file_type', 'sense', // keep only what you need
            ])->merge([
                'first_file' => $files[0] ?? null,
            ]);
        });

        // shuffle results
        $posts->setCollection($collection->shuffle()->values());

        if ($request->header('Authorization')) {
            return response()->json([
                'success' => true,
                'reel' => $posts,
                'next_cursor' => $posts->nextCursor()?->encode(),
            ]);
        }
    }

    // public function for comment list

    public function comment_list(Request $req)
    {

        $comments = DB::table('comment_list')
            ->join('user_detail as users', 'comment_list.user_id', '=', 'users.id')
            ->join('posts', 'comment_list.post_id', '=', 'posts.id')
            ->where('comment_list.post_id', $req->post_id)
            ->latest('comment_list.created_at')
            ->select('comment_list.id as com_id', 'users.id', 'users.name', 'users.user_name', 'users.badge', 'users.profile_img', 'comment_list.comment', 'comment_list.created_at as c_at', 'posts.created_by as post_owner')
            ->get();

        if ($comments) {
            return response()->json(['success' => true, 'data' => $comments]);
        }

        return response()->json(['success' => false, 'message' => 'No comments found']);
    }

    // function for share list

    public function share_list(Request $req)
    {
        // $users = Follow::where('follower_id', Auth::id() ?? 5)
        //     ->join('user_detail as ud', 'ud.id', '=', 'follows.following_id')
        //     ->select('ud.id', 'ud.user_name', 'ud.badge', 'ud.profile_img', 'ud.as_a')->get();

        $auth_following = Follow::where('follower_id', Auth::id())
            ->join('user_detail as ud', 'ud.id', '=', 'follows.following_id')
            ->select('ud.id', 'ud.user_name', 'ud.badge', 'ud.profile_img')->get();

        $auth_followers = Follow::where('follows.following_id', Auth::id())
            ->join('user_detail as ud', 'ud.id', '=', 'follows.follower_id')
            ->select('ud.id', 'ud.user_name', 'ud.badge', 'ud.profile_img')
            ->get();

        // $users = $auth_following->concat($auth_followers);

        // Log::info("without:\n".print_r($users->toArray(), true));

        // 🟦 Combine both without duplicates
        $users = $auth_following->merge($auth_followers)->unique('id')->values();

        // dd($users);

        if ($users) {
            return response()->json(['success' => true, 'data' => $users]);
        }

        return response()->json(['success' => false, 'message' => 'No users found']);
    }

    // function for chat list

    public function chat_list(Request $req)
    {
        $authId = Auth::id() ?? 4;

        // 🔹 Latest chat per user (distinct by pair)
        $latestMessages = DB::table('chat as c1')
            ->select('c1.*')
            ->where(function ($q) use ($authId) {
                $q->where('c1.sender', $authId)
                    ->orWhere('c1.receiver', $authId);
            })
            ->whereRaw('c1.id = (   
        SELECT c2.id FROM chat c2
        WHERE (
            (c2.sender = c1.sender AND c2.receiver = c1.receiver)
            OR
            (c2.sender = c1.receiver AND c2.receiver = c1.sender)
        )
        ORDER BY c2.created_at DESC
        LIMIT 1
    )')
            ->orderBy('c1.created_at', 'desc')
            ->get();

        // 🔹 Collect all userIds from chats
        $chatUserIds = $latestMessages->map(function ($chat) use ($authId) {
            return $chat->sender == $authId ? $chat->receiver : $chat->sender;
        })->unique()->values();

        // dd($chatUserIds);

        // 🔹 Fetch those users
        $chatUsers = DB::table('user_detail')
            ->whereIn('id', $chatUserIds)
            ->get()
            ->keyBy('id');

        $unseenCounts = DB::table('chat')
            ->select('sender', 'receiver', DB::raw('COUNT(*) as unseen_count'))
            ->where('seen', 0)
            ->where('receiver', $authId)
            ->groupBy('sender', 'receiver')
            ->get()
            ->keyBy('sender'); // keyed by sender id

        // dd(DB::table('chat')->where('receiver', $authId)->pluck('seen', 'id'));

        // dd($unseenCounts);

        // 🔹 Transform messages into objects with user info
        $latestMessages = $latestMessages->map(function ($chat) use ($authId, $chatUsers, $unseenCounts) {
            $userId = $chat->sender == $authId ? $chat->receiver : $chat->sender;
            $user = $chatUsers[$userId] ?? null;

            // If I am receiver → unseen count matters, else 0
            $unseen = 0;
            if ($chat->receiver == $authId) {
                $unseen = $unseenCounts[$userId]->unseen_count ?? 0;
                // log::info("Unseen from {$userId} to {$authId}: {$unseen}");
            }

            // check the user last update based on that we will show online or offline
            $user_active = UserDetail::where('id', $userId)->first();
            $lastUpdate = Carbon::parse($user_active->updated_at);
            $now = Carbon::now();
            $diffInMinutes = round($lastUpdate->diffInMinutes($now));

            $last_time = $diffInMinutes <= 3 ? true : false;

            return (object) [
                'chat_id' => $chat->id,
                'message' => $chat->message,
                'created_at' => $chat->created_at,
                'user_id' => $user?->id,
                'bio_name' => $user?->name,
                'user_name' => $user?->user_name,
                'user_email' => $user?->email,
                'profile_img' => $user?->profile_img,
                'badge' => $user?->badge,
                'type' => $chat->type, // 🔹 so we can tell these apart later
                'unseen' => $unseen, // 🔹 new field
                'as_a' => $user?->as_a,
                'is_online' => ($last_time == false) ? $user_active->updated_at->diffForHumans() : 'Online', // online if last active within 3 minutes

                // 'diffInMinutes' => $diffInMinutes,
            ];
        });

        // $followRelations = DB::table('follows')
        //     ->select(
        //         DB::raw("CASE
        //     WHEN follower_id = {$authId} THEN following_id
        //     ELSE follower_id
        // END as user_id"),
        //         DB::raw("CASE
        //     WHEN follower_id = {$authId} THEN 'following'
        //     ELSE 'follower'
        // END as relation_type")
        //     )
        //     ->where('follower_id', $authId)
        //     ->orWhere('following_id', $authId)
        //     ->get();

        // Extract all IDs
        // $followIds = $followRelations->pluck('user_id');

        // $followersNotInChats = DB::table('user_detail')
        //     ->whereIn('id', $followIds)
        //     ->whereNotIn('id', $chatUserIds)
        //     ->get()
        //     ->map(function ($user) use ($followRelations) {
        //         // Find that user's relation type
        //         $relation = $followRelations->firstWhere('user_id', $user->id);
        //         $relationType = $relation->relation_type ?? 'follower'; // default safety

        //         return (object) [
        //             'chat_id' => null,
        //             'message' => null,
        //             'created_at' => null,
        //             'user_id' => $user->id,
        //             'bio_name' => $user->name,
        //             'user_name' => $user->user_name,
        //             'user_email' => $user->email,
        //             'profile_img' => $user->profile_img,
        //             'type' => $relationType, // 👈 either 'following' or 'follower'
        //             'unseen' => 0,
        //         ];
        //     });

        // 🔹 Followers who are NOT in chats (for new chat list)
        $followersNotInChats = DB::table('follows as f')
            ->leftJoin('user_detail as u', 'u.id', '=', 'f.following_id')
            ->where('f.follower_id', $authId)
            // ->orWhere('f.following_id', $authId)
            ->whereNotIn('u.id', $chatUserIds)
            ->select('u.*')
            ->get()
            ->map(function ($user) {

                $user_active = UserDetail::where('id', $user->id)->first();
                $lastUpdate = Carbon::parse($user_active->updated_at);
                $now = Carbon::now();
                $diffInMinutes = round($lastUpdate->diffInMinutes($now));

                $last_time = $diffInMinutes <= 3 ? true : false;

                return (object) [
                    'chat_id' => null,
                    'message' => null,
                    'created_at' => null,
                    'user_id' => $user->id ?? null,
                    'bio_name' => $user->name ?? null,
                    'user_name' => $user->user_name ?? null,
                    'user_email' => $user->email ?? null,
                    'profile_img' => $user->profile_img ?? null,
                    'type' => 'follower', // 🔹 mark as follower
                    'unseen' => 0, // 🔹 always 0
                    'as_a' => $user->as_a,
                    'badge' => $user?->badge,
                    'is_online' => ($last_time == false) ? $user_active->updated_at->diffForHumans() : 'Online', // online if last active within 3 minutes
                    // 'diffInMinutes' => $diffInMinutes,
                ];
            });

        // ✅ Merge chats + followers into one collection
        $allContacts = $latestMessages->concat($followersNotInChats);

        $merged = $allContacts->sortByDesc('created_at')->values();

        // Get the cursor from request (optional, for next/previous page)
        // $cursor = $req->input('cursor');

        // // How many items per page
        // $perPage = 2;

        // // Create a cursor paginator manually
        // $paginated = new CursorPaginator(
        //     $merged->forPage(1, $perPage + 1), // we’ll slice data manually
        //     $perPage,
        //     $cursor,
        //     ['path' => $req->url(), 'query' => $req->query()]
        // );

        // // Slice the data correctly
        // $items = $merged->take($perPage);

        // // Return JSON response
        // return response()->json([
        //     'success' => true,
        //     'data' => $items->values(),
        //     'pagination' => [
        //         'next_cursor' => $paginated->nextCursor()?->encode(),
        //         'prev_cursor' => $paginated->previousCursor()?->encode(),
        //         'per_page' => $perPage,
        //     ],
        // ]);

        return response()->json(['success' => true, 'data' => $merged]);
    }

    public function chat_ind_list(Request $req)
    {
        $receiverId = (int) $req->rec_id;

        // If no receiver ID, return empty HTML
        if (! $receiverId) {
            return response()->json(['success' => false, 'data' => 'no Reciver id']);
        }

        $authId = Auth::id();

        // dd($authId, $receiverId);
        $profile = UserDetail::find($receiverId);
        // Fetch all messages between auth user and receiver
        $messages = Chat::where(function ($q) use ($authId, $receiverId) {
            $q->where('sender', $authId)->where('receiver', $receiverId);
        })->orWhere(function ($q) use ($authId, $receiverId) {
            $q->where('sender', $receiverId)->where('receiver', $authId);
        })
            ->where('created_at', '>=', now()->subMonths(6))
            ->orderBy('created_at', 'asc')
            ->get()->map(function ($msg) {
                $msg->c_at = $msg->created_at->toDateTimeString();

                if ($msg->type && in_array($msg->type, ['job', 'product', 'service', 'post', 'profile'])) {
                    $parts = explode('_', $msg->message, 4);

                    log::info('Shared message parts', ['parts' => $parts]);
                    // if (count($parts) >= 4) {
                    $shareType = $parts[0];
                    $shareTitle = $parts[1] ?? 'No Title';
                    $shareId = $parts[2] ?? '0';
                    $shareUrl = $parts[3] ?? '#';
                    $link = $parts[3];

                    if ($msg->type == 'post') {
                        $post_data = Posts::with('user:id,user_name,profile_img')->where('id', $shareId)->first();
                        $msg->post_id = $post_data->id ?? null;
                        $msg->post_type = $post_data->file_type ?? null;
                        $msg->post_sense = $post_data->sense ?? null;
                        $msg->post_url = $post_data->file[0] ?? null;
                        $msg->post_creator_name = $post_data->user->user_name ?? null;
                        $msg->post_creator_img = $post_data->user->profile_img ?? 'assets/images/Avatar.png';
                        $msg->post_caption = $post_data->caption ?? null;
                        $msg->post_creator_badge = $post_data->user->badge ?? 0;
                        // $shareTitle = $post_data->caption ?? null;
                        // $shareSubTitle = 'Post Shared';
                        // $userProfileImg = 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.$post_data->user->profile_img ?? 'assets/images/Avatar.png';
                    } elseif ($msg->type == 'profile') {
                        $user_data = UserDetail::where('id', $shareId)->first();
                        $msg->profile_id = $user_data->id ?? null;
                        $msg->profile_name = $user_data->name ?? null;
                        $msg->profile_username = $user_data->user_name ?? null;
                        $msg->profile_img = $user_data->profile_img ?? 'assets/images/Avatar.png';
                        $msg->badge = $user_data->badge ?? 0;

                        // $shareUrl = 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.$user_data->profile_img ?? null;
                        // $shareTitle = $user_data->name ?? null;
                        // $shareSubTitle = $user_data->user_name ?? null;
                    } elseif ($msg->type == 'job') {
                        $job_data = Jobs::where('id', $shareId)->first();
                        $msg->job_id = $job_data->id ?? null;
                        $msg->job_title = $job_data->title ?? null;
                        $msg->job_type = 'Job - '.$job_data->categoryRelation->value ?? null;
                        $msg->job_img = env('BASE_URL').'assets/images/NoImage.png';
                        $msg->job_created = $job_data->created_by ?? null;
                    } elseif ($msg->type == 'product') {
                        $product_data = Products::where('id', $shareId)->first();
                        $msg->product_id = $product_data->id ?? null;
                        $msg->product_name = $product_data->name ?? null;
                        $msg->product_type = 'Product - '.$product_data->categoryRelation->value ?? null;
                        $msg->product_img = $product_data->cover_img ?? 'assets/images/NoImage.png';
                        $msg->product_created = $product_data->created_by ?? null;
                        // $shareUrl = 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.$product_data->cover_img ?? null;
                        // $shareTitle = $product_data->name ?? null;
                        // $shareSubTitle = 'Products - '.$product_data->categoryRelation->value ?? null;
                    } elseif ($msg->type == 'service') {
                        $service_data = Service::where('id', $shareId)->first();
                        $msg->service_id = $service_data->id ?? null;
                        $msg->service_name = $service_data->title ?? null;
                        $msg->service_type = 'Services - '.$service_data->serviceType->value ?? null;
                        $msg->service_img = $service_data->image ?? 'assets/images/NoImage.png';
                        $msg->service_created = $service_data->created_by ?? null;
                        // $shareUrl = 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.$service_data->image ?? null;
                        // $shareUrl = 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.$service_data->image ?? null;
                        // $shareTitle = $service_data->title ?? null;
                        // $shareSubTitle = 'Services - '.$service_data->serviceType->value ?? null;
                    }
                }

                return $msg;
            });

        // check the user last update based on that we will show online or offline
        $user_active = UserDetail::where('id', $receiverId)->first();
        $lastUpdate = Carbon::parse($user_active->updated_at);
        $now = Carbon::now();
        $diffInMinutes = round($lastUpdate->diffInMinutes($now));

        $last_time = $diffInMinutes <= 3 ? true : false;

        // dd($messages);

        $updateSeen = Chat::where('sender', $receiverId)
            ->where('receiver', $authId)
            ->where('seen', 0)
            ->update(['seen' => 1]);

        $update_chat = UserDetail::where('id', $authId)->update(['open_chat' => $receiverId]);

        return response()->json(['success' => true, 'data' => $messages, 'profile' => $profile, 'is_online' => ($last_time == false) ? $user_active->updated_at->diffForHumans() : 'Online']);
    }

    // premium reel api

    public function premium(Request $req)
    {

        // $premiumUser = PremiumUser::where('user_id', Auth::id())->whereMonth('created_at', now()->month)
        //     ->whereYear('created_at', now()->year)
        //     ->first();

        $latestPremiumUser = PremiumUser::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->first();

        // dd($latestPremiumUser);

        $isPremium = false;
        $premium_charge = Charge::where('category', 'premium')->latest()->value('charge') * 1.18;

        if ($latestPremiumUser) {
            // 2. Get the date 30 days ago from now.
            $cutoffDate = Carbon::now()->subDays(30);

            log::info('Latest Premium User created_at: '.$latestPremiumUser->created_at);
            log::info('Cutoff Date (30 days ago): '.$cutoffDate);

            // 3. Check if the latest record's 'created_at' is AFTER the cutoff date.
            // This means the record is less than 30 days old.
            if ($latestPremiumUser->created_at->greaterThan($cutoffDate)) {
                $isPremium = true;
            }
        }
        if (! $isPremium) {
            return response()->json(['success' => false, 'message' => 'User is not a premium member', 'premium_charge' => $premium_charge]);
        }

        $premium = Premium::latest()->cursorPaginate(10);

        // Format created_at for each item
        $premium->getCollection()->transform(function ($item) {
            $item->formatted_created_at = $item->created_at->format('d M Y h:i A');

            return $item;
        });

        return response()->json(['success' => true, 'data' => $premium, 'next_cursor' => $premium->nextPageUrl()]);

    }

    // post saved function
    public function saved_posts(Request $request)
    {
        $user_id = Auth::id() ?? 4;

        $posts = Save_post::with(['post_data'])
            // ->whereHas('post_save', function ($q) use ($user_id) {
            //     $q->where('user_id', $user_id);
            // })
            ->where('user_id', $user_id)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->cursorPaginate(20, ['*'], 'saved_cursor', $request->input('cursor')); // ✅ replace get() with cursorPaginate

        // work with collection
        $collection = collect($posts->items())->transform(function ($post) {

            $files = is_array($post->post_data->file) ? $post->post_data->file : json_decode($post->post_data->file, true);
            $post->files = $files[0] ?? null;
            // $post->is_followed = in_array($post->created_by, $followedUserIds);
            // unset($post->file);

            // return $post;
            return [
                'id' => $post->id,
                'title' => $post->post_data->caption ?? null,
                'created_by' => $post->post_data->created_by ?? null,
                'status' => $post->post_data->status ?? null,
                'file_type' => $post->post_data->file_type ?? null,
                'first_file' => $files[0] ?? null,
                'sense' => $post->post_data->sense ?? null,
            ];
        });

        if ($posts) {
            return response()->json([
                'success' => true,
                'data' => $collection,
                'next_cursor' => $posts->nextCursor()?->encode(), // ✅ needed for frontend
                'prev_cursor' => $posts->previousCursor()?->encode(),
                'next_page' => $posts->nextPageUrl(),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No reels or Post found']);
    }

    // liked post

    // post saved function
    public function liked_posts(Request $request)
    {
        $user_id = Auth::id() ?? 4;

        $posts = PostLike::with(['post_data'])
            // ->whereHas('post_save', function ($q) use ($user_id) {
            //     $q->where('user_id', $user_id);
            // })
            ->where('user_id', $user_id)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->cursorPaginate(20, ['*'], 'saved_cursor', $request->input('cursor')); // ✅ replace get() with cursorPaginate

        // work with collection
        $collection = collect($posts->items())->transform(function ($post) {

            $files = is_array($post->post_data->file) ? $post->post_data->file : json_decode($post->post_data->file, true);
            $post->files = $files[0] ?? null;
            // $post->is_followed = in_array($post->created_by, $followedUserIds);
            // unset($post->file);

            // return $post;
            return [
                'id' => $post->id,
                'title' => $post->post_data->caption ?? null,
                'created_by' => $post->post_data->created_by ?? null,
                'status' => $post->post_data->status ?? null,
                'file_type' => $post->post_data->file_type ?? null,
                'first_file' => $files[0] ?? null,
                'sense' => $post->post_data->sense ?? null,
            ];
        });

        if ($posts) {
            return response()->json([
                'success' => true,
                'data' => $collection,
                'next_cursor' => $posts->nextCursor()?->encode(), // ✅ needed for frontend
                'prev_cursor' => $posts->previousCursor()?->encode(),
                'next_page' => $posts->nextPageUrl(),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No reels or Post found']);
    }

    // function for follow list

    public function follow_list(Request $req)
    {
        // $type = $req->type; // 'followers' or 'following'
        $user_id = $req->user_id;

        // $following = Auth::user()->following()->latest('follows.created_at');
        // $followers = Auth::user()->followers()->latest('follows.created_at');

        $following = Follow::with('followingUser:id,name,user_name,profile_img,badge,as_a')->where('follower_id', $user_id)
            ->select('id', 'following_id')
            ->latest('follows.created_at')
            ->get();

        $followers = Follow::with('followerUser:id,name,user_name,profile_img,badge,as_a')->where('following_id', $user_id)
            ->select('id', 'follower_id')
            ->latest('follows.created_at')
            ->get();

        $auth_follow = UserDetail::where('id', $user_id)
            ->select('id', 'name', 'user_name', 'profile_img', 'badge', 'as_a')
            ->first();

        // $following = $follow->filter(function ($item) use ($user_id) {
        //     return $item->follower_id == $user_id;
        // })->map(function ($item) {
        //     return $item->followingUser; // assuming you have a relationship defined
        // })->values();

        // if ($users) {
        return response()->json(['success' => true, 'following' => $following, 'followers' => $followers, 'auth_follow' => $auth_follow]);
        // }

        return response()->json(['success' => false, 'message' => 'No users found']);
    }

    // function for hash tag search

    public function hashtag_search(Request $req)
    {
        $query = $req->hashtag;

        // $posts = Posts::where('caption', 'like', "%{$query}%")
        //     ->where('status', 'active')
        //     ->orderByDesc('created_at')
        //     ->get();

        $hash = Hashtag::query()
            ->when(! empty($query), function ($q) use ($query) {
                $q->where('tag_name', 'like', "%{$query}%");
            })
            ->orderByDesc('created_at')
            ->select('id', 'tag_name')
            ->get();

        if ($hash) {
            return response()->json(['success' => true, 'data' => $hash]);
        }

        return response()->json(['success' => false, 'message' => 'No Hashtags found']);
    }

    // public function profile_comp()
    // {

    //     // basic details for progress
    //     $users_basic_details = UserDetail::select('id', 'name', 'user_name', 'gender', 'bio', 'email', 'location', 'profile_img')
    //         ->where('id', Auth::id())
    //         ->first();
    //     $fields = ['name', 'user_name', 'gender', 'bio', 'email', 'location'];
    //     $filled = 0;

    //     foreach ($fields as $field) {
    //         if (! empty($users_basic_details->$field)) {
    //             $filled++;
    //         }
    //     }
    //     $profileCompletion = round(($filled / count($fields)) * 100);

    //     // additional detils contractor and consultant
    //     $additionalProfileCompletion = 0; // Default value

    //     if (Auth::user()->as_a === 'Contractor' || Auth::user()->as_a === 'Consultant') {

    //         $users_addition_details = UserProfile::where('c_by', Auth::id())->first();

    //         $fields = [
    //             'project_category',
    //             'your_purpose',
    //             'services_offered',
    //             'projects_ongoing',
    //             'ongoing_details',
    //             'labours',
    //             'mobilization',
    //             'strength',
    //             'client_tele',
    //             'customer',
    //             'income_tax',
    //         ];

    //         $filled = 0;

    //         foreach ($fields as $field) {
    //             if (! empty($users_addition_details->$field)) {
    //                 $filled++;
    //             }
    //         }

    //         $additionalProfileCompletion = round(($filled / count($fields)) * 100);
    //     }

    //     // vendor additional details for progress
    //     $vendor_details = 0;
    //     if (Auth::user()->as_a === 'Vendor') {

    //         $vendor_addtional_details = UserProfile::where('c_by', Auth::id())->first();

    //         // Additional profile fields to check
    //         $fields = [

    //             'your_purpose',
    //             'strength',
    //             'client_tele',
    //             'customer',
    //             'delivery_timeline',
    //             'location_catered',

    //         ];

    //         $filled = 0;

    //         foreach ($fields as $field) {
    //             if (! empty($vendor_addtional_details->$field)) {
    //                 $filled++;
    //             }
    //         }

    //         $vendor_details = round(($filled / count($fields)) * 100);
    //     }

    //     // Student additional details for progress
    //     $student_completion = 0;

    //     if (Auth::user()->type_of_names[0] === 'Student') {

    //         $student_details = UserProfile::where('c_by', Auth::id())->first();

    //         // Additional profile fields to check
    //         $fields = [
    //             'professional_status',
    //             'education',
    //             'college',
    //             'aadhar_no',
    //             'pan_no',
    //         ];

    //         $filled = 0;

    //         foreach ($fields as $field) {
    //             if (! empty($student_details->$field)) {
    //                 $filled++;
    //             }
    //         }

    //         $student_completion = round(($filled / count($fields)) * 100);
    //     }

    //     // Student additional details for progress
    //     $working_completion = 0;

    //     if (Auth::user()->type_of_names[0] === 'Working') {

    //         $working_details = UserProfile::where('c_by', Auth::id())->first();

    //         // Additional profile fields to check
    //         $fields = [
    //             'professional_status',
    //             'education',
    //             'designation',
    //             'employment_type',
    //             'experience',
    //             'projects_handled',
    //             'expertise',
    //             'current_ctc',
    //             'notice_period',
    //             'aadhar_no',
    //             'pan_no',
    //         ];

    //         $filled = 0;

    //         foreach ($fields as $field) {
    //             if (! empty($working_details->$field)) {
    //                 $filled++;
    //             }
    //         }

    //         $working_completion = round(($filled / count($fields)) * 100);
    //     }

    //     // gst detail verification

    //     $gst_details = 0;
    //     if (Auth::user()->you_are === 'Business') {

    //         $gst_addtional_details = GstDetails::where('user_id', Auth::id())->first();

    //         // Additional profile fields to check
    //         $fields = [

    //             'gst_verify',
    //             'gst_number',
    //             'name',
    //             'business_legal',
    //             'contact_no',
    //             'email_id',
    //             'pan_no',
    //             'register_date',
    //             'gst_address',
    //             'nature_business',
    //             'annual_turnover',

    //         ];

    //         $filled = 0;

    //         foreach ($fields as $field) {
    //             if (! empty($gst_addtional_details->$field)) {
    //                 $filled++;
    //             }
    //         }

    //         $gst_details = round(($filled / count($fields)) * 100);
    //     }

    //     $com_img = ($users_basic_details->profile_img != null) ? 100 : 0;
    //     $user_type = Auth::user()->you_are;
    //     if ($user_type == 'Business') {
    //         if (Auth::user()->as_a == 'Contractor' || Auth::user()->as_a == 'Consultant') {
    //             $comp = ($profileCompletion + $additionalProfileCompletion + $com_img) / 3;
    //         } elseif (Auth::user()->as_a == 'Vendor') {
    //             $comp = ($profileCompletion + $vendor_details + $com_img + $gst_details) / 4;
    //         }

    //     } elseif ($user_type == 'Professional') {
    //         if (Auth::user()->type_of_names[0] == 'Working') {
    //             $comp = ($profileCompletion + $working_completion + $com_img) / 3;
    //         } elseif (Auth::user()->type_of_names[0] == 'Student') {
    //             $comp = ($profileCompletion + $student_completion + $com_img) / 3;
    //         }
    //     } else {
    //         $comp = ($profileCompletion + $com_img) / 2;
    //     }
    //     $profile_data = [

    //         'profile_completion' => $profileCompletion ?? 0,
    //         'contractor_completion' => $additionalProfileCompletion ?? 0,
    //         'vendor_completion' => $vendor_details ?? 0,
    //         'student_completion' => $student_completion ?? 0,
    //         'working_completion' => $working_completion ?? 0,
    //         'gst_details' => $gst_details ?? 0,
    //         'profile_img' => $com_img,
    //         'completion' => round($comp),
    //     ];

    //     return $profile_data;
    // }

    public function badge_cost(Request $req)
    {

        $badge = $req->badge;

        if ($badge == 5) {
            $type = '5L_badge';
        } elseif ($badge == 10) {
            $type = '10L_badge';
        } elseif ($badge == 15) {
            $type = '15L_badge';
        }
        $cost = Charge::where('category', $type)->latest()->value('charge');

        return response()->json(['success' => true, 'cost' => $cost ?? 0]);

    }
    // public function searchProducts(Request $request)
    // {

    //     $query     = $request->input('q');         // search keyword
    //     $perPage   = (int) $request->input('per_page', 2);
    //     $cursor    = $request->input('cursor');   // for cursor pagination
    //     $location  = $request->input('location'); // user location (if any)

    //     $location = 3;

    //     // 🔹 Base query
    //     $productsQuery = Products::query()
    //         ->where('products.status', 'active');

    //     // 🔹 Apply search mode
    //     if ($query) {
    //         // 🔹 Search mode (with JOIN)
    //         $productsQuery->with('categoryRelation')
    //             ->where(function ($q) use ($query) {
    //                 $q->where('name', 'like', "%{$query}%")
    //                     ->orWhere('brand_name', 'like', "%{$query}%")
    //                     ->orWhere('key_feature', 'like', "%{$query}%")
    //                     ->orWhere('description', 'like', "%{$query}%")
    //                     ->orWhere('specifications', 'like', "%{$query}%")
    //                     ->orWhereHas('categoryRelation', function ($q2) use ($query) {
    //                         $q2->where('value', 'like', "%{$query}%");
    //                     });
    //             })
    //             ->orderByDesc('created_at')
    //             ->orderByDesc('id');
    //     } else {
    //         // 🔹 Feed mode filters
    //         $productsQuery->where('availability', 'In Stock')
    //             ->where('approvalstatus', 'approved')
    //             ->where('created_by', '!=', Auth::id())
    //             // ->where('created_at', '>=', now()->subMonths(6))
    //             ->orderByRaw("
    //             CASE
    //                 WHEN highlighted = 1 AND location = '{$location}' THEN 1
    //                 WHEN highlighted = 1 THEN 2
    //                 WHEN location = '{$location}' THEN 3
    //                 ELSE 4
    //             END
    //         ");
    //     }

    //     // 🔹 Common ordering
    //     $productsQuery->orderByDesc('products.created_at')
    //         ->orderByDesc('products.id');

    //     // 🔹 Cursor pagination
    //     $products = $productsQuery->cursorPaginate(
    //         $perPage,
    //         ['*'],
    //         'cursor',
    //         $cursor
    //     );

    //     return response()->json([
    //         'mode'        => $query ? 'search' : 'feed', // 👈 tell frontend which mode
    //         'success'     => true,
    //         'products'    => $products->items(),
    //         'next_cursor' => $products->nextCursor()?->encode(),
    //         'per_page'    => $perPage
    //     ]);
    //     // $query   = $request->input('q', null);
    //     // $perPage = (int) $request->input('per_page', 2);
    //     // $cursor  = $request->input('cursor', null);

    //     // $productsQuery = Products::query()
    //     //     ->where('status', 'active')
    //     //     ->when($query, function ($q) use ($query) {
    //     //         $q->where(function ($q2) use ($query) {
    //     //             $q2->where('name', 'like', "%{$query}%")
    //     //                 ->orWhere('brand_name', 'like', "%{$query}%");
    //     //         });
    //     //     })
    //     //     // ->orderByDesc('highlighted')
    //     //     ->orderByDesc('created_at')
    //     //     ->orderByDesc('id');

    //     // $products = $productsQuery->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

    //     // return response()->json([
    //     //     'products'    => $products->items(),
    //     //     'next_cursor' => $products->nextCursor()?->encode(),
    //     //     'per_page'    => $perPage,
    //     // ]);
    // }
}
