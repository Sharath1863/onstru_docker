<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\OtpService;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Franchise;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\FranchiseWallet;
use App\Models\Orders;
use App\Models\Settlement;
use Illuminate\Support\Facades\Log;

class Franchise_api extends Controller
{
    //Franchise Register
    public function franchise_register(Request $request, OtpService $otpService)
    {
        Log::info($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'contact' => 'required|string|regex:/^[0-9]{10}$/||unique:franchise,mobile',
            'mail_id' => 'required|string',
            'type_of' => 'required|string|in:Franchise',
            'password' => 'required|string',
           
          
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

       $existing = UserDetail::where('number','=', $request->contact)->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'This contact number is already registered in user_detail',
            ], 409);
        }

       $lastOrder = DB::table('franchise')->orderBy('id', 'desc')->first();

       if ($lastOrder && preg_match('/ONSTRUFRN(\d{3})/', $lastOrder->code, $matches)) {
            // Extract numeric part and increment by 1
            $number = intval($matches[1]) + 1;
        } else {
            // If no previous order or code format doesn't match, start from 1
            $number = 1;
        }

      // Format the new code with prefix and zero-padded number
       $newOrderId = 'ONSTRUFRN' . str_pad($number, 3, '0', STR_PAD_LEFT);

        $otp = rand(1000, 9999);

        $user =  Franchise::create([
            'name' => $request->name,
            'mobile' => $request->contact,
            'mail_id' => $request->mail_id,
            'type_of' => $request->type_of,
            'password' => $request->password,
            'otp' => $otp,
            'code'=>$newOrderId,
            'hash_password' => bcrypt($request->password),
            
        ]);

        $otpService->sendOtp($user->mobile, $user->otp);

        $data = [
            'user_id' => $user->id,
            'otp' => $otp, // Generate a random OTP
        ];

       

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => $data,
        ], 200);
    }


     // Verify OTP
     public function franchise_otp_verify(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'otp' => 'required|integer',
             'user_id' => 'nullable|exists:franchise,id',
            //  'fcm_token' => 'required',
         ]);
 
         if ($validator->fails()) {
             return response()->json([
                 'status' => 'error',
                 'errors' => $validator->errors(),
             ], 422);
         }
 
         $user = Franchise::find($request->user_id);
         if ($user && $user->otp == $request->otp) {
             $user->otp_status = 'yes';
            //  $user->mob_token = $request->fcm_token;
             $user->save();
 
             Auth::login($user);
 
             return response()->json([
                'success' => true,
                 'message' => 'OTP verified successfully',
             ], 200);
         }
 
         return response()->json([
             'status' => 'error',
             'message' => 'Invalid OTP',
         ], 400);
     }



    //login
     public function franchise_login(Request $request, OtpService $otpService)
     {
         $validator = Validator::make($request->all(), [
             'contact' => 'required|string|regex:/^[0-9]{10}$/|exists:franchise,mobile',
             'password' => 'required|string',
            // 'fcm_token' => 'required',
         ]);
 
         if ($validator->fails()) {
             return response()->json([
                 'status' => 'error',
                 'errors' => $validator->errors(),
             ], 422);
         }
 
         $credentials = $request->only('contact', 'password');
 
         // if (Auth::attempt($credentials)) {
         $user = Franchise::where('mobile', $request->contact)->first();

         if($user->otp_status=="no"){

            $otp = rand(1000, 9999);
            $user->otp = $otp; // Generate a new OTP
            $otpService->sendOtp($user->mobile, $otp);
            $user->save();
            return response()->json([
                'success' => true,
                'user' => $user,
                'user_id'=>$user->id
            ], 200);

         }
 
         if ($user && Hash::check($request->password, $user->hash_password)) {
             $user->save();
             $token = $user->createToken('token')->plainTextToken;
             $user->token = $token; // Add token to user data
             $payment=FranchiseWallet::where('user_id','=',$user->id)->where('type','=','all')->first();
             // Return user data and token
             return response()->json([ 'success' => true, 'user' => $user,'payment'=>$payment], 200);
         }
 
         return response()->json([
             'status' => 'error',
             'message' => 'Invalid credentials',
         ], 401);
     }


      // logout
    public function franchise_logout(Request $request)
    {
        // dd('token not found');

        //$user = $request->user(); dd($user);
        $user = Auth::guard('franchise_api')->user();
       
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

    public function franchise_home(Request $request)
    {
        $id = Auth::guard('franchise_api')->id();
        //dd($id);

    if (!$id) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    // $id = $franchise->id;
    //dd($franchise);
        // $validator = Validator::make($request->all(), [
        //     'code' => 'required|exists:franchise,code'
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 'error',
        //         'errors' => $validator->errors(),
        //     ], 422);
        // }
        
        
        $user = Franchise::where('id','=',$id)->first();
        //contractor
        $contractor_total=UserDetail::where('as_a','=','Contractor')->where('ref_id','=',$user->code)->count();
        $today_contractor_count = UserDetail::where('as_a', 'Contractor')
                        ->where('ref_id', $user->code)
                        ->whereDate('created_at', Carbon::today()) // only today’s date
                        ->count();
        //vendor
        $vendor_total=UserDetail::where('as_a','=','Vendor')->where('ref_id','=',$user->code)->count();
        $today_vendor_count = UserDetail::where('as_a', 'Vendor')
                            ->where('ref_id', $user->code)
                            ->whereDate('created_at', Carbon::today()) // only today’s date
                            ->count();
        //consultant
        $consultant_total=UserDetail::where('as_a','=','Consultant')->where('ref_id', '=', $user->code)->count();
        $today_consultant_count = UserDetail::where('as_a', 'Consultant')
                        ->where('ref_id', $user->code)
                        ->whereDate('created_at', Carbon::today()) // only today’s date
                        ->count();
        //technical
        $technical_total=UserDetail::where('as_a','=','Technical')->where('ref_id', '=', $user->code)->count();
        $today_technical_count = UserDetail::where('as_a', 'Technical')
                                ->where('ref_id', $user->code)
                                ->whereDate('created_at', Carbon::today()) // only today’s date
                                ->count();
        //nontechnical
        $nontechnical_total=UserDetail::where('as_a','=','Non-Technical')->where('ref_id', '=', $user->code)->count();
        $today_nontechnical_count = UserDetail::where('as_a', 'Non-Technical')
                                ->where('ref_id', $user->code)
                                ->whereDate('created_at', Carbon::today()) // only today’s date
                                ->count();

        //consumer
        $consumer_total=UserDetail::where('you_are','=','Consumer')->where('ref_id', '=', $user->code)->count();
        $today_consumer_count = UserDetail::where('you_are', 'Consumer')
                                ->where('ref_id', $user->code)
                                ->whereDate('created_at', Carbon::today()) // only today’s date
                                ->count();



        //commision amount

        $records = Settlement::where('franchise_id', $user->id)
        ->get();  // sum of the `amount` column :contentReference[oaicite:1]{index=1}

        $totalSales = 0;
        $totalLeads = 0;
        $totalAmount = 0;
    

      
      foreach ($records as $record) {
        if (is_array($record->amount)) {
            $totalSales  += (float) ($record->amount['sales'] ?? 0);
            $totalLeads  += (float) ($record->amount['leads'] ?? 0);
            $totalAmount += (float) ($record->amount['total'] ?? 0);
        } else {
            // if 'amount' is stored as JSON string
            $amount = json_decode($record->amount, true);
            $totalSales  += (float) ($amount['sales'] ?? 0);
            $totalLeads  += (float) ($amount['leads'] ?? 0);
            $totalAmount += (float) ($amount['total'] ?? 0);
        }
    }
                                
        return response()->json([
            'success' => true,
            'contractor_total'=> $contractor_total,
            'today_contractor_count'=> $today_contractor_count,
            'out_of_contractor'=>$user->Contractor,

            'vendor_total'=> $vendor_total,
            'today_vendor_count'=> $today_vendor_count,
            'out_of_vendor'=>$user->Vendor,

            'consultant_total'=> $consultant_total,
            'today_consultant_count'=> $today_consultant_count,
            'out_of_consultant'=>$user->Consultant,

            
            'technical_total'=> $technical_total,
            'today_technical_count'=> $today_technical_count,
            'out_of_technical'=>$user->Technical,
            
            'nontechnical_total'=> $nontechnical_total,
            'today_nontechnical_count'=> $today_nontechnical_count,
            'out_of_non_technical'=>$user->{'Non-Technical'},
             
            'consumer_total'=> $consumer_total,
            'today_consumer_count'=> $today_consumer_count,
            'out_of_consumer'=>$user->Consumer,

            'sales_amount' => $totalSales,
            'leads_amount' => $totalLeads,
            'total_amount' => $totalAmount,

            
        ], 200);
    }


    public function franchise_transaction(Request $request)
    {
       // dd('hello');
        // $id = Auth::guard('franchise_api')->id();
        Log::info($request->all());
        $validator = Validator::make($request->all(), [
        'user_id' => 'required|string|max:255',
        'type' => 'required|string|in:all',
        'payment_id' => 'required|string',
        'payment_type' => 'required|string',
        'payment_status' => 'required|string',
        'amount' => 'string',
        ]);
       
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }
        
            $franchise = Franchise::where('id','=', $request->user_id ?? 5)->first();
            Log::info($franchise);
           
            if (!$franchise) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }


            $user =  FranchiseWallet::create([
                'user_id'=> $franchise->id,
                'type' => $request->type,
                'payment_id' => $request->payment_id,
                'payment_type' => $request->payment_type,
                'payment_status' => $request->payment_status,
                'amount' => $request->amount,
               
            ]);
             if($user){
                 $update = Franchise::where('id', $franchise->id)->update([
                    'Contractor' => 100,
                    'Vendor' => 100,    
                    'Consultant' => 100,
                    'Technical' => 100,
                    'Non-Technical' => 100,
                    'Consumer' => 100,  


                 ]);
                }                
                
                $token = $franchise->createToken('token')->plainTextToken;
                $franchise->token = $token;

            return response()->json([
                        'success' => true,
                         'message' => 'transaction successfully added',
                         'user'=>$franchise,
                        
                    //    'data' => [
                    //        'list' => $wallet_list,
                    //      'wallet_balance' => $user_details->balance,
                    //    ],
                    ], 200);  
     }  

     public function franchise_recharge(Request $request)
     {
         // dd('hello');
         //Log::info($request->all());
         $id = Auth::guard('franchise_api')->id();
         $validator = Validator::make($request->all(), [
         // 'user_id' => 'required|string|max:255',
         'type' => 'required|string|in:Contractor,Vendor,Consultant,Technical,Non-Technical,Consumer',
         'payment_id' => 'required|string',
         'payment_type' => 'required|string',
         'payment_status' => 'required|string',
         'amount' => 'string',
         'count'=>'required'
         ]);
        
         if ($validator->fails()) {
             return response()->json([
                 'status' => 'error',
                 'errors' => $validator->errors(),
             ], 422);
         }
         
             $franchise = Franchise::where('id', $id ?? 5)->first();
            
             if (!$franchise) {
                 return response()->json(['error' => 'Unauthenticated'], 401);
             }
 
 
             $wallet =  FranchiseWallet::create([
                 'user_id'=>$id,
                 'type' => $request->type,
                 'payment_id' => $request->payment_id,
                 'payment_type' => $request->payment_type,
                 'payment_status' => $request->payment_status,
                 'amount' => $request->amount,
                
             ]);
              if($wallet){
                $column = $request->type; // e.g. contractor, vendor, etc.
                $incrementValue = (int) $request->count;
        
                // make sure the column exists in franchise table
                if (in_array($column, ['Contractor', 'Vendor','Consultant', 'Technical', 'Non-Technical', 'Consumer'])) {
                    // use Eloquent’s increment method (atomic update)
                    $franchise->increment($column, $incrementValue);
                }
            }
        
            
         
             return response()->json([
                         'success' => true,
                          'message' => 'recharge successfully!',
                     //    'data' => [
                     //        'list' => $wallet_list,
                     //      'wallet_balance' => $user_details->balance,
                     //    ],
                     ], 200);  
         
      }


      public function follow_users_list(Request $request)
      {
          // dd('hello');
          $id = Auth::guard('franchise_api')->id();
          $validator = Validator::make($request->all(), [
          'type' => 'required|string|in:Contractor,Vendor,Consultant,Technical,Non-Technical,Consumer',
          ]);
         
          if ($validator->fails()) {
              return response()->json([
                  'status' => 'error',
                  'errors' => $validator->errors(),
              ], 422);
          }
          
              $franchise = Franchise::where('id', $id ?? 5)->first();
             
              if (!$franchise) {
                  return response()->json(['error' => 'Unauthenticated'], 401);
              }

              $type = $request->input('type');
            
                  if ($type === 'Contractor') {
                      $users = UserDetail::where('as_a', 'Contractor')
                          ->where('ref_id', $franchise->code)
                          ->select('name', 'profile_img') 
                          ->get();
                  } if($type === 'Vendor') {
                      // For other types, use 'as_a' column
                      $users = UserDetail::where('as_a', 'Vendor')
                          ->where('ref_id', $franchise->code)
                          ->select('name', 'profile_img') 
                          ->get();
                       
                  }
                  if($type === 'Consultant') {
                    // For other types, use 'as_a' column
                    $users = UserDetail::where('as_a', 'Consultant')
                        ->where('ref_id', $franchise->code)
                        ->select('name', 'profile_img') 
                       ->get();
  
                }
                if($type === 'Technical') {
                    // For other types, use 'as_a' column
                    $users = UserDetail::where('as_a', 'Technical')
                        ->where('ref_id', $franchise->code)
                        ->select('name', 'profile_img') 
                       ->get();
                }
                if($type === 'Non-Technical') {
                    // For other types, use 'as_a' column
                    $users = UserDetail::where('as_a', 'Non-Technical')
                        ->where('ref_id', $franchise->code)
                        ->select('name', 'profile_img') 
                        ->get();
                }

                if($type === 'Consumer') {
                    // For other types, use 'as_a' column
                    $users = UserDetail::where('you_are', 'Consumer')
                        ->where('ref_id', $franchise->code)
                        ->select('name', 'profile_img') 
                       ->get();
                }
              
                  // Return JSON response
                  return response()->json([
                      'success' => true,
                      'message' => "Users fetched successfully for type: {$type}",
                      'count' => $users->count(),
                      'data' => $users,
                  ], 200);
        }

        //resend otp

        // resent otp
        public function franchise_resend_otp(Request $request, OtpService $otpService)
        {
            $validator = Validator::make($request->all(), [
                'user_id' => 'nullable|exists:franchise,id',
                'contact' => 'nullable|exists:franchise,mobile',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422);
            }
               if ($request->user_id) {
                 $user = Franchise::find($request->user_id);
                }
               if ($request->number) {
                $user = Franchise::where('mobile', $request->contact)->where('id','=',$request->user_id)->first();
                }
            if ($user) {
                $otp = rand(1000, 9999);
                $user->otp = $otp; // Generate a new OTP
                $otpService->sendOtp($user->mobile, $otp);
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'OTP resent successfully',
                    'otp' => $otp, // Return the new OTP
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }
              

        public function franchise_commision(Request $request)
      {
          // dd('hello');
            $id = Auth::guard('franchise_api')->id();
        
            $franchise = Franchise::where('id', $id ?? 5)->first();
             
              if (!$franchise) {
                  return response()->json(['error' => 'Unauthenticated'], 401);
              }
             
              $today = now()->toDateString();
    
              $records = Settlement::where('franchise_id', $franchise->id)
              ->whereDate('created_at', $today)
              ->sum('amount');  // sum of the `amount` column :contentReference[oaicite:1]{index=1}
      
              $totalAmount = $records->sum('amount');

              return response()->json([
                'franchise_id' => $franchise->id,
                'date' => $today,
                'total_amount' => $totalAmount,
                'records' => $records,
            ]);
        }

        public function franchise_contact(Request $request)
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
            $userMatches = \App\Models\UserDetail::where('number', 'like', "%{$input}%")
             ->pluck('number');

            // Search franchise table
            $franchiseMatches = \App\Models\Franchise::where('mobile', 'like', "%{$input}%")
                ->pluck('mobile');

            // Combine the result collections
            $allMatches = $userMatches->merge($franchiseMatches)->unique()->values();

            if ($allMatches->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No matching mobile numbers found.',
                    //'data'    => [],
                ]);
            }
    
            return response()->json([
                'success' => false,
                'message' => 'Matching mobile numbers found.',
                // 'data' => $matches
            ]);
        }


          // otp status and fcm token update in userdetail table->opt_staus and fcm_token
    public function franchise_otp_update(Request $request, OtpService $otpService)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:franchise,id',
            'otp_status' => 'required',
          
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }
        $user = Franchise::find($request->user_id);
        if ($user) {

            $user->otp_status = $request->otp_status;
            //$user->mob_token = $request->fcm_token;
            $token = $user->createToken('token')->plainTextToken;
            $user->save();
            // $user->token = $token;
            // Log::info("token Status Before Check: " . $token);

            return response()->json([
                'status' => 'success',
                'message' => 'OTP status update successfully',
                'data' => [
                    'user_id'=>$user->id,

                    'name' => $user->name,
                   // 'user_name' => $user->user_name,
                    'mobile' => $user->mobile,
                    'code' => $user->code,
                    'type_of' => $user->type_of,
                    //'gender' => $user->gender,
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

    public function choose_plan(Request $request, OtpService $otpService)
    {
       
        $id = Auth::guard('franchise_api')->id();
        $user = Franchise::find($id);
    
        if ($user) {
            // Example plans — key = number of days, value = price
            $vendorPlans = [
                5 => 500,
                10 => 1000,
                15 => 1500,
                20 => 2000,
            ];
    
            $contractorPlans = [
                5 => 500,
                10 => 1000,
                15 => 1500,
                20 => 2000,
            ];

            $consultantPlans = [
                5 => 500,
                10 => 1000,
                15 => 1500,
                20 => 2000,
            ];

            $technicalPlans = [
                5 => 500,
                10 => 1000,
                15 => 1500,
                20 => 2000,
            ];
            $nontechnicalPlans = [
                5 => 500,
                10 => 1000,
                15 => 1500,
                20 => 2000,
            ];
            $consumerPlans = [
                5 => 500,
                10 => 1000,
                15 => 1500,
                20 => 2000,
            ];
    
            return response()->json([
                'success' => true,
                'message' => 'Plans Charge List',
                'data' => [
                    'Vendor' => $vendorPlans,
                    'Contractor' => $contractorPlans,
                    'Consultant' => $consultantPlans,
                    'Technical' =>  $technicalPlans,
                    'Non-Technical' =>$nontechnicalPlans,
                    'Consumer' =>$consumerPlans,
                ],
            ], 200);
        }
    
        return response()->json([
            'status' => 'error',
            'message' => 'User not found',
        ], 404);
    }
    

    public function franchise_settlemenet(Request $request)
    {
        // dd('hello');
          $id = Auth::guard('franchise_api')->id();
      
          $franchise = Franchise::where('id', $id ?? 5)->first();
           
            if (!$franchise) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
           
            $records = Settlement::where('franchise_id', $franchise->id)
           ->get();

           
           $totalSales = 0;
           $totalLeads = 0;
           $totalAmount = 0;
           $settlementList = []; 
       
              // sum of the `amount` column :contentReference[oaicite:1]{index=1}

              foreach ($records as $record) {
                $amount = is_array($record->amount) ? $record->amount : json_decode($record->amount, true);


               // if (is_array($record->amount)) {
                    //$totalSales  += (float) ($record->amount['sales'] ?? 0);
                    //$totalLeads  += (float) ($record->amount['leads'] ?? 0);
                   // $totalAmount += (float) ($record->amount['total'] ?? 0);
               // } else {
                    // if 'amount' is stored as JSON string
                   // $amount = json_decode($record->amount, true);
                   // $totalSales  += (float) ($amount['sales'] ?? 0);
                   // $totalLeads  += (float) ($amount['leads'] ?? 0);
                  //  $totalAmount += (float) ($amount['total'] ?? 0);
                //}

                $settlementList[] = [
                    'id' => $record->id,
                    'from_date' => $record->from_date,
                    'to_date' => $record->to_date,
                    'franchise_id' => $record->franchise_id,
                    'status' => $record->status,
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                   'amount' => $amount['total'] ?? 0,
                ];
            }
        

            return response()->json([
              'success' => true,
              'seetlement_list' => $settlementList
          ]);
      }

      // forgot passowrd
    public function frn_forgot_password(Request $request,OtpService $otpService)
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
        $user = Franchise::where('mobile', $request->contact)->latest()->first();

        if (! $user) {
            return response()->json([
                'status' => true,
                'message' => 'user not found.',
            ], 200);
        }

       // $mobile = $user->number;
       $otp = rand(1000, 9999);

       // Update OTP in DB
       $user->update(['otp' => $otp]);
   
       // Send same OTP via service
       $otpService->sendOtp($user->mobile, $otp);


        return response()->json([
            'success' => true,
            'otp' => $otp,

        ]);

    }

     // forgot password
     public function frn_pswd_update(Request $request)
     {
         //Log::info($request->all());
         $validator = Validator::make($request->all(), [
             'new_password' => 'required|min:6',
             'contact' => 'required|exists:franchise,mobile',
         ]);
         $contact = $request->contact;
         $new_password = $request->new_password;
 
         if ($validator->fails()) {
             return response()->json([
                 'status' => 'error',
                 'errors' => $validator->errors(),
             ], 422);
         }
 
         $user = Franchise::where('mobile', $contact)->first();
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

        //popup version update
    public function frn_update_popup(Request $request)
    {
        return response()->json(['version' => '0.0.1'], 200);
    }

    // get old password
    public function franchise_old_password(Request $request)
    {

        $id = Auth::guard('franchise_api')->id();
        // dd($id);

        $user = Franchise::where('id', $id)->first();
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

    public function franchise_commision_list(Request $request)
    {
        // dd('hello');
          $id = Auth::guard('franchise_api')->id();
      
          $franchise = Franchise::where('id', $id ?? 5)->first();
           
            if (!$franchise) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
           
            $today = now()->toDateString();
  
            $records = Settlement::where('franchise_id', $franchise->id)
            ->get();  // sum of the `amount` column :contentReference[oaicite:1]{index=1}
    
            $totalSales = 0;
            $totalLeads = 0;
            $totalAmount = 0;
        

          
          foreach ($records as $record) {
            if (is_array($record->amount)) {
                $totalSales  += (float) ($record->amount['sales'] ?? 0);
                $totalLeads  += (float) ($record->amount['leads'] ?? 0);
                $totalAmount += (float) ($record->amount['total'] ?? 0);
            } else {
                // if 'amount' is stored as JSON string
                $amount = json_decode($record->amount, true);
                $totalSales  += (float) ($amount['sales'] ?? 0);
                $totalLeads  += (float) ($amount['leads'] ?? 0);
                $totalAmount += (float) ($amount['total'] ?? 0);
            }
        }
    
        return response()->json([
            'success'=>true,
            'franchise_id' => $franchise->id,
            // 'date' => $today,
            'sales_amount' => $totalSales,
            'leads_amount' => $totalLeads,
            'total_amount' => $totalAmount,
            // 'records' => $records,
        ]);
    }
    
      }
    

   




      
    
    



