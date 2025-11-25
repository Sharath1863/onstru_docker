<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Cart;
use App\Models\Charge;
use App\Models\CommentList;
use App\Models\DropdownList;
use App\Models\Follow;
use App\Models\GstDetails;
use App\Models\Jobs;
use App\Models\Notification;
use App\Models\OrderProducts;
use App\Models\PostLike;
use App\Models\Posts;
use App\Models\Products;
use App\Models\ReadyToWork;
use App\Models\Save_post;
use App\Models\SavedProduct;
use App\Models\Service;
use App\Models\UserDetail;
use App\Services\NotificationService;
use Carbon\Carbon;
// use AWS\CRT\HTTP\Request;
use Illuminate\Http\Request;
use Illuminate\Pagination\Cursor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Home_cnt extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    private function notifyUser($userId, $title, $body, $category_id, $com = 'post')
    {
        $this->notificationService->create([
            'category' => $com,
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

    public function home(Request $request)
    {
        // dd("hello");
        $users = UserDetail::where('id', '!=', Auth::id())->take(10)->get();
        $category = DropdownList::where('dropdown_id', 3)->get();
        $locations = DropdownList::where('dropdown_id', 1)->get();
        $serviceTypes = DropdownList::where('dropdown_id', 5)->pluck('value', 'id');
        $post_count = Posts::where('created_by', Auth::id())->where('status', 'active')->count();
        $products = Products::withCount('reviews')
            ->withAvg('reviews', 'stars')
            ->latest()
            ->get();
        $jobs = Jobs::all();
        // $suggest_users = UserDetail::latest()
        //     ->where('id', '!=', Auth::id())
        //     ->take(20)->get();

        $user = Auth::user();

        // dd($user->as_a);

        // already followed users
        $followedIds = $user->following()->pluck('user_detail.id');

        // 1) First 7 with same user_type
        $sameType = UserDetail::where('id', '!=', $user->id)
            ->where('as_a', $user->as_a)
            ->whereNotIn('id', $followedIds)
            ->latest()
            ->take(7)
            ->get();

        // 2) Next 7 with same location (but different type)
        $sameLocation = UserDetail::where('id', '!=', $user->id)
            ->where('location', $user->location)
            ->where('as_a', '!=', $user->as_a)
            ->whereNotIn('id', $followedIds)
            ->latest()
            ->take(7)
            ->get();

        // 3) Last 6 from everyone else
        $others = UserDetail::where('id', '!=', $user->id)
            ->where('as_a', '!=', $user->as_a)
            ->where('location', '!=', $user->location)
            ->whereNotIn('id', $followedIds)
            ->latest()
            ->take(6)
            ->get();

        // merge results
        $suggest_users = $sameType
            ->merge($sameLocation)
            ->merge($others);

        $totalNeeded = 20;

        if ($suggest_users->count() < $totalNeeded) {
            $needed = $totalNeeded - $suggest_users->count();

            $more = UserDetail::where('id', '!=', $user->id)
                ->whereNotIn('id', $followedIds->merge($suggest_users->pluck('id')))
                ->inRandomOrder()
                ->take($needed)
                ->get();

            if ($more->isEmpty()) {
                // nothing left in DB to suggest
                // just return what we already have
                // return $suggest_users;
            }

            $suggest_users = $suggest_users->merge($more);
        }

        $suggest_users = $suggest_users->where('status', 'active')->shuffle();
        $cartItems = [];
        if (Auth::check()) {
            $cartItems = Cart::where('c_by', Auth::id())
                ->pluck('product_id')
                ->toArray();
        }
        $readyToWork = ReadyToWork::where('created_by', Auth::id())->first();
        $following = Auth::user()->following()->latest('follows.created_at')->paginate(20);
        $followers = Auth::user()->followers()->latest('follows.created_at')->paginate(20);
        $savedProducts = [];
        if (Auth::check()) {
            $savedProducts = SavedProduct::where('c_by', Auth::id())
                ->pluck('product_id')
                ->toArray();
        }
        $gstverified = GstDetails::where('user_id', Auth::id())->where('gst_verify', 'yes')->first();
        if ($gstverified === null) {
            $gstverified = 'no';
        } else {
            $gstverified = 'yes';
        }

        // badge code
        $user = Auth::user();

        $totalEarnings = 0;

        $badges = Charge::whereIn('category', ['5L_badge', '10L_badge', '15L_badge'])
            ->latest('id')  // optional: latest per category might need subquery
            ->get()
            ->keyBy('category');
        $badge_5L = ($badges['5L_badge']->charge ?? 0) * 1.18;
        $badge_10L = ($badges['10L_badge']->charge ?? 0) * 1.18;
        $badge_15L = ($badges['15L_badge']->charge ?? 0) * 1.18;

        // $badge_5L = charge::where('category', '5L_badge')->latest()->value('charge') * 1.18;
        // $badge_10L = Charge::where('category', '10L_badge')->latest()->value('charge') * 1.18;
        // $badge_15L = charge::where('category', '15L_badge')->latest()->value('charge') * 1.18;
        if ($user->as_a === 'Vendor') {

            // dd($user->id);
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->startOfMonth()->addDays(14)->endOfDay();

            // $totalEarnings = DB::table('order_products')
            //     ->join('products', 'order_products.product_id', '=', 'products.id')
            //     ->where('products.created_by', $user->id)
            //     ->whereBetween('order_products.created_at', [$start, $end])
            //     ->selectRaw('SUM(order_products.quantity * products.sp) as total')
            //     ->value('total') ?? 0;
            $totalEarnings = OrderProducts::whereHas('product', function ($query) {
                $query->where('created_by', Auth::id());
            })
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum(DB::raw('(base_price * quantity) * (1 + (tax / 100))'));
        }
        // Log::info('Total Earnings (with tax) for Current Month: ' . $totalEarnings);

        // Log::info('Decoded posts cursor: '.json_encode($postsCursor));
        $location = $user->location;
        // Safer ordering: created_at + id

        $postsCursorObj = $jobsCursorObj = $productsCursorObj = $serviceCursorObj = null;

        $postsArr = $jobsArr = $productsArr = $serviceArr = [];

        // Cursors from frontend
        if ($request->ajax()) {
            $postsCursor = $request->input('posts_cursor') ?? null;
            $jobsCursor = $request->input('jobs_cursor') ?? null;
            $productsCursor = $request->input('products_cursor') ?? null;
            $serviceCursor = $request->input('service_cursor') ?? null;
        } else {
            $postsCursor = $request->input('posts_cursor') ?? 'empty';
            $jobsCursor = $request->input('jobs_cursor') ?? 'empty';
            $productsCursor = $request->input('products_cursor') ?? 'empty';
            $serviceCursor = $request->input('service_cursor') ?? 'empty';
        }
        // dd($postsCursor, $jobsCursor, $productsCursor, $serviceCursor);

        $postsArr = $jobsArr = $productsArr = $serviceArr = collect();

        $next = [
            'posts' => null,
            'jobs' => null,
            'products' => null,
            'services' => null,
        ];

        // dd($postsCursor);

        // Posts

        // if ($postsCursor != null || $postsCursor == 'empty') {
        //     // $postsArr = collect();

        //     $query = Posts::where('status', 'active')
        //         ->where('created_by', '!=', Auth::id());

        //     // If client sent last_id, fetch older posts
        //     if ($postsCursor != null && $postsCursor != 'empty') {
        //         $query->where('id', '<', $postsCursor);
        //     }

        //     $postsPaginator = $query->inRandomOrder() // ✅ random each request
        //         ->take(24)        // ✅ limit results manually
        //         ->get();
        //     // ->orderByDesc('created_at')
        //     // ->cursorPaginate(24, ['*'], 'posts_cursor', $postsCursor);

        //     // Transform for flags (liked, saved, etc.)
        //     $postsPaginator->map(function ($post) {
        //         $post->is_liked = $post->likedByAuth !== null;
        //         $post->is_saved = $post->post_save !== null;
        //         $post->is_follow = $post->user ? Auth::user()->isFollowing($post->user) : false;
        //         $post->is_reported = $post->post_report !== null;
        //         $post->type = 'post';

        //         return $post;
        //     });

        //     // $postsArr = collect($postsPaginator->items())->map(fn ($x) => tap($x, fn ($i) => $i->type = 'post'));

        //     // Determine next last ID
        //     $postsArr = $postsPaginator->last()?->id;
        //     // dd($postsArr);

        //     // $postsPaginator->getCollection()->transform(function ($post) {
        //     //     $post->is_liked = $post->likedByAuth !== null;
        //     //     $post->is_saved = $post->post_save !== null;
        //     //     $post->is_follow = $post->user ? Auth::user()->isFollowing($post->user) : false;

        //     //     $post->is_reported = $post->post_report !== null;

        //     //     return $post;
        //     // });

        //     // $postsArr = collect($postsPaginator->items())->map(fn ($x) => tap($x, fn ($i) => $i->type = 'post'));

        //     // $next['posts'] = $postsPaginator->nextCursor()?->encode();
        //     $next['posts'] = $postsArr;
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

            $postsPaginator = $posts;
            $next['posts'] = $posts->random()->id; // 👈 next cursor is min ID of current batch
        } else {
            // if ($postsCursor === 'empty') {
            //     Log::info('Posts cursor is empty string');
            // } else {
            //     Log::info('Posts cursor is null');
            //     $query = Posts::where('status', 'active')
            //         ->where('created_by', '!=', Auth::id());
            // }

        }

        if (($jobsCursor != null || $jobsCursor == 'empty') && ($user->you_are != 'Business')) {

            // Jobs
            $jobsPaginator = Jobs::where('created_by', '!=', Auth::id() ?? 0)
                // ->orderByRaw('
                //     CASE
                //         WHEN highlighted = 1 AND location = ? THEN 1
                //         WHEN highlighted = 1 THEN 2
                //         WHEN location = ? THEN 3
                //         ELSE 4
                //     END, created_at DESC, id DESC
                // ', [$location, $location])
                ->cursorPaginate(5, ['*'], 'jobs_cursor', $jobsCursor);
            $jobsArr = collect($jobsPaginator->items())->map(fn ($x) => tap($x, fn ($i) => $i->type = 'job'));

            $next['jobs'] = $jobsPaginator->nextCursor()?->encode();
        }

        if ($productsCursor != null || $productsCursor == 'empty') {

            // Products
            $productsPaginator = Products::withCount('reviews')
                ->withAvg('reviews', 'stars')
                ->where('created_by', '!=', Auth::id() ?? 0)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                // ->orderByRaw('
                //     CASE
                //         WHEN highlighted = 1 AND location = ? THEN 1
                //         WHEN highlighted = 1 THEN 2
                //         WHEN location = ? THEN 3
                //         ELSE 4
                //     END, created_at DESC, id DESC
                // ', [$location, $location])
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->cursorPaginate(5, ['*'], 'products_cursor', $productsCursor);
            $productsArr = collect($productsPaginator->items())->map(fn ($x) => tap($x, fn ($i) => $i->type = 'product'));

            $next['products'] = $productsPaginator->nextCursor()?->encode();
        }

        if ($serviceCursor != null || $serviceCursor == 'empty') {
            // Services
            try {
                $servicePaginator = Service::with('serviceType:id,value')
                    ->where('created_by', '!=', Auth::id() ?? 0)
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    //     ->orderByRaw('
                    //     CASE
                    //         WHEN highlighted = 1 AND location = ? THEN 1
                    //         WHEN highlighted = 1 THEN 2
                    //         WHEN location = ? THEN 3
                    //         ELSE 4
                    //     END, created_at DESC, id DESC
                    // ', [$location, $location])
                    ->cursorPaginate(5, ['*'], 'service_cursor', $serviceCursor);
            } catch (\Exception $e) {
                Log::error('Service Query Failed', ['error' => $e->getMessage()]);

                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
            $serviceArr = collect($servicePaginator->items())->map(fn ($x) => tap($x, fn ($i) => $i->type = 'service'));

            $next['services'] = $servicePaginator->nextCursor()?->encode();
        }

        // Prepare next cursors
        // $next = [
        //     'posts' => ($postsPaginator->nextCursor()?->encode()) ?? null,
        //     'jobs' => ($jobsPaginator->nextCursor()?->encode()) ?? null,
        //     'products' => ($productsPaginator->nextCursor()?->encode()) ?? null,
        //     'services' => ($servicePaginator->nextCursor()?->encode()) ?? null,
        // ];

        // $postsData = $postsArr->all(); // convert collection to array
        $postsData = $postsPaginator->all() ?? []; // convert collection to array
        // shuffle($postsData);

        // Prepare data and merge pattern
        $data = [
            'post' => $postsData ?? [],
            'job' => $jobsArr->all() ?? [],
            'product' => $productsArr->all() ?? [],
            'service' => $serviceArr->all() ?? [],
        ];

        if ($user->you_are !== 'Business') {
            // remove jobs from data
            $pattern = [
                ['type' => 'post', 'count' => 6],
                ['type' => 'job', 'count' => 5],
                ['type' => 'post', 'count' => 6],
                ['type' => 'product', 'count' => 5],
                ['type' => 'post', 'count' => 6],
                ['type' => 'service', 'count' => 5],
            ];
        } else {

            $pattern = [
                ['type' => 'post', 'count' => 6],
                ['type' => 'product', 'count' => 5],
                ['type' => 'post', 'count' => 6],
                ['type' => 'service', 'count' => 5],
                ['type' => 'post', 'count' => 6],
            ];
        }
        $merged = collect();
        foreach ($pattern as $block) {
            $type = $block['type'];
            $count = $block['count'];

            if (! empty($data[$type])) {
                $slice = array_splice($data[$type], 0, $count);
                $merged = $merged->merge($slice);
            }
        }

        // dd($merged->all());

        if ($request->ajax()) {
            // log::info('1 request'.json_encode($merged, JSON_PRETTY_PRINT));
            // log::info('AJAX request - returning JSON with '.json_encode($merged, JSON_PRETTY_PRINT).' items');

            return response()->json([
                // dd($merged),
                'feed' => view('home.home-center', ['posts_data' => $merged])->render(),
                'next' => $next,
                'merge' => $merged,
            ]);
        }

        // dd($postsCursor, $jobsCursor, $productsCursor, $serviceCursor);

        return view('home.index', ['merged' => $merged, 'next' => $next], compact('products', 'category', 'locations', 'serviceTypes', 'savedProducts', 'suggest_users', 'following', 'followers', 'cartItems', 'jobs', 'users', 'readyToWork', 'totalEarnings', 'badge_5L', 'badge_10L', 'badge_15L', 'gstverified', 'post_count', 'merged'));
    }

    public function toggle_like(Request $request)
    {
        $post = Posts::findOrFail($request->post_id);

        if (PostLike::where('post_id', $request->post_id)->where('user_id', Auth::id())->exists() && $request->action === 'like') {
            return response()->json([
                'success' => false,
                'message' => 'You have already liked this post.',
            ], 200);
        }

        // dd($request->all());

        if ($request->action === 'like') {

            $post->increment('like_cnt');
            // dd($post);

            $action = 'like';
            // insert like record (prevent duplicates with firstOrCreate)
            DB::table('post_like')->updateOrInsert(
                [
                    'post_id' => $request->post_id,
                    'user_id' => Auth::id() ?? 2,
                ],
                [
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $type = strtolower($post->file_type);

            if ($type == 'video') {
                $type = 'Reel';
            } else {
                $type = 'Post';
            }
            $title = 'Liked on Your '.$type;

            $check_like = Notification::where('category', 'post')
                ->where('category_id', $post->id)
                ->where('reciever', $post->created_by)
                ->where('title', 'like', '%Liked%')
                ->where('c_by', Auth::id())
                ->exists();
            // dd($check_like);
            // do nothing
            if (! $check_like) {
                if ($post->user->id != Auth::id()) {
                    $this->notifyUser(
                        $post->created_by,
                        $title,
                        'liked your '.$type,
                        $post->id,
                        'post'
                    );
                    if ($post->user && ($post->user->web_token || $post->user->mob_token)) {
                        $data = [
                            'web_token' => $post->user->web_token,
                            'mob_token' => $post->user->mob_token ?? null,
                            'title' => $title,
                            'body' => 'liked your '.$type,
                            'id' => $post->id,
                            'link' => route('notification'),
                        ];
                        $this->notificationService->token($data);
                    }
                }
            }
        } else {
            // Prevent negative counts
            if ($post->like_cnt > 0) {
                $post->decrement('like_cnt');
            }
            $action = 'unlike';

            // delete like record
            DB::table('post_like')
                ->where('post_id', $request->post_id)
                ->where('user_id', Auth::id() ?? 2)
                ->delete();
        }

        if ($post->like_cnt >= 1000) {
            $cnt = number_format($post->like_cnt / 1000, 1).'k';
        } else {
            $cnt = $post->like_cnt;
        }

        return response()->json([
            'success' => true,
            'action' => $action,
            'likes_count' => $cnt,
        ]);
    }

    // function for follow and unfollow

    public function follow(Request $req)
    {

        $user = Auth::id() ?? 2;

        $followed = Follow::where('follower_id', $user)
            ->where('following_id', $req->user_id)
            ->first();
        // if ($followed) {
        //     return response()->json(['success' => false, 'message' => 'You are already following this user.']);
        // }

        if ($req->action == 'follow') {

            $action = 'followed';

            Follow::create([
                'follower_id' => $user,
                'following_id' => $req->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {

            $action = 'unfollowed';

            Follow::where('follower_id', $user)
                ->where('following_id', $req->user_id)
                ->delete();
        }

        return response()->json(['success' => true, 'action' => $action]);
    }

    // function for like list
    public function getLikesList(Request $req)
    {
        $users = DB::table('post_like')
            ->join('user_detail as users', 'post_like.user_id', '=', 'users.id')
            ->where('post_like.post_id', $req->post_id)
            ->select('users.id', 'users.name', 'users.user_name', 'users.badge', 'users.profile_img', 'users.as_a')
            ->get();

        if ($req->header('Authorization')) {

            if ($users->isEmpty()) {
                return response()->json(['status' => true, 'message' => 'No Likes Yet...!', 'data' => []], 200);
            }

            return response()->json(['status' => true, 'message' => 'Like List', 'data' => $users], 200);
        }

        // Render HTML manually (instead of partials)
        $html = '';
        if ($users->count() > 0) {
            foreach ($users as $user) {
                $profileImg = $user->profile_img
                    ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.$user->profile_img
                    : asset('assets/images/Avatar.png');
                $badgeImg = asset('assets/images/Badge_'.$user->badge.'.png');
                $profile_url = url('user-profile/'.$user->id);
                $html .= '
            <div class="modal-user like-modal mb-2">
                <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar-div-30 position-relative">
                            <img src="'.$profileImg.'" class="avatar-30" alt="">
                            <img src="'.$badgeImg.'" class="badge-30" alt="">
                        </div>
                        <div class="user-content">
                            <h6 class="mb-1 text-dark">'.e($user->name).'</h6>
                            <h6 class="bio">'.e($user->user_name).'</h6>
                        </div>
                    </div>
                    ';
                if (Auth::id() != $user->id) {
                    $html .= '
                    <a href="'.$profile_url.'">
                        <button class="followersbtn">View Profile</button>
                    </a>';
                } else {
                    $html .= '';
                }
                $html .= '
                </div>
            </div>';
            }
        } else {
            $html = '<h6 class="text-center">No Likes Yet...!</h6>';
        }

        return response()->json(['html' => $html]);
    }

    // get comment list
    public function getCommentList(Request $req, $returnHtmlOnly = false)
    {

        $comments = DB::table('comment_list')
            ->join('user_detail as users', 'comment_list.user_id', '=', 'users.id')
            ->join('posts', 'comment_list.post_id', '=', 'posts.id')
            ->where('comment_list.post_id', $req->post_id)
            ->latest('comment_list.created_at')
            ->select('comment_list.id as com_id', 'users.id', 'users.name', 'users.user_name', 'users.badge', 'users.profile_img', 'comment_list.comment', 'comment_list.created_at as c_at', 'posts.created_by as post_owner', 'comment_list.user_id as created_by')
            ->get();

        if ($req->header('Authorization')) {

            if ($comments->isEmpty()) {
                return response()->json(['status' => true, 'message' => 'No Comments Yet...!', 'data' => []], 200);
            }

            return response()->json(['status' => true, 'message' => 'Comment List', 'data' => $comments], 200);
        }

        $html = '';

        // if ($comments->count() > 0) {
        foreach ($comments as $comment) {
            $profileImg = $comment->profile_img
                ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.$comment->profile_img
                : asset('assets/images/Avatar.png');
            $badgeImg = asset('assets/images/Badge_'.$comment->badge.'.png');
            $html .= '
                <input type="hidden" id="post_comment_id" value="'.$req->post_id.'">
                <div class="modal-user comment-modal mb-3">
                    <div class="dropdown">
                        <div class="user-content d-flex align-items-start justify-content-between" data-bs-toggle="dropdown" aria-expanded="false" id="comment-'.$comment->com_id.'">
                            <div class="d-flex align-items-start justify-content-start column-gap-2">
                                <div class="avatar-div-30 position-relative">
                                    <img src="'.$profileImg.'" class="avatar-30" alt="">
                                    <img src="'.$badgeImg.'" class="badge-30" alt="">
                                </div>
                                <div>
                                    <h5 class="mb-1">'.e($comment->user_name).'</h5>
                                    <h6>'.e($comment->comment).'</h6>
                                </div>
                            </div>
                            <h6>'.e(Carbon::parse($comment->c_at)->diffForHumans(Carbon::now(), true)).'</h6>
                        </div>
                        <ul class="dropdown-menu">';
            // if (Auth::id() == $comment->post_owner) {
            //     $html .= '
            //     <li class="mb-1">
            //      <a class="dropdown-item comment-action"
            //             data-id="'.$comment->com_id.'"
            //             data-action="delete"
            //             href="#">
            //                 <i class="fas fa-trash text-danger pe-1"></i> Delete
            //             </a>
            //     </li>';
            // }

            if ((Auth::id() == $comment->created_by) || (Auth::id() == $comment->post_owner)) {
                $html .= '
                    <li>
                        <a class="dropdown-item comment-action" data-id="'.$comment->com_id.'" 
                            data-action="delete"  href="#">
                            <i class="fas fa-trash text-danger pe-1"></i> Delete
                        </a>';
            }

            $html .= '
                        <a class="dropdown-item"  href="'.route('user-profile', $comment->created_by).'">
                            <i class="fas fa-copy text-primary pe-1"></i> Profile
                        </a>';

            $html .= '

                    </li>
                </ul>
             </div>
            </div>';
        }
        // } else {
        //     $html = '<h6 class="text-center">No Comments Yet...!</h6>';
        // }

        // If called internally → return just HTML
        if ($returnHtmlOnly) {
            return $html;
        }

        $post_det = Posts::find($req->post_id);

        $user_det = UserDetail::where('id', $post_det->created_by)->first();

        $save_data = DB::table('save_post')->where('post_id', $req->post_id)->where('user_id', Auth::id())->exists();

        // dd($user_det->user_name);

        return response()->json(['html' => $html, 'post_id' => $req->post_id, 'post_cby' => $user_det->user_name, 'post_cby_id' => $user_det->id, 'post_cby_img' => $user_det->profile_img, 'post_cby_badge' => $user_det->badge, 'post_like_cnt' => $post_det->like_cnt, 'save_post' => $save_data]);
    }

    public function storeComment(Request $req)
    {
        $commentExists = CommentList::where('user_id', Auth::id() ?? 2)
            ->where('post_id', $req->post_id)
            ->exists();

        // if ($commentExists) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'You have already commented on this post.',
        //     ], 200);
        // }
        // ✅ Create validator
        $validator = Validator::make($req->all(), [
            'post_id' => 'required|exists:posts,id',
            'comment' => 'required|string|min:1|max:500',
        ], [
            'post_id.required' => 'Post ID is required.',
            'post_id.exists' => 'Invalid post.',
            'comment.required' => 'Comment cannot be empty.',
            'comment.max' => 'Comment cannot exceed 500 characters.',
        ]);

        // ❌ If validation fails, return JSON error
        if ($validator->fails()) {

            if ($req->header('Authorization')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $last_id = DB::table('comment_list')->insertGetId([
            'post_id' => $req->post_id,
            'user_id' => Auth::id() ?? 2,
            'comment' => $req->comment,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $comment = Posts::findOrFail($req->post_id);
        $comment->increment('com_cnt');
        $type = strtolower($comment->file_type);

        if ($type == 'video') {
            $type = 'Reel';
        } else {
            $type = 'Post';
        }

        $title = 'Commented on Your '.$type;

        $this->notifyUser(
            $comment->created_by,
            $title,
            'commented on your '.$type,
            $comment->id,
            'comment'
        );
        if ($comment->user && ($comment->user->web_token || $comment->user->mob_token)) {
            $data = [
                'web_token' => $comment->user->web_token,
                'mob_token' => $comment->user->mob_token ?? null,
                'title' => $title,
                'body' => 'commented on your '.$type,
                'id' => $comment->id,
                'link' => route('notification'),
            ];
            $this->notificationService->token($data);
        }

        if ($comment->com_cnt >= 1000) {
            $cnt = number_format($comment->com_cnt / 1000, 1).'k';
        } else {
            $cnt = $comment->com_cnt;
        }

        $html = $this->getCommentList($req, true);

        if ($req->header('Authorization')) {
            return response()->json(['success' => true, 'message' => 'Comment Added Successfully', 'data' => $last_id], 200);
        }

        return response()->json(['html' => $html, 'com_cnt' => $cnt]);
    }

    // toggle save
    public function toggle_save(Request $request)
    {
        $post = Posts::findOrFail($request->post_id);

        if (Save_post::where('post_id', $request->post_id)->where('user_id', Auth::id() ?? 2)->exists() && $request->action === 'save') {
            return response()->json([
                'success' => false,
                'message' => 'You have already saved this post.',
            ], 200);
        }

        if ($request->action === 'save') {
            $action = 'save';
            DB::table('save_post')->updateOrInsert(
                [
                    'post_id' => $request->post_id,
                    'user_id' => Auth::id() ?? 2,
                ],
                [
                    'post_type' => $post->file_type,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        } else {
            $action = 'unsave';

            // delete like record
            DB::table('save_post')
                ->where('post_id', $request->post_id)
                ->where('user_id', Auth::id() ?? 2)
                ->delete();
        }

        return response()->json([
            'success' => true,
            'action' => $action,
            // 'likes_count' => $cnt
        ]);
    }

    // toogle report
    public function toggle_report(Request $request)
    {
        $action = $request->input('action');
        $commentId = $request->input('comment_id');

        // dd($action, $commentId);
        $message = $request->input('message', null);

        if ($action === 'delete') {
            $comment = CommentList::findOrFail($commentId);

            $post_data = Posts::findOrFail($comment->post_id);

            if ($post_data->com_cnt > 0) {
                $post_data->decrement('com_cnt');
            }

            if ($request->header('Authorization')) {
                if (Auth::id() != $comment->post->created_by || Auth::id() != $comment->user_id) {
                    $comment->delete();

                    return response()->json(['success' => true, 'message' => 'Comment deleted'], 200);
                }
            }

            if ((Auth::id() == $comment->post->created_by) || (Auth::id() == $comment->user_id)) {
                $comment->delete();

                return response()->json(['success' => true, 'message' => 'Comment deleted']);
            }

            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        if (($action === 'report') || ($action === 'report post')) {

            $type = ($action === 'report') ? 'comment' : 'post';

            DB::table('report')->insert([
                'type' => $type,
                'f_id' => $commentId,
                'user_id' => Auth::id(),
                'message' => $message,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => $type.' reported']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid action']);
    }

    public function getShareList(Request $req)
    {
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

        // Log::info("with:\n".print_r($users->toArray(), true));

        $html = '';

        $link = '';
        $html .= '<input type="hidden" id="post_id_share" value="'.$req->post_id.'">
                    <input type="hidden" id="share_type" value="'.$req->share_type.'">';
        if ($users->count() > 0) {
            foreach ($users as $index => $user) {
                $profileImg = $user->profile_img
                    ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.$user->profile_img
                    : asset('assets/images/Avatar.png');
                $badgeImg = asset('assets/images/Badge_'.$user->badge.'.png');

                $html .= '
                        
                    <div class="modal-user share-modal mb-3" data-index="'.$user->id.'">
                        <div class="d-flex align-items-center justify-content-center flex-column gap-1">
                            <div class="avatar-div-40 position-relative">
                                <img src="'.$profileImg.'" class="avatar-40" alt="">
                                <img src="'.$badgeImg.'" class="badge-40" alt="">
                                <span class="tick-icon d-none">
                                    <i class="fas fa-check"></i>
                                </span>
                            </div>
                            <div class="user-content mt-0">
                                <h6 class="text-center long-text" id="'.e($user->user_name).'">'.e($user->user_name).'</h6>
                            </div>
                        </div>
                    </div>
                    
                    ';
            }

            $post = Posts::find($req->post_id);
            $base_url = env('BASE_URL');
            $post_url_map = [
                'post' => "user-profile/{$post->created_by}/{$post->id}/{$post->file_type}",
                'product' => "product-details/{$req->post_id}",
                'job' => "job-details/{$req->post_id}",
                'service' => "service-details/{$req->post_id}",
            ];

            $link_text_map = [
                'post' => 'Post Shared',
                'product' => 'Product Shared',
                'job' => 'Job Shared',
                'service' => 'Service Shared',
            ];

            $path = $post_url_map[$req->share_type] ?? "user-profile/{$post->id}";
            $link_text = $link_text_map[$req->share_type] ?? 'Profile Shared';

            // Build the full (real) URL
            $full_url = "{$base_url}{$path}";

            // Encode only when used in query params
            $encoded_url = urlencode($full_url);
            $encoded_text = urlencode($link_text);

            // Build share URLs
            $fb_url = "https://www.facebook.com/sharer/sharer.php?u={$encoded_url}";
            $twitter_url = "https://twitter.com/intent/tweet?text={$encoded_text}&url={$encoded_url}";
            $telegram_url = "https://t.me/share/url?url={$encoded_url}&text={$encoded_text}";
            $whatsapp_url = "https://wa.me/?text={$encoded_text}%0A{$encoded_url}";

            if ($link_text == 'Post Shared') {
                $download_url = "https://onstru-social.s3.ap-south-1.amazonaws.com/{$post->file[0]}";
            }

            $link .= '
                    <div class="share-btns mx-auto">
                        <button class="sharebtn share-copy" data-bs-toggle="tooltip" data-bs-title="Copy Link" data-link="'.$base_url.$path.'">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                    ';
            // if($link_text =='Post Shared'){
            //     $link .= '
            //         <div class="share-btns mx-auto">
            //             <a  href="' . $download_url . '" class="sharebtn share-download" download>
            //                 <button class="sharebtn" data-bs-toggle="tooltip" data-bs-title="Download">
            //                     <i class="fas fa-download"></i>
            //                 </button>
            //             </a>
            //         </div>';
            // }
            $link .= '
                    <div class="share-btns mx-auto">
                        <a href="'.$whatsapp_url.'" class="share-whatsapp" target="_blank">
                            <button class="sharebtn" data-bs-toggle="tooltip" data-bs-title="WhatsApp">
                                <i class="fa-brands fa-whatsapp"></i>
                            </button>
                        </a>
                    </div>
                    <div class="share-btns mx-auto">
                        <a href="'.$fb_url.'" class="share-facebook" target="_blank">
                            <button class="sharebtn" data-bs-toggle="tooltip" data-bs-title="Facebook">
                                <i class="fa-brands fa-facebook-f"></i>
                            </button>
                        </a>
                    </div>
                    <div class="share-btns mx-auto">
                        <a href="'.$telegram_url.'"  class="share-telegram" target="_blank">
                            <button class="sharebtn" data-bs-toggle="tooltip" data-bs-title="Telegram">
                                <i class="fa-brands fa-telegram"></i>
                            </button>
                        </a>
                    </div>
                    <div class="share-btns mx-auto">
                        <a href="'.$twitter_url.'" class="share-twitter" target="_blank">
                            <button class="sharebtn" data-bs-toggle="tooltip" data-bs-title="X - Twitter">
                                <i class="fa-brands fa-x-twitter"></i>
                            </button>
                        </a>
                    </div>
                    <div class="share-btns mx-auto">
                        <button class="sharebtn share-more" data-bs-toggle="tooltip" data-bs-title="More" data-link="'.$base_url.$path.'" data-text="'.$link_text.'">
                            <i class="fas fa-ellipsis-vertical"></i>
                        </button>
                    </div>
               ';
        } else {
            $html = '<h6 class="text-center">No Followings Yet...!</h6>';
        }

        return response()->json(['html' => $html, 'link' => $link, 'post_id' => $req->post_id ?? null, 'share_type' => $req->share_type ?? null]);
    }

    public function buyBadge(Request $request)
    {
        $request->validate([
            'badge' => 'required|in:5,10,15',
        ]);

        $user = Auth::user();

        // Optional: Check wallet balance (if badge costs ₹1000)
        if ($request->badge == 5) {
            $charge = charge::where('category', '5L_badge')->latest()->value('charge') * 1.18;
        } elseif ($request->badge == 10) {
            $charge = Charge::where('category', '10L_badge')->latest()->value('charge') * 1.18;
        } elseif ($request->badge == 15) {
            $charge = Charge::where('category', '15L_badge')->latest()->value('charge') * 1.18;
        }

        if ($user->balance < $charge) {
            if ($request->header('Authorization')) {
                return response()->json(['status' => false, 'message' => 'Insufficient wallet balance. Please recharge.'], 200);
            }

            return back()->with('error', 'Insufficient wallet balance. Please recharge.');
        }

        // Deduct wallet amount
        $user->balance -= $charge;
        $user->badge = $request->badge;
        $user->save();
        Badge::create([
            'badge' => $request->badge,
            'amount' => $charge,
            'status' => 'active',
            'created_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'message' => 'Badge purchased successfully!', 'badge' => $request->badge], 200);
        }

        return back()->with('success', 'Badge purchased successfully!');
    }
}
