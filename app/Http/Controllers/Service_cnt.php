<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Click;
use App\Models\DropdownList;
use App\Models\Notification;
use App\Models\Posts;
use App\Models\Service;
use App\Models\ServiceBoost;
use App\Models\ServiceRequest;
use App\Models\ServiceReview;
use App\Models\UserDetail;
use App\Services\Aws;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Service_cnt extends Controller
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

    public function index(Request $request)
    {
        // if ($request->ajax()) {
        //     dd('ajax request');
        //     // Log::info($request->all());
        //     // return response()->json(['message' => 'AJAX request received']);
        // }

        $location = Auth::user()->location ?? '';
        $serviceTypes = DropdownList::where('dropdown_id', 5)->pluck('value', 'id');
        $locations = DropdownList::where('dropdown_id', 1)->pluck('value', 'id');
        // $services = Cache::remember('service_cache', 2, function () use ($location) {
        $query = Service::with('serviceType', 'locationRelation')
            ->where('status', 'active')
            ->withCount('reviews')
            ->withAvg('reviews', 'stars')
            ->where('created_by', '!=', Auth::id())
            ->where('approvalstatus', 'approved');

        // dd($query->toSql());

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%')
                    // ->orWhere('specifications', 'like', '%'.$keyword.'%')
                    // ->orWhere('key_feature', 'like', '%'.$keyword.'%')
                    ->orWhereHas('locationRelation', function ($q2) use ($keyword) {
                        $q2->where('value', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('serviceType', function ($q2) use ($keyword) {
                        $q2->where('value', 'like', '%' . $keyword . '%');
                    });
            });
        }

        // Categories (array of values)
        if ($request->filled('categories')) {
            $categories = is_array($request->categories) ? $request->categories : [$request->categories];
            $query->whereHas('serviceType', function ($q) use ($categories) {
                $q->whereIn('value', $categories);
            });
        }

        // Location
        if ($request->filled('locations')) {
            $locations = is_array($request->locations) ? $request->locations : [$request->locations];
            $query->whereHas('locationRelation', function ($q) use ($locations) {
                $q->whereIn('value', $locations);
            });
        }
        // budget
        if ($request->filled('budget')) {
            $query->where('price_per_sq_ft', '<=', $request->budget);
        }
        // highlighted
        if ($request->filled('highlight')) {
            $query->where('highlighted', $request->highlight);
        }

        $query->where('created_at', '>=', now()->subMonths(6)); // ✅ last 6 months
        $query->orderByRaw("
                CASE
                    WHEN highlighted = 1 AND location = '{$location}' THEN 1
                    WHEN highlighted = 1 THEN 2
                    WHEN location = '{$location}' THEN 3
                    ELSE 4
                END, created_at DESC
            ");
        $query->orderByDesc('created_at') // ✅ secondary order
            ->orderByDesc('id');

        // dd($query->toArray());

        $cursor = $request->input('cursor'); // get cursor from query string

        $services = $query->cursorPaginate(
            7,        // per page
            ['*'],    // select all columns
            'cursor', // query param name
            $cursor   // actual cursor value
        );

        // dd($services);

        // $services = $query->cursorPaginate(10);
        $next_page_url = $services->nextPageUrl();
        if ($request->ajax() || $request->header('Authorization')) {
            // dd('services index');
            if ($request->header('Authorization')) {
                return response()->json(['success' => true, 'data' => [

                    'data' => $services->getCollection()->isEmpty() ? [] : $services->items(), // empty array if no data
                    'service_location' => $service->locationRelation->value ?? null,
                    'service_type' => $service->serviceType->value ?? null,
                    'next_page_url' => $services->nextCursor()?->encode() ?? null,
                    'next_page_url_1' => $next_page_url,
                ]]);
            }

            return response()->json([
                'data' => $services->getCollection()->isEmpty() ? [] : $services->items(), // empty array if no data
                'next_page_url' => $next_page_url,
            ]);
        }

        return view('services.services', compact('services', 'locations', 'serviceTypes', 'next_page_url'));
    }

    public function show(Request $req, $id = null)
    {
        
        try {
            if ($req->service_id) {
                $id = $req->service_id;
            }
            $service = Service::with('creator:id,name,as_a,badge,profile_img,address,number,email,web_token,mob_token', 'serviceType:id,value', 'locationRelation:id,value')
                ->withCount('reviews')
                ->withAvg('reviews', 'stars')
                ->findOrFail($id);
            $reviews = ServiceReview::where('service_id', $id)->with('user:id,name,as_a,badge,profile_img')->get();

            

            $serviceTypes = DropdownList::where('dropdown_id', 5)->pluck('value', 'id');

            $locations = DropdownList::where('dropdown_id', 1)->pluck('value', 'id');

            $sub_images = json_decode($service->sub_images, true);
            // Convert image paths to full URLs
            $fullImagePaths = [];

            if (is_array($sub_images)) {
                foreach ($sub_images as $img) {
                    $fullImagePaths[] = $img;
                }
            }

            if (!empty($service->video)) {
                $fullImagePaths[] = '"' . $service->video . '"';
            }


            if ($service->highlighted && $service->created_by != Auth::id()) {
                
                $boostId = serviceBoost::where('service_id', $service->id)
                ->where('type', 'click')
                ->where('status', 'active')
                ->latest()
                ->value('id');

                $flag = Click::where('category', 'Service')
                ->where('category_id', $id)
                ->where('boost_id', $boostId)
                ->where('created_by', Auth::id())->exists();
                if (!$flag) {
                    $service->decrement('click');
                    Click::create([
                        'category' => 'Service',
                        'category_id' => $service->id,
                        'boost_id' => $boostId,
                        'status' => 'active',
                        'created_by' => Auth::id() ?? 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if ($service->click == 0) {
                        $service->decrement('highlighted');
                        $boost = serviceBoost::where('service_id', $service->id)
                        ->where('type', 'click')
                        ->where('status', 'active')
                        ->latest()
                        ->first();
                        
                        if ($boost) {
                            $boost->update(['status' => 'inactive']);
                        }
                        

                        Posts::where('category', 'service')->where('category_id', $service->id)->update(['status' => 'deleted']);
                        
                        $this->notifyUser(
                            $service->created_by,
                            'Service Highlight Expired',
                            'Your service "' . $service->title . '" highlighting has been expired.',
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
                                'body' => 'Your service "' . $service->title . '" highlight has been expired',
                                'id' => $service->id,
                                'link' => route('service.show', ['id' => $service->id]),
                            ];
                            $this->notificationService->token($data);
                        }
                    }
                }
            } elseif ($service->created_by == Auth::id()) {
                // dd('here');

                $serviceTypes = DropdownList::where('dropdown_id', 5)->pluck('value', 'id');
                $locations = DropdownList::where('dropdown_id', 1)->pluck('value', 'id');

                if ($req->header('Authorization')) {
                return response()->json(['success' => true, 'data' => [
                    'service' => $service,
                    'service_type' => $service->serviceType->value ?? null,
                    'service_loc' => $service->locationRelation->value ?? null,
                    'all_media' => $fullImagePaths,
                    'reviews' => $reviews,
                    'review_status' => true,
                ]]);
            }

                return view('services.view', compact('service', 'locations', 'serviceTypes', 'reviews'));
            }
            
            // if ($req->header('Authorization')) {
            //     return response()->json(['success' => true, 'data' => [
            //         'service' => $service,
            //         'reviews' => $reviews,
            //     ]]);
            // }
            // dd('here');
            $review_status = ServiceReview::where('service_id', $id)->where('c_by', Auth::id())->exists();
            // dd($review_status);
            if ($req->header('Authorization')) {
                return response()->json(['success' => true, 'data' => [
                    'service' => $service,
                    'service_type' => $service->serviceType->value ?? null,
                    'service_loc' => $service->locationRelation->value ?? null,
                    'all_media' => $fullImagePaths,
                    'reviews' => $reviews,
                    'review_status' => $review_status,
                ]]);
            }

            return view('services.individual_service', compact('service', 'serviceTypes', 'locations', 'reviews'));
        } catch (\Exception $er) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $er->getMessage(),
            ], 500);
        }
    }

    public function storeServiceRequest(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'service_id' => 'required|exists:services,id',
            'service_type' => 'required|string',
            'addBuildup' => 'required|numeric|min:1',
            'addBudget' => 'required|numeric|min:1',
            'addStartDate' => 'required|date|after:today',
            'addLoc' => 'required|string|max:255',
            'addContact' => 'required|digits:10|regex:/^[6-9][0-9]{9}$/',
            'addDescp' => 'required|string|max:1000',
        ]);

        $service = Service::findOrFail($validatedData['service_id']);

        try {
            // Create the service request
            $serviceRequest = ServiceRequest::create([
                'c_by' => Auth::id(),
                'service_id' => $validatedData['service_id'],
                'service_type' => $validatedData['service_type'],
                'buildup_area' => $validatedData['addBuildup'],
                'budget' => $validatedData['addBudget'],
                'start_date' => $validatedData['addStartDate'],
                'location' => $validatedData['addLoc'],
                'phone_number' => $validatedData['addContact'],
                'description' => $validatedData['addDescp'],
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->notifyUser(
                $serviceRequest->service->created_by,
                'New Service Request',
                'You have received a new service request for "' . $serviceRequest->service->title . '".',
                $serviceRequest->service->id
            );
            
            if ($service->creator && ($service->creator->web_token || $service->creator->mob_token)) {
                
                $data = [
                    'web_token' => $service->creator->web_token,
                    'mob_token' => $service->creator->mob_token ?? null,
                    'title' => 'New Service Request',
                    'body' => 'Your service "' . $service->title . '" got requested by ' . Auth::user()->name,
                    'id' => $service->id,
                    'link' => route('service.show', ['id' => $service->id]),
                ];
                $this->notificationService->token($data);
                dd('here');
            }
            if ($request->header('Authorization')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Service request submitted successfully!',

                ]);
            }

            return redirect()->back()->with('success', 'Service Request Submitted Successfully!');
        } catch (\Exception $e) {
            if ($request->header('Authorization')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service request not submitted!',

                ]);
            }
            return redirect()->back()->with('error', 'Failed to submit service request: ' . $e->getMessage());
        }
    }

    // Store Service
    public function storeService(Request $request, Aws $aws)
    {
        // Log::info($request->all());
        $rules = [
            'addTitle' => 'required|string|max:255',
            'addServicetype' => 'required|string|max:255',
            'addPrice' => 'required|numeric|min:0',
            'addLoc' => 'required|string|max:255',
            'addDescp' => 'required|string',
        ];

        foreach (['service-image-1', 'service-image-2', 'service-image-3', 'service-image-4'] as $field) {
            $rules[$field] = [
                $field === 'service-image-1' ? 'required' : 'nullable',
                'file'
            ];
        }

        $request->validate($rules);
        $s3Keys = [];

        foreach (['service-image-1', 'service-image-2', 'service-image-3', 'service-image-4'] as $field) {
            if ($request->hasFile($field)) {
                $uploadedFiles = $request->file($field);

                if (! is_array($uploadedFiles)) {
                    $uploadedFiles = [$uploadedFiles];
                }
                $folder = 'service_images';
                $s3Results = $aws->common_upload_to_s3($uploadedFiles, $folder);
                $s3Keys[] = is_array($s3Results) ? $s3Results[0] : $s3Results;
            }
        }

        $video = null;
        if ($request->hasFile('addVideo')) {
            $videofile = $request->file('addVideo');

            if (! is_array($videofile)) {
                $videofile = [$videofile];
            }
            $folder = 'service_video';
            $videoresult = $aws->common_upload_to_s3($videofile, $folder);
            $video = is_array($videoresult) ? $videoresult[0] : $videoresult;
        }

        $total_amount = Charge::where('category', 'service_list')->latest()->value('charge') * 1.18;

        $userId = Auth::id() ?? 1;

        $service = Service::create([
            'created_by' => $userId,
            'title' => $request->addTitle,
            'service_type' => $request->addServicetype,
            'price_per_sq_ft' => $request->addPrice,
            'location' => $request->addLoc,
            'description' => $request->addDescp,
            'image' => $s3Keys[0] ?? null,
            'sub_images' => json_encode($s3Keys),
            // 'video'      => $video,
            'click' => 0,
            'highlighted' => 0,
            'approvalstatus' => 'pending',
            'status' => 1,
        ]);

        ServiceBoost::create([
            'service_id' => $service->id,
            'type' => 'list',
            'click' => 0,
            'amount' => $total_amount,
            'status' => 'active',
        ]);
        // if ($s3Key) {
        //     Posts::create([
        //         'file_type' => 'image',
        //         'category' => 'service',
        //         'category_id' => $service->id,
        //         'file' => $s3Key,
        //         'caption' => $request->addDescp,
        //         'location' => $request->addLoc,
        //         // 'created_by' => Auth::id(),
        //         'created_by' => $userId,
        //         'status' => 'inactive'
        //     ]);
        // }
        // if ($video) {
        //     Posts::create([
        //         'file_type' => 'video',
        //         'category' => 'service',
        //         'category_id' => $service->id,
        //         'file' => [$video],
        //         'caption' => $request->addDescp,
        //         'location' => $request->addLoc,
        //         'created_by' => Auth::id(),
        //         'status' => 'inactive'
        //     ]);
        // }
        // dd($request->all(), $s3Key, $video, $total_amount); 'job_id' => $job->id

        // sa-new

        UserDetail::where('id', $userId)
            ->where('balance', '>=', $total_amount)
            ->decrement('balance', $total_amount);

        if ($request->header('Authorization')) {
            return response()->json(
                [
                    'message' => 'Service added Successfully',
                    'success' => true,
                ],
                200
            );
        }

        return redirect()->back()->with('success', 'Service Added Successfully!');
    }

    // Update Service
    public function updateService(Request $request, Aws $aws, $id = null)
    {
        try {

            $service = Service::where('id', $id)
                ->where('created_by', Auth::id())
                ->firstOrFail();

            $rules = [
                'title' => 'required|string|max:255',
                'servicetype' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'location' => 'required|string|max:255',
                'description' => 'required|string',
            ];

            foreach (['service-image-1', 'service-image-2', 'service-image-3', 'service-image-4'] as $field) {
                $rules[$field] = ['nullable', 'file'];
            }

            $request->validate($rules);

            $data = [
                'title' => $request->title,
                'service_type' => $request->servicetype,
                'price_per_sq_ft' => $request->price,
                'location' => $request->location,
                'description' => $request->description,
            ];

            $s3Keys = json_decode($service->sub_images ?? '[]', true);
            $oldMainImage = $service->image ?? null;
            $folder = 'service_images';
            $fields = ['service-image-1', 'service-image-2', 'service-image-3', 'service-image-4'];

            for ($i = 0; $i < count($fields); $i++) {
                $field = $fields[$i];
                if ($request->hasFile($field)) {
                    // Delete old file if exists
                    if (! empty($s3Keys[$field])) {
                        Storage::disk('s3')->delete($s3Keys[$field]);
                    }
                    $uploaded = $aws->common_upload_to_s3([$request->file($field)], $folder);
                    $s3Keys[$i] = $uploaded[0];
                }
            }

            $data['image'] = $s3Keys[0] ?? $oldMainImage;
            $data['sub_images'] = json_encode($s3Keys);
            $data['approvalstatus'] = 'pending';
            $service->update($data);

            if ($request->header('Authorization')) {
                return response()->json([
                    'message' => 'Service Details',
                    'success' => true,
                    'data' => 'service update successfully',
                    // 'job_id' => $job->id,

                ], 200);
            }

            return redirect()->back()->with('success', 'Service Updated Successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update service: ' . $e->getMessage());
        }
    }

    public function deleteService($id)
    {
        $service = Service::where('id', $id)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        // Delete image file
        if ($service->image_or_catalogue && file_exists(public_path($service->image_or_catalogue))) {
            unlink(public_path($service->image_or_catalogue));
        }

        $service->delete();

        return redirect()->back()->with('success', 'Service Deleted Successfully!');
    }

    // FIXED: Changed from Service to ServiceModel
    public function getService(Request $request, $id = null)
    {
        // Log::info($request->all());
        try {

            if ($request->service_id) {
                $id = $request->service_id;
            }
            $service = Service::with('locationRelation')
                ->with('serviceType')
                ->with('locationRelation')
                ->where('id', $id)
                ->where('created_by', Auth::id())
                ->firstOrFail();
            $sub_images = json_decode($service->sub_images, true);

            // Convert image paths to full URLs
            $fullImagePaths = [];
            if (is_array($sub_images)) {
                foreach ($sub_images as $index => $img) {
                    if ($index === 0) {
                        continue;
                    } // Skip first image
                    $fullImagePaths[] = $img;  // Generates full URL like http://yourdomain.com/service_images/img.jpg
                }
            }
            $data = [
                'service_id' => $service->id,
                'title' => $service->title,
                'service_type' => $service->serviceType->value ?? null,
                'location' => $service->locationRelation->value ?? null,
                'price' => $service->price_per_sq_ft,
                'description' => $service->description,
                'cover_image' => $service->image,
                'service_images' => $fullImagePaths,
                // 'remarks' => $service->remark,
                // 'service_boost' => $service->highlighted ?? null,
                // 'reviews_count' => $service->reviews_count ?? 0,
                // 'reviews_avg_stars' => $service->reviews_avg_stars ? round($service->reviews_avg_stars, 1) : null,
                // 'creator_email' => $service->creator->email ?? null,
                // 'creator_address' => $service->creator->address ?? null,
                // 'created_at' => $service->created_at->toDateTimeString(),
            ];
            if ($request->header('Authorization')) {
                return response()->json([
                    'message' => 'Service Details',
                    'success' => true,
                    'data' => $data,

                ], 200);
            }

            return response()->json($service);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Service not found'], 404);
        }
    }

    public function request(Request $request)
    {
        $serviceTypes = DropdownList::where('dropdown_id', 5)->pluck('value', 'id');
        $locations = DropdownList::where('dropdown_id', 1)->pluck('value', 'id');
        $serviceRequests = ServiceRequest::with(['user', 'service.locationRelation', 'service.serviceType'])
            ->whereHas('service', function ($query) {
                $query->where('created_by', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->get();
        if ($request->header('Authorization')) {
            return response()->json([
                'success' => true,
                'message' => 'Service Requested List',
                'data' => $serviceRequests,
                // 'job_id' => $job->id,

            ], 200);
        }

        return view('services.request', compact('serviceRequests', 'locations', 'serviceTypes'));
    }

    public function requested(Request $request)
    {
        $serviceTypes = DropdownList::where('dropdown_id', 5)->pluck('value', 'id');
        $locations = DropdownList::where('dropdown_id', 1)->pluck('value', 'id');
        $serviceRequests = ServiceRequest::where('c_by', Auth::id())
            ->with('service')
            ->orderBy('created_at', 'desc')
            ->get();
        $userReviews = ServiceReview::where('c_by', Auth::id())
            ->whereIn('service_id', $serviceRequests->pluck('service_id'))
            ->get()
            ->keyBy('service_id');
        if ($request->header('Authorization')) {
            return response()->json([
                'success' => true,
                'message' => 'Service Requested List',
                'data' => $serviceRequests,
                // 'job_id' => $job->id,

            ], 200);
        }

        return view('services.requested', compact('serviceRequests', 'locations', 'serviceTypes', 'userReviews'));
    }

    public function highlightService(Request $request, Aws $aws)
    {
        // dd($request->all());
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'clicks' => 'required|integer|min:1',
            'servicevideo' => [
                'nullable',
                'file',
                function ($attribute, $value, $fail) use ($request) {
                    $file = $request->file('servicevideo'); // ✅ get the UploadedFile object

                    if (! $file || ! $file->isValid()) {
                        $fail('The uploaded file is not valid.');

                        return;
                    }

                    $allowedImages = ['jpg', 'jpeg', 'png'];
                    $allowedVideos = ['mp4'];
                    $extension = strtolower($file->getClientOriginalExtension());

                    if (! in_array($extension, array_merge($allowedImages, $allowedVideos))) {
                        $fail("The $attribute must be a valid image or video file.");
                    }
                },
            ],
        ]);

        $video = null;
        if ($request->hasFile('servicevideo')) {
            $videofile = $request->file('servicevideo');
            if (! is_array($videofile)) {
                $videofile = [$videofile];
            }
            $folder = 'service_video';
            $videoresult = $aws->common_upload_to_s3($videofile, $folder);
            $video = is_array($videoresult) ? $videoresult[0] : $videoresult;
            // dd($videoresult);
        }

        $service_day_charge = Charge::where('category', 'service_highlight')->latest()->value('charge') * 1.18;
        $total_amount = $service_day_charge * $request->clicks;

        UserDetail::where('id', Auth::id())
            ->where('balance', '>=', $total_amount) // ensure balance is sufficient
            ->decrement('balance', $total_amount);

        ServiceBoost::create([
            'service_id' => $request->service_id,
            'type' => 'click',
            'amount' => $total_amount,
            'click' => $request->clicks,
            'status' => 'active',
        ]);

        $service = Service::find($request->service_id);
        Service::where('id', $request->service_id)->update(['highlighted' => 1, 'click' => $request->clicks]);
        Posts::create([
            'file_type' => 'image',
            'category' => 'service',
            'category_id' => $service->id,
            'file' => [$service->image],
            'caption' => $service->description,
            'location' => $service->locationRelation->value,
            'created_by' => Auth::id(),
            'status' => 'active',
        ]);
        if ($request->servicevideo) {
            Posts::create([
                'file_type' => 'video',
                'category' => 'service',
                'category_id' => $service->id,
                'file' => [$video],
                'caption' => $service->description,
                'location' => $service->locationRelation->value,
                'created_by' => Auth::id(),
                'status' => 'active',
            ]);
            $service->update([
                'video' => $video,
            ]);
        }
        if ($request->header('Authorization')) {
            return response()->json([
                'message' => 'Service Highlighted successfully!',
                'success' => true,
                // 'job_id' => $job->id,

            ], 200);
        }

        return redirect()->back()->with('success', 'Service Highlighted Successfully!');
    }

    public function viewService($id)
    {
        $service = Service::with('creator')
            ->withCount('reviews')
            ->withAvg('reviews', 'stars')
            ->findOrFail($id);
        if ($service->created_by != Auth::id()) {
            return redirect()->back()->with('success', 'You can\'t view this service.');
        }
        $reviews = ServiceReview::where('service_id', $id)->with('user')->get();
        $serviceTypes = DropdownList::where('dropdown_id', 5)->pluck('value', 'id');
        $locations = DropdownList::where('dropdown_id', 1)->pluck('value', 'id');
        return view('services.view', compact('service', 'locations', 'serviceTypes', 'reviews'));
    }

    public function toggleServiceStatus(Request $request)
    {
        $service = Service::findOrFail($request->id);
        $posts = Posts::where('category', 'service')
            ->where('category_id', $request->id)
            ->where('status', '!=', 'deleted')
            ->latest()
            ->get();
        $newStatus = $service->status === 'active' ? 'inactive' : 'active';
        $service->status = $newStatus;
        $service->save();

        if ($posts->isNotEmpty()) {
            // dd('here');
            $posts->each(function ($post) use ($newStatus) {
                $post->status = $newStatus;
                $post->save();
            });
        }

        return response()->json([
            'status' => $service->status,
            'message' => 'Status updated successfully.',
        ]);
    }

    public function review(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
        ]);
        ServiceReview::create([
            'service_id' => $request->service_id,
            'c_by' => Auth::id() ?? 1,
            'review' => $request->review,
            'stars' => $request->rating,
        ]);
        if ($request->header('Authorization')) {
            return response()->json([
                'message' => 'Review submitted successfully',
                'success' => true,
            ], 200);
        }

        return response()->json(['success' => 'Review Posted Successfully!', 'service_id' => $request->service_id]);
    }
}
