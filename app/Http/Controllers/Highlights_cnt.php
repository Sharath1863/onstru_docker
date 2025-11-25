<?php

namespace App\Http\Controllers;

use App\Models\Click;
use App\Models\JobApplied;
use App\Models\JobBoost;
use App\Models\Jobs;
use App\Models\Posts;
use App\Models\ProductBoost;
use App\Models\Products;
use App\Models\Service;
use App\Models\ServiceBoost;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Highlights_cnt extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    private function notifyUser($userId, $title, $body, $category_id)
    {
        $this->notificationService->create([
            'category' => 'service',
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

    public function highlight_products()
    {
        $userId = Auth::id();

        $boostClicks = ProductBoost::whereHas('product', function ($query) use ($userId) {
            $query->where('created_by', $userId);
        })
            ->where('type', 'click')
            ->sum('click');

        $highlightedClicks = Products::where('created_by', $userId)
            ->where('highlighted', 1)
            ->sum('click');

        $timesClicked = $boostClicks - $highlightedClicks;

        $totalSpend = ProductBoost::whereHas('product', function ($query) use ($userId) {
            $query->where('created_by', $userId);
        })
            ->where('type', 'click')
            ->get()
            ->sum(function ($boost) {
                return $boost->click * $boost->amount;
            });

        $totalHighlights = ProductBoost::whereHas('product', function ($q) use ($userId) {
            $q->where('created_by', $userId);
        })
            ->where('type', 'click')
            ->count();

        // $boostedProducts = Products::where('created_by', $userId)
        //     ->whereHas('boosts')
        //     ->where('type', 'click')
        //     ->with('categoryRelation:id,value')
        //     ->get();
        $boostedProducts = Products::where('created_by', $userId)
            ->whereHas('boosts', function ($query) {
                $query->where('type', 'click');
            })
            ->with(['categoryRelation:id,value'])
            ->get();

        if (request()->header('Authorization')) {

            $data = [
                'totalSpend' => $totalSpend,
                'boostedProducts' => $boostedProducts,
                'totalHighlights' => $totalHighlights,
                'timesClicked' => $timesClicked,
                'highlightedClicks' => $highlightedClicks,
                'boostClicks' => $boostClicks,

            ];

            return response()->json(['success' => true, 'data' => $data]);
        }

        return view('highlights.products.index', compact('totalSpend', 'boostedProducts', 'totalHighlights', 'timesClicked', 'highlightedClicks', 'boostClicks'));
    }

    public function view_products(Request $request, $id = null)
    {
        if ($request->product_id) {

            $id = $request->product_id;
            // dd($id);
        }

        // dd($id);

        $userId = Auth::id();
        $product = Products::with([
            'boosts' => function ($q) {
                $q->where('type', 'click')
                    ->orderBy('created_at', 'desc');
            },
            'clicks.user',
        ])->findOrFail($id);

        // Decode images JSON into array
        $subImages = json_decode($product->image ?? '[]', true);

        // If it's associative (image1, image2, ...), take only the values
        if (! empty($subImages) && is_array($subImages)) {
            $subImages = array_values($subImages);
        } else {
            $subImages = [];
        }

        // Append video (if exists)
        if (! empty($product->video)) {
            $subImages[] = $product->video;
        }

        // Append cover image (if exists)
        if (! empty($product->cover_img)) {
            $subImages[] = $product->cover_img;
        }

        // Now you have images + video in one array
        $product->all_media = $subImages;
        $product->total_clicked = $product->boosts->sum('click');

        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $product]);
        }

        return view('highlights.products.view', compact('product'));
    }

    public function highlight_services(Request $req)
    {
        $userId = Auth::id();
        $boostClicks = ServiceBoost::whereHas('service', function ($query) use ($userId) {
            $query->where('created_by', $userId);
        })
            ->where('type', 'click')
            ->sum('click');

        $highlightedClicks = Service::where('created_by', $userId)
            ->where('highlighted', 1)
            ->sum('click');

        $timesClicked = $boostClicks - $highlightedClicks;

        $totalSpend = ServiceBoost::whereHas('service', function ($query) use ($userId) {
            $query->where('created_by', $userId);
        })
            ->where('type', 'click')
            ->get()
            ->sum(function ($boost) {
                return $boost->amount;
                // return $boost->click * $boost->amount;
            });

        $totalHighlights = ServiceBoost::whereHas('service', function ($q) use ($userId) {
            $q->where('created_by', $userId);
        })
            ->where('type', 'click')
            ->count();

        $boostedService = Service::with([
            'serviceType:id,value',
            'locationRelation:id,value',
            'boosts' => function ($q) {
                $q->where('type', 'click')
                    ->orderBy('created_at', 'desc');
            },
        ])->where('created_by', $userId)
            ->whereHas('boosts')       // must have boosts
            // ->whereHas('serviceType')  // must have serviceType
            ->get();

        // dd($boostedService);

        if ($req->header('Authorization')) {

            $data = [
                'totalSpend' => $totalSpend,
                'boostedService' => $boostedService,
                'totalHighlights' => $totalHighlights,
                'timesClicked' => $timesClicked,
                'highlightedClicks' => $highlightedClicks,
                'boostClicks' => $boostClicks,

            ];

            return response()->json(['success' => true, 'data' => $data]);
        }

        return view('highlights.services.index', compact('totalSpend', 'boostedService', 'totalHighlights', 'timesClicked', 'highlightedClicks', 'boostClicks'));
    }

    public function view_service(Request $request, $id = null)
    {
        if ($request->id) {

            $id = $request->id;
            // dd($id);
        }

        $userId = Auth::id();
        $service = Service::with([
            'boosts' => function ($q) {
                $q->where('type', 'click')
                    ->orderBy('created_at', 'desc');
            },
            'clicks.user:id,name,badge,as_a,user_name,profile_img',
            'serviceType:id,value',
            'locationRelation:id,value',
        ])->findOrFail($id);

        $subImages = json_decode($service->sub_images ?? '[]', true);

        // Append video as a single item
        if (! empty($service->video)) {
            $subImages[] = $service->video;
        }

        // Now you have images + video in one array
        $service->all_media = $subImages;
        $service->total_clicked = $service->boosts->sum('click');

        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $service]);
        }

        return view('highlights.services.view', compact('service'));
    }

    // Jobs Highlight
    public function highlight_jobs()
    {
        $userId = Auth::id();

        $totalSpend = JobBoost::whereHas('job', function ($query) use ($userId) {
            $query->where('created_by', $userId);
        })
            ->sum('amount');

        $totalHighlights = JobBoost::whereHas('job', function ($q) use ($userId) {
            $q->where('created_by', $userId);
        })
            ->count();

        $boostedJobs = Jobs::with(['user:id,name', 'user.gst:id,user_id,business_legal', 'locationRelation:id,value'])->where('created_by', $userId)
            ->whereHas('boosts')
            ->get()->map(function ($data) {
                $data->legal_name = $data->user->gst->business_legal ?? $data->user->name;

                return $data;
            });

        // dd($boostedJobs);

        $totalBoostedDays = JobBoost::whereHas('job', function ($query) use ($userId) {
            $query->where('created_by', $userId);
        })
            ->get()
            ->sum(function ($boost) {
                $from = Carbon::parse($boost->from);
                $to = Carbon::parse($boost->to);

                return $from->diffInDays($to) + 1;
            });

        $totalHighlightedClicks = Jobs::where('created_by', $userId)
            ->where('highlighted', 1)
            ->sum('click');

        $highlightedJobIds = Jobs::where('created_by', $userId)
            ->where('highlighted', 1)
            ->pluck('id');

        $totalHighlightedApplicants = JobApplied::whereIn('job_id', $highlightedJobIds)->count();
        if (request()->header('Authorization')) {

            $data = [
                'totalSpend' => $totalSpend,
                'boostedJobs' => $boostedJobs,
                'totalHighlights' => $totalHighlights,
                'totalBoostedDays' => $totalBoostedDays,
                'totalHighlightedApplicants' => $totalHighlightedApplicants,

            ];

            return response()->json(['success' => true, 'data' => $data]);
        }

        return view('highlights.jobs.index', compact('totalSpend', 'boostedJobs', 'totalHighlights', 'totalBoostedDays', 'totalHighlightedApplicants'));
    }

    public function view_jobs(Request $request, $id = null)
    {
        if ($request->id) {

            $id = $request->id;

            // dd($id);
        }

        $userId = Auth::id();

        $job = Jobs::with(['boosts', 'clicks.user:id,name,badge,as_a,user_name,profile_img', 'locationRelation:id,value', 'user.gst:id,user_id,business_legal'])->orderBy('created_at', 'desc')->findOrFail($id);
        $totalBoostedDays = $job->boosts->sum(function ($boost) {
            $from = \Carbon\Carbon::parse($boost->from);
            $to = \Carbon\Carbon::parse($boost->to);

            return $from->diffInDays($to) + 1;
        });

        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $job, 'totalBoostedDays' => $totalBoostedDays]);
        }

        return view('highlights.jobs.view', compact('job', 'totalBoostedDays'));
    }

    public function getClicks($boost_id)
    {
        $clicks = Click::with('user')->where('boost_id', $boost_id)->get();

        return response()->json($clicks);
    }

    // function for job highlight users
    public function job_highlight_users(Request $request)
    {
        $request->validate([
            'boost_id' => 'required',
            'job_id' => 'required',
            'category' => 'required',
        ]);

        $clicks = Click::with('user:id,name,profile_img,user_name,badge,as_a')
            ->where('category', $request->category)
            ->where('boost_id', $request->boost_id)
            ->where('category_id', $request->job_id)->get();

        return response()->json(['success' => true, 'data' => $clicks]);
    }

    // public funtion for increase the boost click count

    public function boost_clicks(Request $req)
    {

        $type = $req->type;
        $prime_id = $req->prime_id;
        // $boost_id = $req->boost_id;
        $auth = Auth::id();

        try {

            if ($type == 'Jobs') {

                // Log::info('Job Request Data', $req->all());

                $job = Jobs::find($prime_id);

                if ($auth == $job->created_by) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Job Created By Auth',
                    ]);
                }

                $boost = JobBoost::where('job_id', $prime_id)
                    ->where('status', 'active')
                    ->latest()
                    ->first();

                $job_check = Click::where('created_by', Auth::id())->where('category', 'Jobs')->where('category_id', $prime_id)->where('boost_id', $boost->id)->exists();

                if ($job_check) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User Already Clicked Jobs',
                    ]);

                }

                $job->increment('click');

            } elseif ($type == 'Product') {

                $product = Products::find($prime_id);

                if ($auth == $product->created_by) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product Created By Auth',
                    ]);
                }

                $boost = ProductBoost::where('product_id', $prime_id)
                    ->where('status', 'active')
                    ->where('type', 'click')
                    ->latest()
                    ->first();

                // dd($boost);

                $pro_check = Click::where('created_by', Auth::id())->where('category', 'Product')->where('category_id', $prime_id)->where('boost_id', $boost->id)->exists();

                if ($pro_check) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User Already Clicked Product',
                    ]);
                }

                if ($product->click > 0) {
                    $product->decrement('click');
                }
                // else {
                //     return response()->json([
                //         'success' => false,
                //         'message' => 'Product Boost Expired',
                //     ]);
                // }

                if ($product->click == 0) {
                    $product->decrement('highlighted');
                    $boost->update(['status' => 'inactive']);

                    Posts::where('category', 'products')->where('category_id', $product->id)->update(['status' => 'inactive']);

                    $this->notifyUser(
                        $product->created_by,
                        'Product Highlights Ended',
                        'Your product "'.$product->name.'" highlighting has been expired.',
                        $product->id
                    );
                    if ($product->vendor && ($product->vendor->web_token || $product->vendor->mob_token)) {
                        $data = [
                            'web_token' => $product->vendor->web_token,
                            'mob_token' => $product->vendor->mob_token ?? null,
                            'title' => 'Product Highlights Ended',
                            'body' => 'Your product "'.$product->name.'" highlighting has been expired.',
                            'id' => $product->id,
                            'link' => route('individual-product', ['id' => $product->id]),
                        ];
                        $this->notificationService->token($data);
                    }
                }

            } elseif ($type == 'Service') {
                $service = Service::find($prime_id);

                if ($auth == $service->created_by) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Service Created By Auth',
                    ]);
                }

                $boost = ServiceBoost::where('service_id', $prime_id)
                    ->where('status', 'active')
                    ->where('type', 'click')
                    ->latest()
                    ->first();

                // dd(Auth::id());

                if (! $boost) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No Service Boost',
                    ]);
                }

                $ser_check = Click::where('created_by', Auth::id())->where('category', 'Service')->where('category_id', $prime_id)->where('boost_id', $boost->id)->exists();

                if ($ser_check) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User Already Clicked Service',
                    ]);
                }

                try {
                    if ($service->click > 0) {
                        $service->decrement('click');
                    }
                    // else {
                    //     return response()->json([
                    //         'success' => false,
                    //         'message' => 'Service Boost Expired',
                    //     ]);
                    // }

                    // dd($boost);

                    if ($service->click == 0) {

                        $service->decrement('highlighted');
                        $boost->update(['status' => 'inactive']);

                        Posts::where('category', 'service')->where('category_id', $service->id)->update(['status' => 'deleted']);

                        $this->notifyUser(
                            $service->created_by,
                            'Service Highlight Expired',
                            'Your service "'.$service->title.'" highlighting has been expired.',
                            $service->id
                        );
                        // dd('outside if');
                        if ($service->creator && ($service->creator->web_token || $service->creator->mob_token)) {
                            //    dd($service->creator->web_token);
                            Log::info($service->creator->web_token);

                            $data = [
                                'web_token' => $service->creator->web_token,
                                'mob_token' => $service->creator->mob_token ?? null,
                                'title' => 'Service Highlight',
                                'body' => 'Your service "'.$service->title.'" highlight has been expired',
                                'id' => $service->id,
                                'link' => route('service.show', ['id' => $service->id]),
                            ];
                            $this->notificationService->token($data);
                        }
                    }
                } catch (\Exception $e) {
                    log::error('service', ['service_error' => $e->getMessage()]);
                }

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'No Type allowed',
                ]);
            }

            Click::create([
                'category' => $type,
                'category_id' => $prime_id,
                'boost_id' => $boost->id,
                'status' => 'active',
                'created_by' => Auth::id() ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => $type.' Highlight Updated',
            ]);

        } catch (\Exception $e) {
            Log::error('Error Message', ['error_data' => $e]);
        }

    }
}
