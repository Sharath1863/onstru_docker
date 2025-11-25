<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Jobs\Video_process;
use App\Models\Charge;
use App\Models\Franchise;
use App\Models\Notification;
use App\Models\Posts;
use App\Models\UserDetail;
use App\Services\Aws;
use App\Services\OtpService;
use App\Services\Vision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Register_api extends Controller
{
    // function for update popup

    public function update_popup(Request $request)
    {
        return response()->json(['status' => 'success', 'version' => '1.0.1']);
    }

    public function contact_exist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bio_contact' => 'required|string|regex:/^[0-9]{10}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = UserDetail::where('number', $request->bio_contact)->first();

        return response()->json([
            'status' => 'success',
            'message' => $user ? 'Contact Exists' : 'Contact does not exist',
            'data' => $user ? 1 : 0,

        ], 200);
    }

    public function register(Request $request, OtpService $otpService)
    {
        Log::info($request->all());
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:255|unique:user_detail,user_name',
            'type' => 'required|string|in:Business,Professional,Consumer',
            'as_a' => [
                'nullable',
                'string',
                'required_if:type,Business',
                'required_if:type,Professional',
                'required_unless:type,Consumer',
                Rule::when($request->type === 'Business', Rule::in(['Vendor', 'Contractor', 'Consultant'])),
                Rule::when($request->type === 'Professional', Rule::in(['Technical', 'Non-Technical'])),
            ],
            // 'as_a' => 'nullable|string|required_if:type,Business:Vendor,Contractor,Consultant|required_if:type,Professional:Technical,Non-Technical|required_unless:type,Consumer',
            'type_of' => 'nullable|required_if:type,Business|required_if:type,Professional',
            'bio_name' => 'required|string|max:255',
            'bio_contact' => 'required|string|regex:/^[0-9]{10}$/||unique:user_detail,number',
            'bio_gender' => 'required|string|in:Male,Female,Others',
            'password' => 'required|string',
            'location' => 'nullable',
            'ref_id' => 'nullable|string|exists:franchise,code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }
        $existing = Franchise::where('mobile', '=', $request->bio_contact)->first();
        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'This contact number is already registered in Franchise',
            ], 409);
        }
        // $user = UserDetail::where('number', $request->bio_contact)->first();

        //     if ($user) {
        //         return response()->json([
        //             'status' => 'error',
        //             'message' => 'Contact number already exists',
        //         ], 409);
        //     }

        $otp = rand(1000, 9999);

        $slug = Str::uuid()->toString();

        $user = UserDetail::create([
            'user_name' => $request->user_name,
            'you_are' => $request->type,
            'as_a' => $request->as_a,
            'type_of' => is_array($request->type_of)
            ? implode(',', $request->type_of)
            : $request->type_of,
            'name' => $request->bio_name,
            'number' => $request->bio_contact,
            'gender' => $request->bio_gender,
            'password' => $request->password,
            'location' => (int) $request->location,
            'otp' => $otp,
            'hash_password' => bcrypt($request->password),
            'slug' => $slug,
            'ref_id' => $request->ref_id,
        ]);

        $otpService->sendOtp($user->number, $user->otp);

        $data = [
            'user_id' => $user->id,
            'otp' => $otp, // Generate a random OTP
        ];

        // Save OTP to the user  // SMS API Details
        // $authKey = "3636736465636b35323233";
        // $senderId = "DRDECK";
        // $route = "2"; // Working for now
        // $country = "91";
        // $dltTeId = "1707175066512828187";
        // $message = urlencode("Dear user, your DriversDeck registration OTP is $otp. Please do not share this with anyone. - DRDECK");

        // $url = "http://promo.smso2.com/api/sendhttp.php?authkey=$authKey&mobiles=$phone&message=$message&sender=$senderId&route=2&country=91&DLT_TE_ID=$dltTeId";

        // // Send SMS
        // try {
        //     $response = file_get_contents($url);
        //     Log::info("SMS sent to $phone. OTP: $otp. Response: $response");
        // } catch (\Exception $e) {
        //     Log::error("SMS sending failed: " . $e->getMessage());
        // }

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => $data,
        ], 201);
    }

    // Verify OTP
    public function otp_verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|integer',
            'user_id' => 'nullable|exists:user_detail,id',
            'fcm_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = UserDetail::find($request->user_id);
        if ($user && $user->otp == $request->otp) {
            $user->otp_status = 'yes';
            $user->mob_token = $request->fcm_token;
            $user->save();

            Auth::login($user);

            return response()->json([
                'status' => 'success',
                'message' => 'OTP verified successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid OTP',
        ], 400);
    }

    // resent otp
    public function resend_otp(Request $request, OtpService $otpService)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:user_detail,id',
            'number' => 'nullable|exists:user_detail,number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }
        if ($request->user_id) {
            $user = UserDetail::find($request->user_id);
        }
        if ($request->number) {
            $user = UserDetail::where('number', $request->number)->first();
        }
        if ($user) {
            $otp = rand(1000, 9999);
            $user->otp = $otp; // Generate a new OTP
            $otpService->sendOtp($user->number, $otp);
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'OTP resent successfully',
                'otp' => $otp, // Return the new OTP
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'User not found',
        ], 404);
    }

    // login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|string|regex:/^[0-9]{10}$/|exists:user_detail,number',
            'password' => 'required|string',
            'fcm_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('number', 'password');

        // if (Auth::attempt($credentials)) {
        $user = UserDetail::where('number', $request->number)->with('gst')->first();

        try {

            // if ($user && Hash::check($request->password, $user->hash_password)) {
            if ($user) {
                // $user = Auth::user();
                // Optionally, you can generate a token for the user
                $user->mob_token = $request->fcm_token;
                $user->save();
                $token = $user->createToken('token')->plainTextToken;
                // Log::info($token);

                $user->token = $token; // Add token to user data
                $user->gst_verified = ($user->gst && $user->gst->gst_verify === 'yes') ? 'yes' : 'no';

                // Auth::login($user);

                // Return user data and token
                return response()->json(['status' => 'success', 'user' => $user], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials',
                ], 401);
            }
        } catch (\exception $e) {
            log::error('Error', ['error' => $e->getMessage()]);
        }

    }

    // logout
    public function logout(Request $request)
    {
        // dd('token not found');

        $user = $request->user();

        if ($user) {
            // clear custom tokens
            if ($request->header('Authorization')) {
                $user->mob_token = null;
            } else {
                $user->web_token = null;
            }
            // $user->save();

            $token = $user->currentAccessToken();

            // dd($token->id);

            $token_data = DB::table('personal_access_tokens')
                ->where('id', $token->id)
                ->exists();
            if ($token_data) {
                // Revoke the token
                // dd('token found');
                $token->delete();
                $user->save();
            } else {
                // dd('token not found');

                // If token already deleted or invalid
                return response()->json([
                    'status' => 'error',
                    'message' => 'Already logged out or token invalid',
                ], 401);
            }

            // delete Sanctum token
            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No user found',
        ], 401);

    }

    // create post
    public function create_post1(Request $request, Vision $visionService)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Handle image upload if provided
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }
        // If an image is provided, perform Safe Search detection
        $safeSearch = [];
        if ($imagePath) {
            $fullImagePath = storage_path('app/public/'.$imagePath);
            $safeSearch = $visionService->detectSafeSearch($fullImagePath);
        }

        // Create the post (you would typically save this to a database)
        $post = [
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imagePath,
            'safe_search' => $safeSearch,
            'created_at' => now(),
        ];

        // Return the created post data
        return response()->json([
            'status' => 'success',
            'message' => 'Post created successfully',
            'data' => $post,
        ], 201);
    }

    // vidoe upload

    public function create_video(Request $request, Vision $visionService)
    {
        // $request->validate([
        //     'video' => 'required|mimetypes:video/mp4,video/quicktime|max:51200', // ~50MB max
        // ]);

        // // Step 1: Store video temporarily in Laravel storage
        // $localPath = $request->file('video')->store('videos', 'public');

        // $fullLocalPath = storage_path('app/public/' . $localPath);

        // // Step 2: Initialize Google Storage Client
        // $storage = new StorageClient([
        //     'projectId' => env('GOOGLE_CLOUD_PROJECT_ID'),
        //     'keyFilePath' => storage_path('app/onstru-video-app.json'),
        // ]);

        // $bucket = $storage->bucket(env('GOOGLE_CLOUD_STORAGE_BUCKET'));

        // // Step 3: Upload file to GCS
        // $object = $bucket->upload(
        //     fopen($fullLocalPath, 'r'),
        //     [
        //         'name' => 'videos/' . basename($fullLocalPath) // optional folder in bucket
        //     ]
        // );

        // // Step 4: Get the GCS URI
        // $gcsUri = "gs://" . env('GOOGLE_CLOUD_STORAGE_BUCKET') . "/" . $object->name();

        // // Optional: delete local temp file
        // Storage::delete($localPath);

        $gcsUri = 'gs://onstru_bucket01/videos/NGDeCNXxZYVWuZWVwSAl5rtN8cbQlRJm5DivoYYg.mp4';
        // $path = $request->file('video')->store('videos', 'gcs'); // 'videos' folder in bucket

        // $gcsUri = "gs://" . env('GOOGLE_CLOUD_STORAGE_BUCKET') . "/" . basename($path)
        $safeSearch = $visionService->detect_video($gcsUri);

        return response()->json([
            'status' => 'success',
            'gcs_uri' => $gcsUri,
            'safe_search' => $safeSearch,
        ]);
    }

    // function to test aws rekognition

    public function create_post(Request $request, Aws $aws)
    {
        // dd($request->allFiles());

        $validator = Validator::make($request->all(), [
            'images' => 'required|file|mimes:jpg,jpeg,png,mp4,mov,avi,mkv',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get the uploaded file
        $file = $request->file('images');

        $ext = strtolower($file->getClientOriginalExtension());

        // Upload to
        $s3Key = $aws->upload_to_s3($file); // returns 'images/xxxx.jpg' or 'videos/xxxx.mp4'

        // Decide search method based on extension
        $imageExtensions = ['jpg', 'jpeg', 'png'];
        $videoExtensions = ['mp4', 'mov', 'avi', 'mkv'];

        // $sensitiveImages = array_filter($moderationResults, function ($result) {
        //     return $result['level'] === 'sensitive';
        // });

        // $hasSensitive = count($sensitiveImages) > 0 ? 1 : 0;

        if (in_array($ext, $imageExtensions)) {
            $result = $aws->image_search($s3Key);
            $type = 'image';
        } elseif (in_array($ext, $videoExtensions)) {
            $result = $aws->video_search($s3Key);
            $arr = [
                'post_id' => $post_id ?? 1,
                'job_id' => $result,

            ];
            if (! empty($result)) {
                Video_process::dispatch($arr)->onQueue('video_check');
            }
            // $type = 'video';
        } else {
            $type = 'unknown';

            return response()->json(['error' => 'Unsupported file type'], 422);
        }

        // Log the result
        // Log::info($result);

        return response()->json([
            'status' => 'success',
            'data' => $result,
            's3_key' => $s3Key,
        ]);
    }

    // jobid return respose time

    public function get_video_details(Request $request, Aws $aws)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // $result = $aws->get_video_details($request->job_id);

        $arr = [
            // 'post_id' => $post_id ?? 1,
            'job_id' => $request->job_id,
            'post_id' => 1, // Replace with actual post ID if available

        ];

        // if (!empty($result)) {
        Video_process::dispatch($arr)->onQueue('video_check');
        // }

        return response()->json([
            'status' => 'success',
            'data' => $arr,
        ]);
    }

    // public function create_post(Request $request, Aws $aws)
    // {
    //     // $imagePath = 'posts/your_image.jpg'; // Example image path in S3
    //     // $result = $aws->image_search($imagePath);

    //     $request->validate([
    //         'images' => 'required|file|mimes:jpg,jpeg,png',
    //     ]);

    //     // 2. Get the uploaded file
    //     $file = $request->file('images');

    //     // 3. Build a path inside S3 bucket
    //     $img = uniqid() . '.' . $file->getClientOriginalExtension();

    //     // 2. Generate a unique S3 key
    //     $ext = strtolower($file->getClientOriginalExtension());
    //     $key = uniqid() . '.' . $ext;

    //     $path = 'images/' . $img;

    //     // 3. Upload to S3
    //     $s3Key = $aws->upload_to_s3($file); // returns 'images/xxxx.jpg' or 'videos/xxxx.mp4'

    //     // 4. Decide search method based on extension
    //     $imageExtensions = ['jpg', 'jpeg', 'png'];
    //     $videoExtensions = ['mp4', 'mov', 'avi', 'mkv'];

    //     if (in_array($ext, $imageExtensions)) {
    //         $result = $aws->image_search($s3Key);
    //     } elseif (in_array($ext, $videoExtensions)) {
    //         $result = $aws->video_search($s3Key);
    //     } else {
    //         return response()->json(['error' => 'Unsupported file type'], 422);
    //     }

    //     $result = $aws->image_search($path);

    //     log::info($result);

    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $result,
    //     ]);z
    // }

    // dropdown list for type of based on as_a
    public function dropdown_list(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'as_a' => 'nullable|string|in:Vendor,Contractor,Consultant,Technical,Non-Technical',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->as_a === 'Vendor') {
            // Step 3: Fetch data from dropdownlist table where dropdown_id = 6
            $data = DB::table('dropdown_lists')
                ->where('dropdown_id', 6)
                ->select('id', 'value')
                ->get();
        } elseif ($request->as_a === 'Contractor') {

            $data = DB::table('dropdown_lists')
                ->where('dropdown_id', 7)
                ->select('id', 'value')
                ->get();
        } elseif ($request->as_a === 'Consultant') {

            $data = DB::table('dropdown_lists')
                ->where('dropdown_id', 8)
                ->select('id', 'value')
                ->get();
        } elseif ($request->as_a === 'Technical') {

            $data = DB::table('dropdown_lists')
                ->where('dropdown_id', 9)
                ->select('id', 'value')
                ->get();
        } elseif ($request->as_a === 'Non-Technical') {

            $data = DB::table('dropdown_lists')
                ->where('dropdown_id', 9)
                ->select('id', 'value')
                ->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);

        return response()->json([
            'status' => 'info',
            'message' => 'No data fetched. "as_a" type.',
        ]);
    }

    // location list for dropdown
    public function location_list(Request $request)
    {

        $data = DB::table('dropdown_lists')
            ->where('dropdown_id', 1)
            ->select('id', 'value')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    // search user for based on userdetail table->username
    public function searchUser(Request $request)
    {
        $input = $request->input('user_name');

        // Minimum 3 characters required
        if (strlen($input) < 4) {
            return response()->json([
                'success' => false,
                'message' => 'Enter at least 3 characters',
                // 'data' => []
            ], 422);
        }

        $matches = \App\Models\UserDetail::where('user_name', 'like', "%{$input}%")
            ->pluck('user_name');

        if ($matches->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No matching usernames found.',
                // 'data' => []
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Matching usernames found.',
            // 'data' => $matches
        ]);
    }

    // search user number for based on userdetail table->number
    public function searchMobile(Request $request)
    {
        $input = $request->input('mobile');

        // Check minimum 3 digits
        if (strlen($input) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'Enter at least 3 digits of mobile number.',
            ], 422);
        }

        // Search matching mobile numbers using LIKE
        $matches = \App\Models\UserDetail::where('number', 'like', "%{$input}%")
            ->pluck('number');

        if ($matches->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No matching mobile numbers found.',
                // 'data' => []
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Matching mobile numbers found.',
            // 'data' => $matches
        ]);
    }

    // otp status and fcm token update in userdetail table->opt_staus and fcm_token
    public function otp_status_update(Request $request, OtpService $otpService)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user_detail,id',
            'otp_status' => 'required',
            'fcm_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }
        $user = UserDetail::find($request->user_id);
        if ($user) {

            $user->otp_status = $request->otp_status;
            $user->mob_token = $request->fcm_token;
            $token = $user->createToken('token')->plainTextToken;
            $user->save();
            // $user->token = $token;
            // Log::info("token Status Before Check: " . $token);

            return response()->json([
                'status' => 'success',
                'message' => 'OTP status update successfully',
                'data' => [

                    'name' => $user->name,
                    'user_name' => $user->user_name,
                    'mobile' => $user->number,
                    'as_a' => $user->as_a,
                    'gender' => $user->gender,
                    // 'location' => $user->location,
                    'token' => $token,
                ],
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'User not found',
        ], 404);
    }

    // fetch  category in dropdown list table based on dropdown table id
    public function jobs_category_list(Request $request)
    {

        try {
            $data = DB::table('dropdown_lists')
                ->where('dropdown_id', 4)
                ->select('id', 'value')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch service details', 'message' => $e->getMessage()], 500);
        }
    }

    // fetch service type in dropdown list table based on dropdown table id
    public function services_type_list(Request $request)
    {

        $data = DB::table('dropdown_lists')
            ->where('dropdown_id', 5)
            ->select('id', 'value')
            ->get();

        $service_charge = Charge::where('category', 'service_list')->latest()->value('charge');
        $total_amount = round($service_charge * 1.18, 1);

        return response()->json([
            'status' => 'success',
            'service_charge' => $total_amount,
            'data' => $data,

        ]);

    }

    // check balance from user table
    public function check_balance(Request $request)
    {

        //  $validator = Validator::make($request->all(), [
        //      'user_id' => 'required|string|max:255|exists:user_detail,id'
        //  ]);

        //  if ($validator->fails()) {
        //      return response()->json([
        //          'status' => 'error',
        //          'errors' => $validator->errors()
        //      ], 422);
        //  }
        //  $id=Auth::id();
        // dd($id);
        // $wallet_balance = UserDetail::find(Auth::id());
        // dd($wallet_balance);

        return response()->json([
            'success' => true,
            'wallet_balance' => floatval(Auth::user()->balance ?? 0),
            // 'wallet_balance' => $wallet_balance->balance ?? 0,
        ], 200);

    }

    // forgot passowrd
    public function forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact' => 'required',
        ], [
            'contact.required' => 'Mobile number is required.',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => implode(', ', $validator->errors()->all()),
            ], 422);
        }

        // Get the latest customer by mobile
        $user = UserDetail::where('number', $request->contact)->latest()->first();

        if (! $user) {
            return response()->json([
                'status' => true,
                'message' => 'user not found.',
            ], 200);
        }

        $mobile = $user->number;
        $v_code = rand(1000, 9999);
        $authKey = '373776616e616e38313500';
        $senderId = 'ONSTRU';
        $route = '2';
        $country = '91';
        $dltTeId = '1707172965885916248';
        $message = urlencode("Welcome to Onstru! Your verification code is: $v_code. Please enter this code to complete your registration. - ONSTRU");
        $url = "http://promo.smso2.com/api/sendhttp.php?authkey=$authKey&mobiles=$mobile&message=$message&sender=$senderId&route=$route&country=$country&DLT_TE_ID=$dltTeId";
        Log::info('SMS URL: '.$url);
        $response = file_get_contents($url);

        DB::table('user_detail')
            ->where('number', $mobile)
            ->update(['otp' => $v_code]);

        return response()->json([
            'success' => true,
            'otp' => $v_code,

        ]);

    }

    // forgot password
    public function forgot_pswd_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:6',
            'contact' => 'required|exists:user_detail,number',
        ]);
        $contact = $request->contact;
        $new_password = $request->new_password;

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = UserDetail::where('number', $contact)->first();
        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'user not found.',
            ], 404);
        }
        if ($user) {
            // $user->otp_status = 'yes';
            $user->password = $new_password;
            $user->hash_password = Hash::make($new_password);
            $user->save();

            // Auth::login($user);
            return response()->json([
                'status' => true,
                'message' => 'new password updated successfully',
            ], 200);
        }

    }

    // change password
    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'change_password' => 'required|min:6',

        ]);
        $id = Auth::id();
        $new_password = $request->change_password;

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = UserDetail::where('id', $id)->first();
        if (! $user) {
            return response()->json([
                'status' => true,
                'message' => 'user not found.',
            ], 200);
        }
        if ($user) {

            $user->password = $new_password;
            $user->hash_password = Hash::make($new_password);
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'change password updated successfully',
            ], 200);
        }

    }

    // function for notification list

    public function notification_list(Request $request)
    {
        // $notifications = Notification::with(['sender:id,name,profile_img', 'order', 'post:id,file', 'receiver:id,name,profile_img', 'job:id,title', 'leads:id,title'])
        //     ->where('c_by', Auth::id())
        //     // ->where('category', 'user')
        //     ->orderBy('created_at', 'desc')
        //     ->get();

        // $notifications->map(function ($notification) {
        //     $notification->time_ago = $notification->created_at->diffForHumans();

        //     // Add first file from post
        //     if ($notification->post && $notification->post->file) {
        //         $notification->post->first_file = $notification->post->file[0] ?? null;
        //     } else {
        //         $notification->post->first_file = null;
        //     }

        //     unset($notification->post->file);

        //     return $notification;
        // });

        $notifications = Notification::where('reciever', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        Notification::where('reciever', Auth::id())->update(['status' => 'inactive', 'seen' => 1]);

        // Preload common relations
        $notifications->load([
            'sender:id,name,user_name,profile_img,badge',
            'receiver:id,name,user_name,profile_img,badge',
        ]);

        // Transform using map()
        $notifications = $notifications->map(function ($notification) {
            // Dynamically load based on category
            switch ($notification->category) {
                case 'post':
                    $notification->loadMissing(['post:id,file,file_type']);
                    if ($notification->post && is_array($notification->post->file)) {
                        $notification->post->first_file = $notification->post->file[0] ?? null;
                    }

                    unset($notification->post->file);
                    break;

                case 'job':
                    $notification->loadMissing(['job:id,title']);
                    break;

                case 'leads':
                    $notification->loadMissing(['leads:id,title']);
                    break;
                case 'service':
                    $notification->loadMissing(['service:id,title']);
                    break;
                case 'order':
                    $notification->loadMissing(['order:id,order_id']);
                    break;
                case 'comment':
                    $notification->loadMissing(['comment:id,post_id,comment', 'comment.post:id,file,file_type']);
                    $notification->comment_data = $notification->title.' : "'.$notification->comment->comment.'"';
                    // unset($notification->comment);
                    // $post_data = Posts::where('id', $notification->comment->post_id)->select('id', 'file')->first();
                    // $notification->post = $notification->comment->post;

                    if ($notification->comment->post && is_array($notification->comment->post->file)) {
                        $notification->comment->post->first_file = $notification->comment->post->file[0] ?? null;

                    } else {
                        $notification->comment->first_file = null;
                    }
                    unset($notification->post, $notification->comment->post->file);
                    break;
            }

            // Add time ago
            $notification->time_ago = $notification->created_at->diffForHumans();

            return $notification;
        });

        // $notifications = $notifications->map(function ($notification) {
        //     $data = [
        //         'id' => $notification->id,
        //         'type' => $notification->category,
        //         'message' => $notification->message ?? null,
        //         'time_ago' => $notification->created_at->diffForHumans(),
        //         'related' => null, // will store post/job/leads/order data
        //     ];

        //     switch ($notification->category) {
        //         case 'post':
        //             $notification->loadMissing(['post:id,file']);
        //             if ($notification->post) {
        //                 $related = [
        //                     'id' => $notification->post->id,
        //                     'first_file' => is_array($notification->post->file) ? $notification->post->file[0] : null,
        //                 ];
        //             } else {
        //                 $related = null;
        //             }
        //             $data['related'] = $related;
        //             break;

        //         case 'job':
        //             $notification->loadMissing(['job:id,title']);
        //             $data['related'] = $notification->job ? [
        //                 'id' => $notification->job->id,
        //                 'title' => $notification->job->title,
        //             ] : null;
        //             break;

        //         case 'leads':
        //             $notification->loadMissing(['leads:id,title']);
        //             $data['related'] = $notification->leads ? [
        //                 'id' => $notification->leads->id,
        //                 'title' => $notification->leads->title,
        //             ] : null;
        //             break;

        //         case 'order':
        //             $notification->loadMissing(['order:id,order_id']);
        //             $data['related'] = $notification->order ? [
        //                 'id' => $notification->order->id,
        //                 'order_id' => $notification->order->order_id,
        //             ] : null;
        //             break;
        //     }

        //     return $data;
        // });

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    // get old password
    public function get_old_password(Request $request)
    {

        $id = Auth::id();
        // dd($id);

        $user = UserDetail::where('id', $id)->first();
        if (! $user) {
            return response()->json([
                'status' => true,
                'message' => 'user not found.',
            ], 200);
        }
        if ($user) {
            return response()->json([
                'status' => true,
                'message' => 'get old password successfully',
                'old_password' => $user->password,
            ], 200);
        }

    }
}
