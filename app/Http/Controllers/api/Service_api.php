<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Service;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\ServiceReview;
use Illuminate\Support\Facades\Storage;
use App\Services\Aws;
use App\Models\Charge;
use App\Models\Posts;


class Service_api extends Controller
{
    // function to create A service request

    // public function create_service_request(Request $request)
    // {
    //     // Validate the incoming request
    //     $validatedData = $request->validate([
    //         'service_id' => 'required|exists:services,id',
    //         'service_type' => 'required|string',
    //         'addBuildup' => 'required|numeric|min:1',
    //         'addBudget' => 'required|numeric|min:1',
    //         'addStartDate' => 'required|date|after:today',
    //         'addLoc' => 'required|string|max:255',
    //         'addContact' => 'required|digits:10|regex:/^[6-9][0-9]{9}$/',
    //         'addDescp' => 'required|string|max:1000',
    //     ]);

    //     try {
    //         // Create the service request
    //         $serviceRequest = ServiceRequest::create([
    //             'c_by' => Auth::id(),
    //             'service_id' => $validatedData['service_id'],
    //             'service_type' => $validatedData['service_type'],
    //             'buildup_area' => $validatedData['addBuildup'],
    //             'budget' => $validatedData['addBudget'],
    //             'start_date' => $validatedData['addStartDate'],
    //             'location' => $validatedData['addLoc'],
    //             'phone_number' => $validatedData['addContact'],
    //             'description' => $validatedData['addDescp'],
    //         ]);

    //         return response()->json(['message' => 'Service request created successfully', 'data' => $serviceRequest], 201);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Failed to create service request', 'message' => $e->getMessage()], 500);
    //     }
    // }

    public function add_service_request(Request $req)
    {

        $validator = Validator::make($req->all(), [
            'title' => 'required',
            'service_type' => 'required|numeric',
            'buildup_area' => 'required|string',
            'budget' => 'required|string',
            'location' => 'required|numeric',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            if ($req->hasFile('image')) {
                $image = $req->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('service_images'), $imageName);
                $req->image = 'service_images/' . $imageName;
            } else {
                $req->image = null;
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Image upload failed', 'message' => $e->getMessage()], 500);
        }

        try {

            $ser =  Service::create([
                'created_by' => Auth::id(),
                'title' => $req->title,
                'service_type' => $req->service_type,
                'price_per_sq_ft' => $req->buildup_area,
                'location' => $req->location,
                'description' => $req->description,
                'image_or_catalogue' => $req->image,
                'wallet' => 0,
                'click' => 0,
                'approvalstatus' => 'pending',
                'status' => 'active',
            ]);

            return response()->json(['message' => 'Service added successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add service', 'message' => $e->getMessage()], 500);
        }
    }

    // function to get created services
    //Log::info('auth id is'.Auth::id()); 'serviceType:id,value', 'locationRelation:id,value'with('creator:id,name')
    public function get_created_services(Request $request)
{
    try {
        //$user_id=$request->user_id;
        $services = Service::with('serviceType', 'locationRelation')
        ->when(request()->has('user_id'), function ($query) {
            $query->where('created_by', request('user_id'))
                ->where('approvalstatus', 'approved');
        }, function ($query) {
            $query->where('created_by', Auth::id());
        })
        ->latest()
        ->get();

        // $services = Service::with('serviceType', 'locationRelation')
        // ->where('created_by', $user_id ?? Auth::id())
        // ->latest()
        // ->get();
        $ServiceData = [];

        foreach ($services as $service) {
            $ServiceData[] = [
                'service_id' => $service->id,
                'title' => $service->title,
                'service_type' => $service->serviceType->value ?? null,  
                'price' => $service->price_per_sq_ft,
                
                // 'legal_name' => $service->user->gst->business_legal ?? null,
                // 'service_cat' => $service->job_cat->value ?? null,
                'service_loc' => $service->locationRelation->value ?? null, 
                'service_image' =>  $service->image ?? null,
                'approval_staus' => $service->approvalstatus ?? null,
                'service_boost' => $service->highlighted ?? null,
            ];
        }

        return response()->json(['success'=>true,
            'data' => $ServiceData], 200);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to fetch services',
            'message' => $e->getMessage()
        ], 500);
    }
}

    // function to get service list api
    public function service_list_api(Request $req)
    {
        // $ser =  Service::where('approvalstatus', 'approved')->latest()->get();

        try {
            $ser = Cache::remember('service_list_api', 120, function () {
                return Service::with('locationRelation:id,value')
                    ->where('approvalstatus', 'approved')
                    ->latest()
                    ->take(200) // 🔥 LIMIT to 200 services
                    ->get()
                    ->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'title' => $service->title,
                            'service_type' => $service->serviceType->value ?? null,
                            'sq_ft' => $service->price_per_sq_ft ?? null,
                            'location' => $service->locationRelation->value ?? null,
                           
                            'created_at' => $service->created_at->toDateTimeString(),
                            'highlighted' => $service->highlighted,
                        ];
                    });
            });

            return response()->json(['data' => $ser], 200);
        } catch (\Throwable $e) {
            // Log the error or handle it
            Log::error('Failed to fetch service list: ' . $e->getMessage());

            // Optional: Return a fallback response or empty collection
            $ser = collect(); // or return response()->json([...], 500);
        }
    }

    // function to get service profile api
    public function service_profile_api(Request $req)
    {
        try {
            $service = Service::with([
                'creator:id,name,email,address',
                'serviceType:id,value',
                'locationRelation:id,value'
            ])
            ->withCount('reviews')
            ->withAvg('reviews', 'stars')
            ->find($req->service_id);
                //$reviews = ServiceReview::where('service_id', $req->service_id)->with('user')->get();
            $serviceCharge = Charge::where('category', 'service_highlight')->value('charge') * 1.18;
            $sub_images = json_decode($service->sub_images, true);

                // Convert image paths to full URLs
                $fullImagePaths = [];
                if (is_array($sub_images)) {
                    foreach ($sub_images as $img) {
                        $fullImagePaths[] = ($img); // Generates full URL like http://yourdomain.com/service_images/img.jpg
                    }
                }
                if (!empty($service->video)) {
                    $fullImagePaths[] = $service->video; // or asset($service->video)
                }
            $data = [
                'id' => $service->id,
                'title' => $service->title,
                'service_type' => $service->serviceType->value ?? null,
                'location' => $service->locationRelation->value ?? null,
                'price' => $service->price_per_sq_ft,
                // 'service_image' => asset('image/product-bricks.jpg'),
                'service_boost' => $service->highlighted ?? null,
                'staus' => $service->status ?? null,
                'description' => $service->description,
                'remarks' => $service->remark,
                'reviews_count' => $service->reviews_count ?? 0,
                'reviews_avg_stars' => $service->reviews_avg_stars ? round($service->reviews_avg_stars, 1) : null,
                'highlight_charge' => $serviceCharge,
                'approval_status' =>  $service->approvalstatus,
                'all_media' => $fullImagePaths,
               
            ];

            if (!$service) {
                return response()->json(['error' => 'Service not found or not approved'], 404);
            }

            return response()->json(['success'=>true,
                'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch service details', 'message' => $e->getMessage()], 500);
        }
    }

    // function to request a service
    public function request_service_api(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'service_type' => 'required',
            'buildup_area' => 'required|string',
            'budget' => 'required|string',
            'start_date' => 'required|date|after:today',
            'location' => 'required|string|max:255',
            'phone_number' => 'required|digits:10|regex:/^[6-9][0-9]{9}$/',
            'description' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            $serviceRequest = ServiceRequest::create([
                'c_by' => Auth::id(),
                'service_id' => $req->service_type,
                'buildup_area' => $req->buildup_area,
                'budget' => $req->budget,
                'start_date' => $req->start_date,
                'location' => $req->location,
                'phone_number' => $req->phone_number,
                'description' => $req->description,
            ]);

            return response()->json(['message' => 'Service request created successfully', 'data' => $serviceRequest], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create service request', 'message' => $e->getMessage()], 500);
        }
    }

    // function to get service request list api
    public function service_request_list_api(Request $req)
    {
        //log::info('Fetching service requests for user ID: ' . Auth::user());
        try {
            $serviceRequests = ServiceRequest::with('service:id,title,service_type', 'service.serviceType:id,value')
                ->where('c_by', Auth::id())
                ->latest()
                ->get()
                ->map(function ($data) {

                    $c_by = UserDetail::where('id' ,$data->c_by)->with('user_location')->first();

                    $review = ServiceReview::where('service_id', $data->service_id)
                        ->avg('stars');

                    return [
                        'id' => $data->service_id,
                        'service_title' => $data->service->title ?? null,
                        'service_image' => $data->image[0] ?? null,
                        'service_type' => $data->service->serviceType->value ?? null,
                        'buildup_area' => $data->buildup_area,
                        'budget' => $data->budget,
                        'start_date' => $data->start_date->format('Y-m-d'),
                        'location' => $data->user->user_location->value,
                        'phone_number' => $data->phone_number,
                        'description' => $data->description,
                        'created_at' => $data->created_at->toDateTimeString(),
                        'requested_by' => $c_by->user_name ?? null,
                        'review_avg' => $review ? round($review, 1) : null,

                    ];
                });
            return response()->json(['data' => $serviceRequests], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch service requests', 'message' => $e->getMessage()], 500);
        }
    }

    public function services_review_list(Request $req)
    {
            $validator = Validator::make($req->all(), [
                'service_id' => 'required',
                
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $reviews = ServiceReview::where('service_id', $req->service_id)
        ->with('user:id,name,profile_img')
        ->latest()
        ->get();

        $data = $reviews->map(function ($review) {
            return [
                'name' => $review->user->name ?? 'Anonymous',
                'stars' => (int) $review->stars,
                'review' => $review->review ?? null,
                'profile'=>$review->user->profile_img ?? null,
                'created_at' => $review->created_at->format('d M Y'),
            ];
        });

        return response()->json(['success'=>true,
            'data' => $data], 200);

            
    }
    
    public function services_request_list(Request $req)
   {
        $serviceRequests = ServiceRequest::with(['user:id,name,email', 'service:id,title,created_by'])
            ->whereHas('service', function ($query) {
                $query->where('created_by', Auth::id()); // Authenticated user's services
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Format the response data
        $data = $serviceRequests->map(function ($request) {
            return [
                'request_id' => $request->id,
                'user_name' => $request->user->name ?? 'Unknown',
                // 'user_email' => $request->user->email ?? null,
                'service_title' => $request->service->title ?? null,
                'service_location' => $request->location ?? null,
                'bulidup_area' => $request->buildup_area ?? null,
                'budget'=> $request->budget ?? null,
                'created_at' => $request->created_at->format('d-m-Y'),
                // 'status' => $request->status ?? null,
                // 'remarks' => $request->remarks ?? null,
                // 'created_at' => $request->created_at->format('d M Y H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }

    //service update method

    // Update Service
    public function service_update(Request $request, Aws $aws, $id = null)
    {
        //Log::info($request->all());
        try {
            // If service_id is passed in the request body, override the $id from URL
            if ($request->service_id) {
                $id = $request->service_id;
            }
    
            $service = Service::where('id', $id)
                ->where('created_by', Auth::id())
                ->firstOrFail();
    
            // Get current images from DB
            $images = json_decode($service->sub_images, true) ?? [];
            $cover_image = $service->image;
    
            // Validation rules
            $rules = [
                'title'             => 'required|string|max:255',
                'servicetype'       => 'required|string|max:255',
                'price'             => 'required|numeric|min:0',
                'location'          => 'required|string|max:255',
                'description'       => 'required|string',
                'service-image-1'   => 'nullable|file|max:5120',
                'service-image-2'   => 'nullable|file|max:5120',
                'service-image-3'   => 'nullable|file|max:5120',
                'service-image-4'   => 'nullable|file|max:5120',
            ];
    
            $request->validate($rules);
    
            // Normalize and handle delete_images[]
            $rawDeleteImages = $request->input('delete_images', []);

        // Delete by field name like 'service-image-2'$index = (int)$matches[1] - 1;
        foreach ($rawDeleteImages as $field) {
            if (preg_match('/service-image-(\d+)/', $field, $matches)) {
                $index = (int)$matches[1] - 1; // service-image-1 => index 0, service-image-2 => index 1//
                if (isset($images[$index])) {
                    unset($images[$index]);
                }
            }
        }

          // Reindex after deletion
            $images = array_values($images);
         
            //  Upload new images
            $uploaded = [];
            $folder = 'service_images';
            $s3Keys = [];
    
            foreach (['service-image-1', 'service-image-2', 'service-image-3', 'service-image-4'] as $index => $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $uploaded = $aws->common_upload_to_s3([$file], $folder);
                    $uploadedUrl = is_array($uploaded) ? $uploaded[0] : $uploaded;
    
                    // If it's the first image, treat it as cover image
                    if ($field === 'service-image-1') {
                        $cover_image = $uploadedUrl;
                    
                        // If sub_images has at least one image, remove the first one (old cover image)
                        if (!empty($images)) {
                            array_shift($images);
                        }
                    
                        // Insert the new cover image at the beginning
                        array_unshift($images, $cover_image);
                    }
                     else {
                        $images[] = $uploadedUrl;
                    }
                }
            }
    
            // Prepare final update data
            $data = [
                'title'         => $request->title,
                'service_type'  => $request->servicetype,
                'price_per_sq_ft' => $request->price,
                'location'      => $request->location,
                'description'   => $request->description,
                'image'         => $cover_image,
                'sub_images'    => json_encode($images),
                'approvalstatus' => 'pending',
            ];
    
            $service->update($data);
    
            return response()->json([
                'success' => true,
                'message' => 'Service  updated successfully',
            ], 200);
    
        } catch (\Exception $er) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error'   => $er->getMessage(),
            ], 500);
        }
    }
    
    //service status inactive
    public function services_status_update(Request $req)
    {
            $validator = Validator::make($req->all(), [
                'service_id' => 'required',
                
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $service = Service::where('id', $req->service_id)
            ->first();

            if (!$service) {
                return response()->json(['error' => 'Service not found'], 404);
            }
        
            if ($service->status === "active") {
               if($service->highlighted==1){
                Posts::where('category','service')->where('category_id',$service->id)->update(['status' => 'inactive']);
               }
               $service->status = 'inactive';
               $service->save();

            return response()->json([
                'success' => true,
                'data' => 'Status set to inactive successfully',
                'status'=>'inactive'
            ], 200);
        }
        
            if ($service->status === "inactive") {
                if($service->highlighted==1){
                    Posts::where('category','service')->where('category_id',$service->id)->update(['status' => 'active']);
                }  
                $service->status = 'active';
                $service->save();

            return response()->json([
                'success' => true,
                'data' => 'Status set to active successfully',
                'status'=>'active'
            ], 200);
        }


            
    }


    public function request_service_details(Request $req)
    {
        Log::info($req->all());
        try {
            $serviceRequest = ServiceRequest::with([
                'user:id,name,email,number',
                'service.serviceType',
                'locationRelation:id,id,value' 
            ])
            ->where('id', $req->request_id)
            ->first();
        
            if (!$serviceRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service request not found.',
                ], 404);
            }
    
            // Format the response data directly
            $data = [
                'user_name'        => $serviceRequest->user->name ?? 'Unknown',
                // 'user_email'    => $serviceRequest->user->email ?? null,
                'service_title'    => $serviceRequest->service->title ?? null,
                'service_type'    => $serviceRequest->service->serviceType->value ?? null,
                'service_location' => $serviceRequest->service->locationRelation->value ?? null,
                'bulidup_area'     => round($serviceRequest->buildup_area) ?? null,
                'budget'           => round($serviceRequest->budget) ?? null,
                'start_date'       => $serviceRequest->start_date?->format('d-m-Y'),
                'user_contact'    => $serviceRequest->phone_number ?? null,
                'description'    => $serviceRequest->description ?? null,
                'posted_date'       => $serviceRequest->created_at?->format('d-m-Y'),
                // 'status'        => $serviceRequest->status ?? null,
                // 'remarks'       => $serviceRequest->remarks ?? null,
            ];
    
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch service request details',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
}
