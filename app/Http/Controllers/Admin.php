<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Cashback;
use App\Models\Charge;
use App\Models\Chat_bot;
use App\Models\Commission;
use App\Models\DropdownList;
use App\Models\Dropdowns;
use App\Models\Franchise;
use App\Models\FranchiseWallet;
use App\Models\JobBoost;
use App\Models\Posts;
use App\Models\ProductBoost;
use App\Models\Report;
use App\Models\ServiceBoost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Jobs;
use App\Models\Products;
use App\Models\Service;
use App\Models\Lead;
use App\Models\OrderProducts;
use App\Models\Orders;
use App\Models\Premium;
use App\Models\GstDetails;
use App\Models\OwnedLeads;
use App\Models\PremiumUser;
use App\Models\Project;
use App\Models\ReadyToWork;
use App\Models\ReadyToWorkBoost;
use App\Services\Aws;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Settlement;



class Admin extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    private function notifyUser($category, $userId, $title, $body, $category_id)
    {
        $this->notificationService->create([
            'category' => $category,
            'category_id' => $category_id,
            'reciever' => $userId,
            'title' => $title,
            'body' => $body,
            'status' => 'active',
            'seen' => false,
            'c_by' => 0,
            'remainder' => null,
        ]);
    }

    public function login(Request $request)
    {
        $user = User::where('name', $request->name)->first();

        // if (!$user) {
        //     dd('User not found');
        // }
        // php artisan tinker --execute="App\Models\User::create(['name' => 'admin', 'email' => 'admin@example.com', 'password' => bcrypt('admin123')]);"
        // dd([
        //     'input_password' => $request->password,
        //     'db_password' => $user->password,
        //     'match' => Hash::check($request->password, $user->password)
        // ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:3',
        ]);

        if (Hash::check($request->password, $user->password)) {
            $request->session()->regenerate();
            Log::info('User logged in', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'time' => now()->toDateTimeString(),
                'ip' => $request->ip(),
            ]);
            Session::put('adminloggined', true);
            Cookie::queue('admin_id', $user->id, 60 * 24);
            return redirect('dashboard_admin');
        }
        return back()->withErrors([
            'name' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    public function admin_logout(Request $request)
    {
        Auth::logout();
        Session::forget('adminloggined');
        Cookie::queue(Cookie::forget('admin_id'));
        return redirect()->route('admin');
    }

    public function dashboard()
    {
        // Business Providers
        $vendorCount = UserDetail::where('you_are', 'business')->where('as_a', 'vendor')->count();
        $contractorCount = UserDetail::where('you_are', 'Business')->where('as_a', 'Contractor')->count();
        $consultantCount = UserDetail::where('you_are', 'Business')->where('as_a', 'Consultant')->count();
        $business_total = $vendorCount + $contractorCount + $consultantCount;

        // Users
        $technicalCount = UserDetail::where('you_are', 'Professional')->where('as_a', 'Technical')->count();
        $nonTechnicalCount = UserDetail::where('you_are', 'Professional')->where('as_a', 'Non-Technical')->count();
        $consumerCount = UserDetail::where('you_are', 'consumer')->count();
        $userTotal = $technicalCount + $nonTechnicalCount + $consumerCount;
        $users = UserDetail::all();

        $productTotal = Products::count();
        $productActive = Products::where('approvalstatus', 'approved')->count();
        $productPending = Products::where('approvalstatus', 'pending')->count();
        $productRejected = Products::where('approvalstatus', 'rejected')->count();
        $productHighlighted = Products::where('highlighted', 1)->count();

        $jobTotal = Jobs::count();
        $jobActive = Jobs::where('approvalstatus', 'approved')->count();
        $jobPending = Jobs::where('approvalstatus', 'pending')->count();
        $jobRejected = Jobs::where('approvalstatus', 'rejected')->count();
        $jobHighlighted = Jobs::where('highlighted', 1)->count();

        $serviceTotal = Service::count();
        $serviceActive = Service::where('approvalstatus', 'approved')->count();
        $servicePending = Service::where('approvalstatus', 'pending')->count();
        $serviceRejected = Service::where('approvalstatus', 'rejected')->count();
        $serviceHighlighted = Service::where('highlighted', 1)->count();

        $highlightedTotal = $productHighlighted + $serviceHighlighted + $jobHighlighted;

        $leadsTotal = Lead::count();
        $leadsActive = Lead::where('approval_status', 'approved')->count();
        $leadsPending = Lead::where('approval_status', 'pending')->count();
        $leadsRejected = Lead::where('approval_status', 'rejected')->count();

        $ordersCreated = Orders::whereDate('created_at', now()->toDateString())->count();
        $deliveredOrders = Orders::where('status', 'delivered')->whereDate('updated_at', now()->toDateString())->count();
        $ordersTotal = $ordersCreated + $deliveredOrders;

        $reportUser = Report::where('type', 'user')->count();
        $reportPost = Report::where('type', 'post')->count();
        $reportTotal = $reportUser + $reportPost;

        return view('admin.dashboard_admin', compact(
            'productTotal',
            'productActive',
            'productPending',
            'jobTotal',
            'users',
            'jobActive',
            'jobPending',
            'serviceTotal',
            'serviceActive',
            'servicePending',
            'leadsTotal',
            'leadsActive',
            'leadsPending',
            'leadsRejected',
            'vendorCount',
            'contractorCount',
            'consultantCount',
            'technicalCount',
            'nonTechnicalCount',
            'consumerCount',
            'business_total',
            'productRejected',
            'jobRejected',
            'serviceRejected',
            'userTotal',
            'ordersCreated',
            'deliveredOrders',
            'ordersTotal',
            'productHighlighted',
            'serviceHighlighted',
            'highlightedTotal',
            'jobHighlighted',
            'reportUser',
            'reportPost',
            'reportTotal',
        ));
    }

    public function user_vendor()
    {
        $users = UserDetail::where('as_a', 'Vendor')->get();
        return view('admin.user_vendor', compact('users'));
    }

    public function user_contractor()
    {
        $users = UserDetail::where('as_a', 'Contractor')->get();
        return view('admin.user_contractor', compact('users'));
    }

    public function user_consultant()
    {
        $users = UserDetail::where('as_a', 'Consultant')->get();
        return view('admin.user_consultant', compact('users'));
    }

    public function user_consumer()
    {
        $users = UserDetail::where('you_are', 'Consumer')->get();
        return view('admin.user_consumer', compact('users'));
    }

    public function user_professional()
    {
        $users = UserDetail::where('you_are', 'Professional')->get();
        return view('admin.user_professional', compact('users'));
    }

    public function product_list()
    {
        $products = Products::with('vendor:id,name,you_are')->latest()->get();
        return view('admin.product_list', compact('products'));
    }

    public function leads_list()
    {
        $leads = Lead::with('user:id,name,you_are')
            ->latest()
            ->get();
        return view('admin.leads_list', compact('leads'));
    }

    public function job_list()
    {
        $jobs = Jobs::with('user:id,name,you_are')->latest()->get();
        return view('admin.job_list', compact('jobs'));
    }

    public function insight_list()
    {
        $grouped = Report::select('f_id', 'type', DB::raw('COUNT(*) as count'), DB::raw('MAX(created_at) as latest_created_at'))
            ->groupBy('f_id', 'type')
            ->orderByDesc('latest_created_at')
            ->get();

        $insights = $grouped->map(function ($item) {
            if ($item->type === 'post') {
                $insight = Report::where('type', 'post')->where('f_id', $item->f_id)->with('post.user')->first();
            } elseif ($item->type === 'user') {
                $insight = Report::where('type', 'user')->where('f_id', $item->f_id)->with('user')->first();
            } else {
                $insight = null;
            }
            if ($insight) {
                $insight->count = $item->count;
                return $insight;
            }
            return null;
        })->filter(); // Remove nulls

        return view('admin.insight_list', compact('insights'));
    }

    public function highlight_products()
    {
        $highlightedProduct = ProductBoost::where('type', 'click')->with('product.vendor')->get();
        return view('admin.highlight_products_list', compact('highlightedProduct'));
    }

    public function highlight_jobs()
    {
        $highlightedJobs = JobBoost::with('job.user')->get();
        return view('admin.highlight_jobs_list', compact('highlightedJobs'));
    }

    public function highlight_services()
    {
        $highlightedService = ServiceBoost::with('service.creator')->where('type', 'click')->get();
        return view('admin.highlight_services_list', compact('highlightedService'));
    }

    public function service()
    {
        $services = Service::latest()->get();
        return view('admin.service_list', compact('services'));
    }

    public function jobStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'remark' => 'nullable|string|max:255',
        ]);
        $job = Jobs::findOrFail($id);
        $job->approvalstatus = $request->status;
        $job->remarks = $request->remark;
        $job->save();

        $this->notifyUser(
            'job',
            $job->created_by,
            'Job ' . ucfirst($request->status),
            'Your job "' . $job->title . '" has been ' . $request->status . '.',
            $job->id
        );

        // now safely access related user
        if ($job->user && ($job->user->web_token || $job->user->mob_token)) {
            $data = [
                'web_token' => $job->user->web_token,
                'mob_token' => $job->user->mob_token ?? null,
                'title' => 'Job ' . ucfirst($request->status),
                'body' => 'Your job "' . $job->title . '" has been ' . $request->status . '.',
                'id' => $job->id,
                'link' => route('job.details', ['id' => $job->id])
            ];
            $this->notificationService->token($data);
        }

        return response()->json([
            'success' => true,
            'approvalstatus' => $job->approvalstatus,
            'message' => 'Status Updated!',
            'data' => $job->user
        ]);
    }

    public function serviceStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'remark' => 'nullable|string|max:255',
        ]);

        $service = Service::findOrFail($id);
        $service->approvalstatus = $request->status;
        $service->remark = $request->remark;
        $service->save();

        $this->notifyUser(
            'service',
            $service->created_by,
            'Service ' . ucfirst($request->status),
            'Your service "' . $service->title . '" has been ' . $request->status . '.',
            $service->id
        );
        if ($service->creator && ($service->creator->web_token || $service->creator->mob_token)) {
            $data = [
                'web_token' => $service->creator->web_token,
                'mob_token' => $service->creator->mob_token ?? null,
                'title' => 'service ' . ucfirst($request->status),
                'body' => 'Your service "' . $service->title . '" has been ' . $request->status . '.',
                'id' => $service->id,
                'link' => route('service.show', ['id' => $service->id])
            ];
            $this->notificationService->token($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated!',
            'status' => $service->approvalstatus,
            'remark' => $service->remark,
            'data' => $service->creator
        ]);
    }

    public function productStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'cashback' => 'nullable|numeric',
            'margin' => 'nullable|numeric',
            'remark' => 'required|string|max:255',
        ]);

        $product = Products::findOrFail($id);
        $product->cashback_price = $request->cashback;
        $product->margin = $request->margin;
        $product->approvalstatus = $request->status;
        $product->remark = $request->remark;
        $product->save();

        $this->notifyUser(
            'product',
            $product->created_by,
            'Product ' . ucfirst($request->status),
            'Your product "' . $product->name . '" has been ' . $request->status . '.',
            $product->id
        );
        if ($product->vendor && ($product->vendor->web_token || $product->vendor->mob_token)) {
            $data = [
                'web_token' => $product->vendor->web_token,
                'mob_token' => $product->vendor->mob_token ?? null,
                'title' => 'product ' . ucfirst($request->status),
                'body' => 'Your product "' . $product->name . '" has been ' . $request->status . '.',
                'id' => $product->id,
                'link' => route('individual-product', ['id' => $product->id])
            ];
            $this->notificationService->token($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated!',
            'status' => $product->approvalstatus,
            'remark' => $product->remark,
        ]);
    }

    public function leadsStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'remark' => 'nullable|string|max:255',
            'admin_charge' => 'required|numeric'
        ]);
        // dd($request->status);

        $leads = Lead::findOrFail($id);
        $leads->approval_status = $request->status;
        $leads->admin_charge = $request->admin_charge;
        $leads->remark = $request->remark;
        $leads->save();

        $this->notifyUser(
            'lead',
            $leads->created_by ?? $leads->user_id,
            'Lead ' . ucfirst($request->status),
            'Your lead "' . $leads->title . '" has been ' . $request->status . '.',
            $leads->id
        );
        if ($leads->user && ($leads->user->web_token || $leads->user->mob_token)) {
            $data = [
                'web_token' => $leads->user->web_token,
                'mob_token' => $leads->user->mob_token ?? null,
                'title' => 'leads ' . ucfirst($request->status),
                'body' => 'Your leads "' . $leads->title . '" has been ' . $request->status . '.',
                'id' => $leads->id,
                'link' => route('leads.details', ['id' => $leads->id])
            ];
            $this->notificationService->token($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated!',
            'status' => $leads->approval_status,
            'remark' => $leads->remark,
        ]);
    }

    public function show_user($id)
    {
        $users = UserDetail::findOrFail($id);
        $gst = GstDetails::where('user_id', $id)->first();
        $badges = Badge::where('created_by', $id)->get();
        return view('admin.users_detail', compact('users', 'gst', 'badges'));
    }

    public function show_product($id)
    {
        $product = Products::with(['hub', 'vendor.gst'])->findOrFail($id);
        $productListing = ProductBoost::where('product_id', $id)->where('type', 'list')->first();
        $productHighlighting = ProductBoost::where('product_id', $id)->where('type', 'click')->get();
        $product->decoded_images = null;
        if (!empty($product->image)) {
            $decoded = json_decode($product->image);
            if (json_last_error() === JSON_ERROR_NONE) {
                $product->decoded_images = $decoded;
            }
        }
        return view('admin.product_detail', compact('product', 'productListing', 'productHighlighting'));
    }

    public function show_service($id)
    {
        $service = Service::findOrFail($id);
        $serviceListing = ServiceBoost::where('service_id', $id)->where('type', 'list')->first();
        $serviceHighlighting = ServiceBoost::where('service_id', $id)->where('type', 'click')->get();
        $service->decoded_images = null;
        if (!empty($service->sub_images)) {
            $decoded = json_decode($service->sub_images);
            if (json_last_error() === JSON_ERROR_NONE) {
                $service->decoded_images = $decoded;
            }
        }
        return view('admin.service_detail', compact('service', 'serviceListing', 'serviceHighlighting'));
    }

    public function show_project($id)
    {
        $project = Project::findOrFail($id);
        $project->decoded_images = null;
        if (!empty($project->sub_image)) {
            $decoded = json_decode($project->sub_image);
            if (json_last_error() === JSON_ERROR_NONE) {
                $project->decoded_images = $decoded;
            }
        }
        return view('admin.project_detail', compact('project'));
    }

    public function show_job($id)
    {
        $job = Jobs::findOrFail($id);
        $jobBoosting = JobBoost::where('job_id', $id)->get();
        return view('admin.job_detail', compact('job', 'jobBoosting'));
    }

    public function show_lead($id)
    {
        $lead = Lead::with([
            'reviews' => function ($q) {
                $q->whereHas('user');
            },
            'reviews.user'
        ])
            ->withCount('reviews')
            ->withAvg('reviews', 'stars')
            ->findOrFail($id);
        $reviews = Lead::where('id', $id)->with('user')->get();
        $ownedUsers = OwnedLeads::where('lead_id', $id)->get();
        return view('admin.lead_detail', compact('lead', 'reviews', 'ownedUsers'));
    }

    public function show_insight($id)
    {
        $post = Posts::where('id', $id)->first();
        $user = UserDetail::where('id', $id)->first();
        $insights = Report::where('f_id', $id)
            ->with(['post.user', 'user', 'reporter'])
            ->get();
        return view('admin.insight_detail', compact('insights', 'id', 'post', 'user'));
    }

    public function charges_list()
    {
        $items = Charge::latest()->get();
        return view('admin.charges_list', compact('items'));
    }

    public function commission_list()
    {
        $commissions = Commission::latest()->get();
        $category = DropdownList::where('dropdown_id', 3)->get();
        return view('admin.commission_list', compact('commissions', 'category'));
    }

    public function cashback_list()
    {
        $cashbacks = Cashback::with('vendor', 'user')->get();
        return view('admin.cashback_list', compact('cashbacks'));
    }

    public function orders_list()
    {
        $orders = Orders::all();
        return view('admin.orders_list', compact('orders'));
    }

    public function settlement_list()
    {
        $list = OrderProducts::where('status', 'delivered')->with(['product.vendor', 'order'])
            ->latest()
            ->get()
            ->groupBy(function ($item) {
                return $item->order_id;
            });
        // dd($list);   
        return view('admin.orders_settlement', ['list' => $list]);
    }

    public function show_order($id)
    {
        $order = Orders::with('address', 'user')->findOrFail($id);
        $order_products = OrderProducts::where('order_id', $order->order_id)->with('product.vendor', 'vendor')->get();
        return view('admin.orders_detail', compact('order', 'order_products'));
    }

    public function premium_list()
    {
        $premiums = Premium::latest()->get();
        return view('admin.premium_list', compact('premiums'));
    }

    public function store(Request $request, Aws $aws)
    {
        $rules = [
            'premium_type' => 'required|in:post,reel,blog',
            'caption' => 'required|string|',
        ];
        if ($request->premium_type === 'post') {
            $rules['image'] = 'required|image|mimes:jpeg,jpg,png,gif|max:5120';
        } elseif ($request->premium_type === 'reel') {
            $rules['video'] = 'required|mimes:mp4,mov,avi,wmv|max:50000';
        }
        $request->validate($rules);
        $premium = new Premium();
        $premium->premium_type = $request->premium_type;
        $premium->caption = $request->caption;
        $premium->c_by = 0;

        // Image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if (!is_array($file)) {
                $file = [$file];
            }
            $folder = 'premium_medias';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $image = is_array($s3Result) ? $s3Result[0] : $s3Result;
            $premium->image = $image;
        }
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            if (!is_array($file)) {
                $file = [$file];
            }
            $folder = 'premium_medias';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $video = is_array($s3Result) ? $s3Result[0] : $s3Result;
            $premium->video = $video;
        }
        $premium->save();
        return redirect()->route('premium_list')->with('success', 'Premium added!');
    }

    public function update(Request $request, $id, Aws $aws)
    {
        $premium = Premium::findOrFail($id);
        $rules = [
            'premium_type' => 'required|in:post,reel,blog',
            'caption' => 'required|string|',
        ];
        if ($request->hasFile('image')) {
            $rules['image'] = 'image|mimes:jpeg,jpg,png,gif|max:5120';
        }
        if ($request->hasFile('video')) {
            $rules['video'] = 'mimes:mp4,mov,avi,wmv|max:50000';
        }
        $request->validate($rules);
        $premium->premium_type = $request->premium_type;
        $premium->caption = $request->caption;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if (!is_array($file)) {
                $file = [$file];
            }
            $folder = 'premium_medias';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $image = is_array($s3Result) ? $s3Result[0] : $s3Result;
            $premium->image = $image;
        }
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            if (!is_array($file)) {
                $file = [$file];
            }
            $folder = 'premium_medias';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $video = is_array($s3Result) ? $s3Result[0] : $s3Result;
            $premium->video = $video;
        }
        $premium->save();
        return redirect()->route('premium_list')->with('success', 'Premium updated!');
    }

    public function premium_users_list()
    {
        $premium = PremiumUser::selectRaw('user_id, COUNT(*) as total, MAX(id) as id, MAX(created_at) as created_at')
            ->with('user')
            ->groupBy('user_id')
            ->get();
        return view('admin.premium_users', compact('premium'));
    }

    public function refreshJobHighlights()
    {
        $jobIdsBeforeUpdate = Jobs::where('highlighted', 1)->pluck('id')->toArray();  // highlighted 1 get
        Jobs::query()->update(['highlighted' => 0]);   // at this value 0 set
        $today = Carbon::today();
        $jobIdsWithActiveBoosts = JobBoost::whereDate('from', '<=', $today)
            ->whereDate('to', '>=', $today)
            ->pluck('job_id')
            ->unique();
        Jobs::whereIn('id', $jobIdsWithActiveBoosts)->update(['highlighted' => 1]);     //at this value 1 set
        $jobIdsAfterUpdate = Jobs::where('highlighted', 1)->pluck('id')->toArray();     //again highlighted 1 get
        $jobsUnhighlighted = array_diff($jobIdsBeforeUpdate, $jobIdsAfterUpdate);       //
        foreach ($jobsUnhighlighted as $jobId) {
            $job = Jobs::with('user')->find($jobId); // get job and user

            if (!$job || !$job->user) {
                continue; // skip if job or user is missing
            }

            $title = 'Job Highlight Expired';
            $body = 'Your job "' . $job->title . '" is no longer highlighted because its boost has expired.';

            // Send internal notification
            $this->notifyUser(
                'job',
                $job->created_by,
                $title,
                $body,
                $job->id
            );

            // Send push notification (if tokens are available)
            if ($job->user->web_token || $job->user->mob_token) {
                $data = [
                    'web_token' => $job->user->web_token,
                    'mob_token' => $job->user->mob_token ?? null,
                    'title' => $title,
                    'body' => $body,
                    'id' => $job->id,
                    'link' => route('job.details', ['id' => $job->id])
                ];
                $this->notificationService->token($data);
            }
        }
        return redirect()->back()->with('success', 'Job highlights updated based on active boosts.');
    }

    public function addCharges(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'charges' => 'required|numeric|min:0',
        ]);
        Charge::where('category', $request->category)->update(['status' => 'inactive']);
        $charge = new Charge();
        $charge->category = $request->category;
        $charge->charge = $request->charges;
        $charge->status = 'active';
        $charge->save();

        return redirect()->back()->with('success', 'Charge added!');
    }

    public function addCommission(Request $request)
    {
        $request->validate([
            'category_id' => 'required|string|max:255',
            'commission' => 'required|numeric|min:0',
        ]);
        Commission::where('category_id', $request->category_id)->update(['status' => 'inactive']);
        $commission = new Commission();
        $commission->category_id = $request->category_id;
        $commission->commission = $request->commission;
        $commission->status = 'active';
        $commission->save();

        return redirect()->back()->with('success', 'Commission added!');
    }

    public function deactivatePost(Request $request)
    {
        $request->validate([
            'post_id' => 'required|string|max:255',
        ]);
        $post = Posts::find($request->post_id);
        if ($post) {
            $post->status = 'inactive';
            $post->save();
            return redirect()->back()->with('success', 'Post Deactivated!');
        } else {
            return redirect()->back()->with('error', 'Post not found!');
        }
    }

    public function activatePost(Request $request)
    {
        $request->validate([
            'post_id' => 'required|string|max:255',
        ]);
        $post = Posts::find($request->post_id);
        if ($post) {
            $post->status = 'active';
            $post->save();
            return redirect()->back()->with('success', 'Post Activated!');
        } else {
            return redirect()->back()->with('error', 'Post not found!');
        }
    }

    public function deactivateUser(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        $user = UserDetail::find($request->id);
        if ($user) {
            $user->status = 'inactive';
            $user->save();
            return redirect()->back()->with('success', 'User Deactivated!');
        } else {
            return redirect()->back()->with('error', 'User not found!');
        }
    }
    
    public function activateUser(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        $user = UserDetail::find($request->id);
        if ($user) {
            $user->status = 'active';
            $user->save();
            return redirect()->back()->with('success', 'User Activated!');
        } else {
            return redirect()->back()->with('error', 'User not found!');
        }
    }

    public function settlement_action(Request $request)
    {
        $productIds = $request->input('product_ids'); // This will be an array
        if (empty($productIds)) {
            return back()->with('error', 'No products selected.');
        }
        OrderProducts::whereIn('id', $productIds)->update(['settlement_status' => 'completed']);
        return back()->with('success', 'Selected products settled.');
    }

    public function refreshreadytowork()
    {
        $today = Carbon::today();
        $expired = ReadyToWork::whereDate('expiry', '<', $today)
            ->where('status', 'active') // optional, if needed
            ->with('user')
            ->get();
        foreach ($expired as $ready) {
            $ready->status = 'expired';
            $ready->save();
            $user = $ready->user;
            if ($user && ($user->web_token || $user->mob_token)) {

                $title = 'Ready to Work Expired';
                $body = 'Your "Ready to Work" profile has expired on ' . Carbon::parse($ready->expiry)->format('d-m-Y') . '.';
                // Internal notification
                $this->notifyUser('ready', $user->id, $title, $body, $ready->id);
                // Push notification
                $this->notificationService->token([
                    'web_token' => $user->web_token,
                    'mob_token' => $user->mob_token ?? null,
                    'title' => $title,
                    'body' => $body,
                    'id' => $ready->id,
                    'link' => route('profile', ['id' => $user->id])
                ]);
            }
        }
        return redirect()->back()->with('success', 'Expired ReadyToWork entries processed.');
    }

    public function report_products()
    {
        $productListing = ProductBoost::where('type', 'list')->get();
        $productHighlighting = ProductBoost::where('type', 'click')->get();
        $productincome = OrderProducts::with('product')->get();
        return view('admin.report_products', compact('productListing', 'productHighlighting', 'productincome'));
    }

    public function report_services()
    {
        $serviceListing = ServiceBoost::where('type', 'list')->get();
        $serviceHighlighting = ServiceBoost::where('type', 'click')->get();
        return view('admin.report_services', compact('serviceListing', 'serviceHighlighting'));
    }

    public function report_jobs()
    {
        $jobBoosting = jobBoost::all();
        return view('admin.report_jobs', compact('jobBoosting'));
    }

    public function report_projects()
    {
        $projectListing = Project::where('amount', '>', 0)->get();
        return view('admin.report_projects', compact('projectListing'));
    }

    public function report_leads()
    {
        $leadsOwned = OwnedLeads::all();
        return view('admin.report_leads', compact('leadsOwned'));
    }

    public function report_premium()
    {
        $premiumReport = PremiumUser::all();
        return view('admin.report_premium', compact('premiumReport'));
    }

    public function report_badges()
    {
        $badges = Badge::all();
        return view('admin.report_badges', compact('badges'));
    }

    public function report_readytowork()
    {
        $readytowork = ReadyToWork::all();
        return view('admin.report_readytowork', compact('readytowork'));
    }

    public function report_chatbot()
    {
        $chatbots = Chat_bot::all();
        return view('admin.report_chatbot', compact('chatbots'));
    }

    // Franchise
    public function franchise_dashboard()
    {
        $vendor_total = UserDetail::where('as_a', '=', 'Vendor')->where('ref_id', '!=', '')->count();
        $contractor_total = UserDetail::where('as_a', '=', 'Contractor')->where('ref_id', '!=', '')->count();
        $cosultant_total = UserDetail::where('as_a', '=', 'Consultant')->where('ref_id', '!=', '')->count();
        $technical_total = UserDetail::where('as_a', '=', 'Technical')->where('ref_id', '!=', '')->count();
        $nontechnical_total = UserDetail::where('as_a', '=', 'Technical')->where('ref_id', '!=', '')->count();
        $consumer_total = UserDetail::where('you_are', '=', 'Consumer')->where('ref_id', '!=', '')->count();

        $today_vendor_count = UserDetail::where('as_a', 'Vendor')
            ->where('ref_id', '!=', '')
            ->whereDate('created_at', Carbon::today()) // only today’s date
            ->count();
        $today_contractor_count = UserDetail::where('as_a', 'Contractor')
            ->where('ref_id', '!=', '')
            ->whereDate('created_at', Carbon::today()) // only today’s date
            ->count();
        $today_consultant_count = UserDetail::where('as_a', 'Consultant')
            ->where('ref_id', '!=', '')
            ->whereDate('created_at', Carbon::today()) // only today’s date
            ->count();
        $today_tech_count = UserDetail::where('as_a', 'Technical')
            ->where('ref_id', '!=', '')
            ->whereDate('created_at', Carbon::today()) // only today’s date
            ->count();
        $today_nontech_count = UserDetail::where('as_a', 'Non-Technical')
            ->where('ref_id', '!=', '')
            ->whereDate('created_at', Carbon::today()) // only today’s date
            ->count();
        $today_consumer_count = UserDetail::where('you_are', 'Consumer')
            ->where('ref_id', '!=', '')
            ->whereDate('created_at', Carbon::today()) // only today’s date
            ->count();

        $today_list = UserDetail::where('ref_id', '!=', '')->with('franchise')
            ->whereDate('created_at', Carbon::today())->get();

        return view('admin.franchise_dashboard', compact(
            'vendor_total',
            'contractor_total',
            'cosultant_total',
            'technical_total',
            'nontechnical_total',
            'consumer_total',
            'today_vendor_count',
            'today_contractor_count',
            'today_consultant_count',
            'today_tech_count',
            'today_nontech_count',
            'today_consumer_count',
            'today_list'
        ));
    }

    public function franchise_list()
    {
        $franchise_list = Franchise::get();
        return view('admin.franchise_list', compact('franchise_list'));
    }

    public function show_franchise(Request $request)
    {
        $user = Franchise::where('id', $request->id)->first();
        $user_data= FranchiseWallet::where('user_id', $request->id)->get();
       // dd($user_data);
        $vendorQuery = UserDetail::where('as_a', '=', 'Vendor')->where('ref_id', '=', $user->code)->get();
        $vendor_data = $vendorQuery;
        $vendor_total = $vendorQuery->count();
        $contractorQuery = UserDetail::where('as_a', '=', 'Contractor')->where('ref_id', '=', $user->code)->get();
        $contractor_data = $contractorQuery;
        $contractor_total = $contractorQuery->count();
        $consultantQuery = UserDetail::where('as_a', '=', 'Consultant')->where('ref_id', '=', $user->code)->get();
        $consultant_data = $consultantQuery;
        $consultant_total = $consultantQuery->count();
        $technicalQuery = UserDetail::where('as_a', '=', 'Technical')->where('ref_id', '=', $user->code)->get();
        $technical_data = $technicalQuery;
        $technical_total = $technicalQuery->count();
        $nontechnicalQuery = UserDetail::where('as_a', '=', 'Non-Technical')->where('ref_id', '=', $user->code)->get();
        $nontechnical_data = $nontechnicalQuery;
        $nontechnical_total = $nontechnicalQuery->count();
        $consumerQuery = UserDetail::where('you_are', '=', 'Consumer')->where('ref_id', '=', $user->code)->get();
        $consumer_data = $consumerQuery;
        $consumer_total = $consumerQuery->count();

        $today_vendor_count = UserDetail::where('as_a', 'Vendor')
            ->where('ref_id', '=', $user->code)
            ->whereDate('created_at', Carbon::today()) // only today’s date
            ->count();
        $today_contractor_count = UserDetail::where('as_a', 'Contractor')
            ->where('ref_id', '=', $user->code)
            ->whereDate('created_at', Carbon::today()) // only today’s date
            ->count();
        $today_consultant_count = UserDetail::where('as_a', 'Consultant')
            ->where('ref_id', '=', $user->code)
            ->whereDate('created_at', Carbon::today()) // only today’s date
            ->count();
        $today_tech_count = UserDetail::where('as_a', 'Technical')
            ->where('ref_id', '=', $user->code)
            ->whereDate('created_at', Carbon::today()) // only today’s date
            ->count();
        $today_nontech_count = UserDetail::where('as_a', 'Non-Technical')
            ->where('ref_id', '=', $user->code)
            ->whereDate('created_at', Carbon::today()) // only today’s date
            ->count();
        $today_consumer_count = UserDetail::where('you_are', 'Consumer')
            ->where('ref_id', '=', $user->code)
            ->whereDate('created_at', Carbon::today()) // only today’s date
            ->count();

        // $today_list=UserDetail::where('ref_id', '!=', '')
        //              ->whereDate('created_at', Carbon::today())->get();

        
        // $ordersQuery = OrderProducts::with(['product.vendor', 'order'])
        //     ->where('status', 'delivered')
        //     ->where(function ($query) {
            
        //     })
        //     ->whereHas('product.vendor', function ($query) use ($franchise) {
        //         $query->where('ref_id', $franchise->code);
        //     });
        $orderProducts = OrderProducts::with(['product.vendor'])
            ->where('status', 'delivered')
            ->whereHas('product.vendor', function ($query) use ($user) {
                $query->where('as_a', 'Contractor')       // user is contractor
                    ->where('ref_id', $user->code);    // ref_id matches
            })
            ->get();
         //dd($orderProducts);
       // $orderProducts = $orderProducts->get();
       $commissions = DB::table('commission')
       ->where('status', 'active')
       ->pluck('commission', 'category_id');

        $results = [];
        foreach ($orderProducts as $op) {
            $product = $op->product;
            $vendor = $product?->vendor;
            $vendor_ref_id = $product?->vendor->ref_id;
            $product_id = $product->id;

            if (!$product || !$vendor)
                continue;

            $salesPrice = $op->base_price * $op->quantity;
            $categoryId = $product->category;
            $commissionPercentage = $commissions->get($categoryId, 0);
            $commissionAmount = ($salesPrice * ($commissionPercentage / 2)) / 100;

            $results[] = [
                'order_products_id' => $op->id,
                'order_id' => $op->order_id,
                'vendor_name' => $vendor->name ?? 'N/A',
                'vendor_ref_id' => $vendor_ref_id,
                'product_id' => $product_id,
                'commission_amount' => $commissionAmount,
                'created_at' => $op->created_at->format('d-m-Y'),
            ];
        }
        //dd($results);
        $totalCommissionAmount = collect($results)->sum('commission_amount');
       

        return view('admin.franchise_detail', compact(
            'vendor_data',
            'vendor_total',
            'contractor_data',
            'contractor_total',
            'consultant_data',
            'consultant_total',
            'technical_data',
            'technical_total',
            'nontechnical_data',
            'nontechnical_total',
            'consumer_total',
            'consumer_data',
            'today_vendor_count',
            'today_contractor_count',
            'today_consultant_count',
            'today_tech_count',
            'today_nontech_count',
            'today_consumer_count',
            'user',
            'user_data'
        ));
        // return view('admin.franchise_detail');
    }

    public function franchise_settlement()
    {
        $franchises_dropdown = Franchise::all();
        return view('admin.franchise_settlement', compact('franchises_dropdown'));
    }

    public function franchise_users(Request $request)
    {
        $id = $request->id;
        $type = $request->type;

        $franchise = Franchise::find($id);

        if (!$franchise) {
            return redirect()->back()->with('error', 'Franchise not found');
        }

        $query = UserDetail::where('ref_id', $franchise->code);
        if (in_array($type, ['Vendor', 'Contractor', 'Consultant', 'Technical', 'Non-Technical'])) {
            $query->where('as_a', $type);
        } elseif ($type === 'Consumer') {
            $query->where('you_are', 'Consumer');
        }
        $users = $query->get();
        return view('admin.franchise_users', compact('franchise', 'type', 'users'));
    }

    public function franchise_amount_settle(Request $request)
    {
        // Validate input
        $request->validate([
            'users' => 'required|exists:franchise,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $franchise = Franchise::where('id', $request->users ?? 5)->first();

        if (!$franchise) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        /* Product  Sales Commision */
        $commissions = DB::table('commission')
            ->where('status', 'active')
            ->pluck('commission', 'category_id');

        $ordersQuery = OrderProducts::with(['product.vendor', 'order'])
            ->where('status', 'delivered')
            ->where(function ($query) {
                $query->whereNull('settle_id')   // ✅ skip settled ones (NULL)
                    ->orWhere('settle_id', 0); // ✅ skip settled ones (0)
            })
            ->whereHas('product.vendor', function ($query) use ($franchise) {
                $query->where('ref_id', $franchise->code);
            });
        if ($fromDate && $toDate) {
            $ordersQuery->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }

        $orderProducts = $ordersQuery->get();

        $results = [];
        foreach ($orderProducts as $op) {
            $product = $op->product;
            $vendor = $product?->vendor;
            $vendor_ref_id = $product?->vendor->ref_id;
            $product_id = $product->id;

            if (!$product || !$vendor)
                continue;

            $salesPrice = $op->base_price * $op->quantity;
            $categoryId = $product->category;
            $commissionPercentage = $commissions->get($categoryId, 0);
            $commissionAmount = ($salesPrice * ($commissionPercentage / 2)) / 100;

            $results[] = [
                'order_products_id' => $op->id,
                'order_id' => $op->order_id,
                'vendor_name' => $vendor->name ?? 'N/A',
                'vendor_ref_id' => $vendor_ref_id,
                'product_id' => $product_id,
                'commission_amount' => $commissionAmount,
                'created_at' => $op->created_at->format('d-m-Y'),
            ];
        }
        $totalCommissionAmount = collect($results)->sum('commission_amount');

        /*    End Product Sales Commision  */
        $franchises_dropdown = Franchise::all();

        /*Leads  Sales Get Commision */
        $LeadQuery = OwnedLeads::with(['lead.user'])

            ->where(function ($query) {
                $query->whereNull('settle_id')
                    ->orWhere('settle_id', 0);
            })
            ->whereHas('lead.user', function ($query) use ($franchise) {
                $query->where('ref_id', $franchise->code);
            });

        if ($fromDate && $toDate) {
            $LeadQuery->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }

        $LeadOrders = $LeadQuery->get();

        $Leads = [];
        foreach ($LeadOrders as $lo) {
            $lead = $lo->lead;
            $lead_user = $lo->lead->user->name;
            $lead_user_id = $lo->lead->user->ref_id;
            //   $product_id = $product->id;

            if (!$lead || !$lead_user_id)
                continue;
            $lead_commission_Amount = ($lead->admin_charge * 5) / 100;

            //   $salesPrice = $op->base_price * $op->quantity;
            //   $categoryId = $product->category;
            //   $commissionPercentage = $commissions->get($categoryId, 0);
            //   $commissionAmount = ($salesPrice * ($commissionPercentage / 2)) / 100;

            $Leads[] = [
                'owned_leadid' => $lo->id,
                'lead_user' => $lead_user,
                'lead_user_id' => $lead_user_id,
                'lead_id' => $lead->id,
                //   'vendor_name' => $vendor->name ?? 'N/A',
                //   'vendor_ref_id' => $vendor_ref_id ,
                //   'product_id'=>$product_id,
                'lead_commission_amount' => $lead_commission_Amount,
                'created_at' => $lo->created_at->format('d-m-Y'),
            ];
        }
        $lead_total_amount = collect($Leads)->sum('lead_commission_amount');
        /* End Leads Commision */

        /*Service Commision*/
        $ServiceQuery = ServiceBoost::where(function ($query) {
            $query->whereNull('settle_id')
                ->orWhere('settle_id', 0);
        })
            ->whereHas('service.creator', function ($q) use ($franchise) {
                $q->where('ref_id', $franchise->code);
            });

        if ($fromDate && $toDate) {
            $ServiceQuery->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }
        $ServiceOrders = $ServiceQuery->get();

        $Services = [];
        foreach ($ServiceOrders as $sv) {
            $service = $sv->service;
            $service_user = $sv->service->creator->name;
            $service_user_id = $sv->service->creator->ref_id;
            //   $product_id = $product->id;

            if (!$service || !$service_user_id)
                continue;
            $service_commission_Amount = ($sv->amount * 5) / 100;
            $Services[] = [
                'serviceboostid' => $sv->id,
                'service_user' => $service_user,
                'service_user_id' => $service_user_id,
                'service_commission_amount' => $service_commission_Amount,
                'created_at' => $sv->created_at->format('d-m-Y'),
            ];
        }
        $service_total_amount = collect($Services)->sum('service_commission_amount');
        //End Service Commision

        /*Jobs Commission List*/
        $JobQuery = JobBoost::where(function ($query) {
            $query->whereNull('settle_id')
                ->orWhere('settle_id', 0);
        })
            ->whereHas('job.user', function ($q) use ($franchise) {
                $q->where('ref_id', $franchise->code);
            });

        if ($fromDate && $toDate) {
            $JobQuery->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }
        $JobOrders = $JobQuery->get();

        $Jobs = [];
        foreach ($JobOrders as $jb) {
            $job = $jb->job;
            $job_user = $jb->job->user->name;
            $job_user_id = $jb->job->user->ref_id;

            if (!$job || !$job_user_id)
                continue;
            $job_commission_Amount = ($jb->amount * 5) / 100;
            $Jobs[] = [
                'job_boostid' => $jb->id,
                'job_user' => $job_user,
                'job_user_id' => $job_user_id,
                'job_commission_amount' => $job_commission_Amount,
                'created_at' => $jb->created_at->format('d-m-Y'),
            ];
        }
        $job_total_amount = collect($Jobs)->sum('job_commission_amount');

        /*Ready To Work Commission*/
        $ReadyQuery = ReadyToWorkBoost::where(function ($query) {
            $query->whereNull('settle_id')
                ->orWhere('settle_id', 0);
        })
            ->whereHas('user', function ($q) use ($franchise) {
                $q->where('ref_id', $franchise->code);
            });

        if ($fromDate && $toDate) {
            $ReadyQuery->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }

        $ReadyOrders = $ReadyQuery->get();
        $Ready = [];
        foreach ($ReadyOrders as $rd) {
            $user = $rd->user;
            $ready_user = $rd->user->name;
            $ready_user_id = $rd->user->ref_id;
            //   $product_id = $product->id;

            if (!$user || !$ready_user_id)
                continue;
            $ready_commission_Amount = ($rd->amount * 5) / 100;


            $Ready[] = [
                'ready_boostid' => $rd->id,
                'ready_user' => $ready_user,
                'ready_user_id' => $ready_user_id,
                'ready_commission_amount' => $ready_commission_Amount,
                'created_at' => $rd->created_at->format('d-m-Y'),
            ];
        }
        $ready_total_amount = collect($Ready)->sum('ready_commission_amount');
        /*End Ready to Work Commision*/

        /*Premium commision amount calculattion*/
        $PremiumQuery = PremiumUser::where(function ($query) {
            $query->whereNull('settle_id')
                ->orWhere('settle_id', 0);
        })
            ->whereHas('user', function ($q) use ($franchise) {
                $q->where('ref_id', $franchise->code);
            });

        if ($fromDate && $toDate) {
            $PremiumQuery->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }

        $PremiumOrders = $PremiumQuery->get();

        $Premium = [];
        foreach ($PremiumOrders as $pr) {
            $user = $pr->user;
            $premium_user = $pr->user->name;
            $premium_user_id = $pr->user->ref_id;
            //   $product_id = $product->id;
            if (!$user || !$premium_user_id)
                continue;
            $premium_commission_Amount = ($pr->price * 5) / 100;
            $Premium[] = [
                'premium_id' => $pr->id,
                'premium_user' => $premium_user,
                'premium_user_id' => $premium_user_id,
                'premium_commission_amount' => $premium_commission_Amount,
                'created_at' => $pr->created_at->format('d-m-Y'),
            ];
        }

        $premium_total_amount = collect($Premium)->sum('premium_commission_amount');
        /*End Premium */

        /*badges Amount Calculation*/
        $BadgeQuery = Badge::where(function ($query) {
            $query->whereNull('settle_id')
                ->orWhere('settle_id', 0);
        })
            ->whereHas('user', function ($q) use ($franchise) {
                $q->where('ref_id', $franchise->code);
            });

        if ($fromDate && $toDate) {
            $BadgeQuery->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }

        $BadgeOrders = $BadgeQuery->get();
        $Badge = [];
        foreach ($BadgeOrders as $bd) {
            $user = $bd->user;
            $badge_user = $bd->user->name;
            $badge_user_id = $bd->user->ref_id;
            //   $product_id = $product->id;

            if (!$user || !$badge_user_id)
                continue;
            $badge_commission_Amount = ($bd->amount * 5) / 100;


            $Badge[] = [
                'badge_id' => $bd->id,
                'badge_user' => $badge_user,
                'badge_user_id' => $badge_user_id,
                'badge_commission_amount' => $badge_commission_Amount,
                'created_at' => $bd->created_at->format('d-m-Y'),
            ];
        }
        $badge_total_amount = collect($Badge)->sum('badge_commission_amount');

        $ProductQuery = ProductBoost::where(function ($query) {
            $query->whereNull('settle_id')
                ->orWhere('settle_id', 0);
        })
            ->whereHas('product.vendor', function ($q) use ($franchise) {
                $q->where('ref_id', $franchise->code);
            });

        if ($fromDate && $toDate) {
            $ServiceQuery->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }
        $ProductOrders = $ProductQuery->get();
        $Product = [];
        foreach ($ProductOrders as $pdt) {
            $product = $pdt->product;
            $product_user = $pdt->product->vendor->name;
            $product_user_id = $pdt->product->vendor->ref_id;
            $product_id = $product->id;

            if (!$product || !$product_user_id)
                continue;
            $product_commission_Amount = ($pdt->amount * 5) / 100;
            $Product[] = [
                'productboostid' => $pdt->id,
                'product_user' => $product_user,
                'product_user_id' => $product_user_id,
                'product_commission_amount' => $product_commission_Amount,
                'created_at' => $pdt->created_at->format('d-m-Y')
            ];
        }
        // dd($Product);
        $product_total_amount = collect($Product)->sum('product_commission_amount');
        //chatbot
        $ChatbotQuery = Chat_bot::where(function ($query) {
            $query->whereNull('settle_id')
                ->orWhere('settle_id', 0);
        })
            ->whereHas('user', function ($q) use ($franchise) {
                $q->where('ref_id', $franchise->code);
            });

        if ($fromDate && $toDate) {
            $ChatbotQuery->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }
        $ChatbotOrders = $ChatbotQuery->get();

        $Chatbot = [];
        foreach ($ChatbotOrders as $ch) {
            $user = $ch->user;
            $chatbot_user = $ch->user->name;
            $chatbot_user_id = $ch->user->ref_id;
            //   $product_id = $product->id;

            if (!$user || !$chatbot_user_id)
                continue;
            $chatbot_commission_Amount = ($ch->amount * 5) / 100;
            $Chatbot[] = [
                'chatbot_id' => $ch->id,
                'chatbot_user' => $chatbot_user,
                'chatbot_user_id' => $chatbot_user_id,
                'chatbot_commission_amount' => $chatbot_commission_Amount,
                'created_at' => $ch->created_at->format('d-m-Y')
            ];
        }

        $chatbot_total_amount = collect($Chatbot)->sum('chatbot_commission_amount');

        //project
        $ProjectQuery = Project::where(function ($query) {
            $query->whereNull('settle_id')
                ->orWhere('settle_id', 0);
        })
            ->whereHas('creator', function ($q) use ($franchise) {
                $q->where('ref_id', $franchise->code);
            });

        if ($fromDate && $toDate) {
            $ProjectQuery->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }
        $ProjectOrders = $ProjectQuery->get();

        $Project = [];
        foreach ($ProjectOrders as $pjt) {
            $user = $pjt->creator;
            $project_user = $pjt->creator->name;
            $project_user_id = $pjt->creator->ref_id;
            //   $product_id = $product->id;

            if (!$user || !$project_user_id)
                continue;
            $project_commission_Amount = ($pjt->amount * 5) / 100;

            $Project[] = [
                'project_id' => $pjt->id,
                'project_user' => $project_user,
                'project_user_id' => $project_user_id,
                'project_commission_amount' => $project_commission_Amount,
                'created_at' => $pjt->created_at->format('d-m-Y')
            ];
        }
        $project_total_amount = collect($Project)->sum('project_commission_amount');

        return view('admin.franchise_settlement', compact(
            'franchise',
            'totalCommissionAmount',
            'results',
            'franchises_dropdown',
            'fromDate',
            'toDate',
            'Leads',
            'lead_total_amount',
            'Services',
            'service_total_amount',
            'Jobs',
            'job_total_amount',
            'Ready',
            'ready_total_amount',
            'Premium',
            'premium_total_amount',
            'Badge',
            'badge_total_amount',
            'Product',
            'product_total_amount',
            'Chatbot',
            'chatbot_total_amount',
            'Project',
            'project_total_amount'

        ));
    }

    public function franchise_amount_store(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        if ($request->has('results')) {

            $franchiseId = $request->input('franchise_id');
            $franchiseName = $request->input('franchise_name');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $productCommission = $request->input('total_product_commission');
            $leadCommission = $request->input('total_lead_commission');
            $serviceCommission = $request->input('total_service_commission');
            $jobCommission = $request->input('total_job_commission');
            $readyCommission = $request->input('total_ready_commission');
            $premiumCommission = $request->input('total_premium_commission');
            $badgeCommission = $request->input('total_badge_commission');
            $productCommission = $request->input('total_product_commission');
            $chatbotCommission = $request->input('total_chatbot_commission');
            $projectCommission = $request->input('total_project_commission');
            $totalCommission = $productCommission + $leadCommission + $serviceCommission + $jobCommission + $readyCommission + $premiumCommission + $badgeCommission + $productCommission + $chatbotCommission + $projectCommission;
            $results = json_decode($request->input('results'), true);
            $leadResults = json_decode($request->input('leads'), true) ?? [];
            $serviceResults = json_decode($request->input('service'), true) ?? [];
            $jobResults = json_decode($request->input('job'), true) ?? [];
            $readyResults = json_decode($request->input('ready'), true) ?? [];
            $premiumResults = json_decode($request->input('premium'), true) ?? [];
            $badgeResults = json_decode($request->input('badge'), true) ?? [];
            $productResults = json_decode($request->input('product'), true) ?? [];
            $chatbotResults = json_decode($request->input('chatbot'), true) ?? [];
            $projectResults = json_decode($request->input('project'), true) ?? [];
            $Amount = [];
            $Amount['sales'] = $productCommission;
            $Amount['leads'] = $leadCommission;
            $Amount['service'] = $serviceCommission;
            $Amount['job'] = $jobCommission;
            $Amount['ready'] = $readyCommission;
            $Amount['premium'] = $premiumCommission;
            $Amount['badge'] = $badgeCommission;
            $Amount['products'] = $productCommission;
            $Amount['chatbot'] = $chatbotCommission;
            $Amount['project'] = $projectCommission;
            $Amount['total'] = $totalCommission;

            $settle = Settlement::create([
                'franchise_id' => $franchiseId,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'amount' => json_encode($Amount)
                // 'amount' => json_decode($json, true), 
                //'details' => json_encode($results),
            ]);
            if ($settle && !empty($results)) {
                foreach ($results as $row) {
                    OrderProducts::where('id', $row['order_products_id'])
                        ->update([
                            'settle_amount' => $row['commission_amount'],
                            'settle_id' => $settle->id,
                            'frn_updated_date' => now(),
                        ]);
                }
            }
            if ($settle && !empty($leadResults)) {
                foreach ($leadResults as $lead) {
                    OwnedLeads::where('id', $lead['owned_leadid'])
                        ->update([
                            'settle_amount' => $lead['lead_commission_amount'],
                            'settle_id' => $settle->id,
                            'frn_updated_date' => now(),
                        ]);
                }
            }
            if ($settle && !empty($serviceResults)) {
                foreach ($serviceResults as $service) {
                    ServiceBoost::where('id', $service['serviceboostid'])
                        ->update([
                            'settle_amount' => $service['service_commission_amount'],
                            'settle_id' => $settle->id,
                            'frn_updated_date' => now(),
                        ]);
                }
            }
            if ($settle && !empty($jobResults)) {
                foreach ($jobResults as $job) {
                    JobBoost::where('id', $job['job_boostid'])
                        ->update([
                            'settle_amount' => $job['job_commission_amount'],
                            'settle_id' => $settle->id,
                            'frn_updated_date' => now(),
                        ]);
                }
            }

            if ($settle && !empty($readyResults)) {
                foreach ($readyResults as $ready) {
                    ReadyToWorkBoost::where('id', $ready['ready_boostid'])
                        ->update([
                            'settle_amount' => $ready['ready_commission_amount'],
                            'settle_id' => $settle->id,
                            'frn_updated_date' => now(),
                        ]);
                }
            }

            if ($settle && !empty($premiumResults)) {
                foreach ($premiumResults as $premium) {
                    PremiumUser::where('id', $premium['premium_id'])
                        ->update([
                            'settle_amount' => $premium['premium_commission_amount'],
                            'settle_id' => $settle->id,
                            'frn_updated_date' => now(),
                        ]);
                }
            }

            if ($settle && !empty($badgeResults)) {
                foreach ($badgeResults as $badge) {
                    Badge::where('id', $badge['badge_id'])
                        ->update([
                            'settle_amount' => $badge['badge_commission_amount'],
                            'settle_id' => $settle->id,
                            'frn_updated_date' => now(),
                        ]);
                }
            }

            if ($settle && !empty($productResults)) {
                foreach ($productResults as $product) {
                    ProductBoost::where('id', $product['productboostid'])
                        ->update([
                            'settle_amount' => $product['product_commission_amount'],
                            'settle_id' => $settle->id,
                            'frn_updated_date' => now(),
                        ]);
                }
            }

            if ($settle && !empty($chatbotResults)) {
                foreach ($chatbotResults as $chatbot) {
                    Chat_bot::where('id', $chatbot['chatbot_id'])
                        ->update([
                            'settle_amount' => $chatbot['chatbot_commission_amount'],
                            'settle_id' => $settle->id,
                            'frn_updated_date' => now(),
                        ]);
                }
            }

            if ($settle && !empty($projectResults)) {
                foreach ($projectResults as $project) {
                    Project::where('id', $project['project_id'])
                        ->update([
                            'settle_amount' => $project['project_commission_amount'],
                            'settle_id' => $settle->id,
                            'frn_updated_date' => now(),
                        ]);
                }
            }
        }
        return redirect()
            ->route('franchise_settlement')
            ->with('success', 'Franchise settlement stored successfully!');
    }

    public function franchise_amt(Request $request, $user_id, $frn_id, $type)
   {
        // 1️⃣ Get franchise info
        $franchise = Franchise::find($frn_id);
        if (!$franchise) {
            return response()->json(['error' => 'Franchise not found'], 404);
        }

        // 2️⃣ Get vendor who is of type 'Vendor'
        $user = UserDetail::where('id', $user_id)
            // ->where('as_a', 'Vendor')
            ->first();

        if (!$user) {
            return response()->json(['error' => 'Vendor not found or not valid'], 404);
        }

        // 3️⃣ Get all delivered orders for this vendor and this franchise
        $orderProducts = OrderProducts::with(['product.vendor', 'order'])
            ->where('status', 'delivered')
            ->where('settle_amount', '!=', 0)
            ->whereHas('product.vendor', function ($query) use ($user, $franchise,$type) {
                $query->where('id', $user->id)
                    ->where('ref_id', $franchise->code)
                    ->where('as_a', $type);
            })
            ->get()
            ->groupBy('order_id')
            ->map(function ($group) {
                $total_settle = $group->sum('settle_amount');
                return [
                    'order_id'     => $group->first()->order_id,
                    'total_settle' => $total_settle,
                    'frn_date'       => $group->first()->frn_updated_date,
                    'products'     => $group->values(),
                ];
            })
            ->values();
         
            //chatbot income indvidual person
            $ChatbotQuery = Chat_bot::where(function ($query) use ($user) {
                $query->whereNotNull('settle_id')   // ✔ settle_id must NOT be null
                      ->where('c_by', '=',$user->id )
                      ->where('settle_amount', '!=', 0);
            })
            ->whereHas('user', function ($q) use ($franchise) {
                $q->where('ref_id', $franchise->code);
            });
            
            $ChatbotOrders = $ChatbotQuery->get();
            //dd($ChatbotOrders);
    

            //ready to work invidual person
            $ReadyQuery = ReadyToWorkBoost::where(function ($query) use ($user)  {
                $query->whereNotNull('settle_id')
                      ->where('user_id', '=',$user->id )
                       ->where('settle_amount', '!=', 0);
            })
                ->whereHas('user', function ($q) use ($franchise) {
                    $q->where('ref_id', $franchise->code);
                });
    
          
            $readyincome = $ReadyQuery->get();
           // dd($readyincome);
           //premium
           $PremiumQuery = PremiumUser::where(function ($query) use($user) {
                $query->whereNotNull('settle_id')
                ->where('user_id', '=',$user->id)
                ->where('settle_amount', '!=', 0);
               
            })
                ->whereHas('user', function ($q) use ($franchise) {
                   $q->where('ref_id', $franchise->code);
            });

           $premiumincome = $PremiumQuery->get();

           //badge individual details
           $BadgeQuery = Badge::where(function ($query) use($user) {
            $query->whereNotNull('settle_id')
                  ->where('created_by', '=',$user->id)
                  ->where('settle_amount', '!=', 0);
        })
            ->whereHas('user', function ($q) use ($franchise) {
                $q->where('ref_id', $franchise->code);
            });

          $badgeincome = $BadgeQuery->get();

         //Project individual details
          $ProjectQuery = Project::where(function ($query) use($user) {
            $query->whereNotNull('settle_id')
                ->where('created_by', '=',$user->id)
                ->where('settle_amount', '!=', 0);
        })
            ->whereHas('creator', function ($q) use ($franchise) {
                $q->where('ref_id', $franchise->code);
            });

          $projectincome = $ProjectQuery->get();
        //dd($projectincome);
      
        //Product individual details
        $ProductQuery = ProductBoost::where(function ($query)  {
            $query->whereNotNull('settle_id')
                 ->where('settle_amount', '!=', 0);
        })
            ->whereHas('product.vendor', function ($q) use ($franchise,$user) {
                $q->where('ref_id', $franchise->code)
                ->where('id', $user->id);       
            });

        $productincome = $ProductQuery->get();
        //dd($productincome);

         /*Jobs Commission List*/
         $JobQuery = JobBoost::where(function ($query) {
            $query->whereNotNull('settle_id')
                 ->where('settle_amount', '!=', 0);
        })
            ->whereHas('job.user', function ($q) use ($franchise,$user) {
                $q->where('ref_id', $franchise->code)
                   ->where('id', $user->id);      
            });

        $jobincome = $JobQuery->get();
        //dd($JobOrders);

        //service individual details
        $ServiceQuery = ServiceBoost::where(function ($query) {
            $query->whereNotNull('settle_id')
                 ->where('settle_amount', '!=', 0);
        })
            ->whereHas('service.creator', function ($q) use ($franchise,$user) {
                $q->where('ref_id', $franchise->code)
                  ->where('id', $user->id); 
            });

        
        $serviceincome = $ServiceQuery->get();

        //lead income calculate
        $LeadQuery = OwnedLeads::with(['lead.user'])

        ->where(function ($query) {
                $query->whereNotNull('settle_id')
                     ->where('settle_amount', '!=', 0);
            })
            ->whereHas('lead.user', function ($query) use ($franchise,$user) {
                $query->where('ref_id', $franchise->code)
                      ->where('id', $user->id); 
            });

        $leadincome = $LeadQuery->get();


           //dd($badgeincome);
        //     $results = [];
        //     //dd($results);
        //     foreach ($orderProducts as $op) {
        //         $product = $op->product;
        //         $vendor = $product?->vendor;
        //         $vendor_ref_id = $product?->vendor->ref_id;
        //         $product_id = $product->id;
    
        //         if (!$product || !$vendor)
        //             continue;
    
              
    
        //         $results[] = [
        //             'order_products_id' => $op->id,
        //             'order_id' => $op->order_id,
        //             'vendor_name' => $vendor->name ?? 'N/A',
        //             'vendor_ref_id' => $vendor_ref_id,
        //             'product_id' => $product_id,
        //             'settle_amount' => $op->settle_amount,
                   
        //         ];
        //     }
    
        //  //dd($results);
        // 5️⃣ Return data to your view
        return view('admin.franchise_amt', [
            'franchise' => $franchise,
            'user' => $user,
            'orderProducts' => $orderProducts,
            'type' => $type,
            'chatbotincome' => $ChatbotOrders,
            'readyincome' => $readyincome,
            'premiumincome' => $premiumincome,
            'badgeincome' => $badgeincome,
            'projectincome' => $projectincome,
            'productincome' => $productincome,
            'jobincome' => $jobincome,
            'serviceincome' => $serviceincome,
            'leadincome'  => $leadincome
            //'settle_amount'=>$settle_amount
           // 'totalSettleAmount' => $totalSettleAmount,
        ]);
    }

}
