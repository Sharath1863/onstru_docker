<?php

namespace App\Http\Controllers;

use App\Models\DropdownList;
use App\Models\Franchise;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Register_cnt extends Controller
{
    public function index()
    {
        $notificationCount = 0;

        return view('login.index', compact('notificationCount'));
    }

    public function forgot_pass()
    {
        $notificationCount = 0;

        return view('login.forgot_password', compact('notificationCount'));
    }

    public function login_otp_verify(Request $request, $type = null)
    {
        $notificationCount = 0;
        if ($type !== 'resend') {
            $request->validate(['phone' => 'required|digits:10']);
            $phone = $request->phone;
        } else {
            $phone = Session::get('phone');
            if (! $phone || ! preg_match('/^\d{10}$/', $phone)) {
                return redirect()->back()->withErrors(['phone' => 'Phone not found in session.']);
            }
        }
        $user = DB::table('user_detail')->where('number', $phone)->first();
        if (! $user) {
            return redirect()->back()->withErrors(['phone' => 'Given Phone number is not registered.']);
        }

        if ((! Session::has('otp') || Session::get('phone') !== $phone) || $type === 'resend') {
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
            // Log::info("SMS API response: " . $response);
            DB::table('user_detail')
                ->where('number', $mobile)
                ->update(['otp' => $v_code]);
            Session::put('otp', $v_code);
            Session::put('phone', $mobile);
        } else {
            // dd(Session::get('otp'));
            $v_code = Session::get('otp');
            $mobile = Session::get('phone');
        }

        return view('login.otp_verification', compact('notificationCount'));

        // if (RateLimiter::tooManyAttempts($request->ip(), 1)) {
        //     return redirect()->back()
        //         ->withErrors(['error' => 'Too many requests. Please try again later.']);
        // }

        // if ($type !== 'resend') {
        //     $request->validate(['phone' => 'required|digits:10']);
        //     $phone = $request->phone;
        // } else {
        //     $phone = Session::get('phone');
        //     if (!$phone) {
        //         return redirect()->back()->withErrors(['phone' => 'Phone not found in session.']);
        //     }
        // }

        // $request->validate([
        //     'phone' => 'required|digits:10',
        // ]);
        // $phone = $request->phone;
        // $user = DB::table('user_detail')->where('number', $phone)->first();

        // if (!$user) {
        //     return redirect()->back()->withErrors(['phone' => 'This phone number is not registered.']);
        // }
        // if ((!Session::has('otp') || Session::get('phone') !== $phone) || $type === 'resend') {
        //     $mobile = $user->number;
        //     $v_code = rand(1000, 9999);
        //     $authKey = "373776616e616e38313500";
        //     $senderId = "ONSTRU";
        //     $route = "2";
        //     $country = "91";
        //     $dltTeId = "1707172965885916248";
        //     // $mobile = $mobile;
        //     $message = urlencode("Welcome to Onstru! Your verification code is: $v_code. Please enter this code to complete your registration. - ONSTRU");
        //     // Build the full URL
        //     $url = "http://promo.smso2.com/api/sendhttp.php?authkey=$authKey&mobiles=$mobile&message=$message&sender=$senderId&route=$route&country=$country&DLT_TE_ID=$dltTeId";
        //     // Send the SMS
        //     Log::info("SMS URL: " . $url);
        //     $response = file_get_contents($url);
        //     Log::info("SMS API response: " . $response);
        //     $user_detail = DB::table('user_detail')
        //         ->where('number', $mobile)
        //         ->update(['otp' => $v_code]);
        //     Session::put('otp', $v_code);
        //     Session::put('phone', $mobile);
        //     // Send OTP via SMS
        // }else{
        //     $v_code = Session::get('otp');
        //     $mobile = Session::get('phone');
        //     // Session::put('otp', $v_code);
        //     // Session::put('phone', $mobile);
        // }
        // if ($user) {
        // Session::put('mobile', $user->number);

        // }
        // else {
        //     return redirect()->back()->withErrors([
        //         'phone' => 'This phone number is not registered.',
        //     ])->withInput();
        // }
    }

    public function login_otp_success()
    {
        return view('login.password_success');
    }

    public function admin_index()
    {
        return view('admin.admin_login');
    }

    public function register()
    {
        $vendortypeof = DropdownList::where('dropdown_id', 6)->get();
        $contractortypeof = DropdownList::where('dropdown_id', 7)->get();
        $consultanttypeof = DropdownList::where('dropdown_id', 8)->get();
        $professionaltypeof = DropdownList::where('dropdown_id', 9)->get();
        $locations = DropdownList::where('dropdown_id', 1)->get();
        $notificationCount = 0;

        return view('register.index', compact('professionaltypeof', 'vendortypeof', 'contractortypeof', 'consultanttypeof', 'locations', 'notificationCount'));
    }

    public function register_otp_verify(Request $request)
    {
        $notificationCount = 0;

        return view('register.otp_verification', compact('notificationCount'));
    }

    public function register_otp_success(Request $request)
    {
        $phone = Session::get('phone');
        $user = UserDetail::where('number', $phone)->first();
        if (! $user) {
            return redirect()->back()->withErrors(['mobile' => 'Mobile Number not found.']);
        }
        if ($user) {
            UserDetail::where('id', $user->id)->update([
                'otp_status' => 'yes',
            ]);
            Session::forget('otp');
            Session::forget('phone');
            Auth::login($user);

            return response()->json(['status' => 'success']);
        } else {
            return redirect()->back()->withErrors(['otp' => 'Invalid OTP Entered.']);
        }
    }

    public function register_otp_view()
    {
        $notificationCount = 0;

        return view('register.otp_success', compact('notificationCount'));
    }

    public function user_login(Request $request, Response $response)
    {
        $request->validate([
            'phone' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);
        $user = UserDetail::where('number', $request->phone)->first();
        // if ($user && Hash::check($request->password, $user->hash_password)) {
        if ($user && $request->password == $user->password) {
            Auth::loginUsingId($user->id);
            $cookie = cookie('user_id', Auth::user()->id, 43200);

            $userId = Auth::user()->id;

            // $name = 'Hari';

            // $slug = Str::uuid()->toString();

            $cookie_smartbot = cookie('user_data', $user->slug, 43200 * 60, '/', null, true, false, false, 'Lax');

            // $cookie_smartbot = cookie(
            //     'user_data',        // cookie name
            //     Auth::user()->id,   // value
            //     43200,              // 30 days
            //     '/',                // path
            //     null,               // current domain
            //     true,               // secure (HTTPS only)
            //     false,              // HttpOnly = allow JS to read it
            //     false,              // raw
            //     'Lax'               // SameSite allows some cross-site requests
            // );

            // Plain text cookie for user_data (Symfony Cookie)
            // $response->headers->set('Set-Cookie', 'user_data={'.$userId.'}; Path=/; Max-Age='.(43200 * 60).'; Secure; HttpOnly; SameSite=Lax');

            // Save FCM token if available
            if ($request->filled('web_token')) {
                $user->web_token = $request->web_token;
                $user->save();
            }

            $redirect = redirect()->intended(route('home'))
                ->with('success', 'Welcome Back '.$user->name)
                ->cookie($cookie)->cookie($cookie_smartbot);

            session()->forget('url.intended');

            return $redirect;
        }

        return back()->withErrors(['message' => 'Invalid Credentials.'])->withInput();
    }

    public function logout(Request $request)
    {
        UserDetail::where('id', Auth::id())->update([
            'web_token' => null,
        ]);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Cookie::queue(Cookie::forget('user_id'));

        return redirect()->route('login')->with('success', 'Logged Out Successfully');
    }

    public function store(Request $request)
    {
        $validTypeOfIds = DropdownList::pluck('id')->toArray();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|',
            'user_name' => 'required|string|unique:user_detail,user_name',
            'you_are' => 'required|string',
            'as_a' => 'nullable|string',
            'type_of' => 'nullable|array',
            'type_of.*' => ['nullable', Rule::in($validTypeOfIds)],
            'gender' => 'required|in:Male,Female,Other',
            'phone' => 'required|string',
            'location' => 'required|string',
            'password' => 'required|string|min:6',
        ], [
            'name.required' => 'Name is required.',
            'you_are.required' => 'You Are a is required.',
            'gender.required' => 'Gender is required.',
            'phone.required' => 'Phone Number is required.',
            'location.required' => 'Location is required.',
            'password.required' => 'Password is required.',
            'ref_code' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $existing = Franchise::where('mobile', '=', $request->phone)->first();
        if ($existing) {
            return redirect()->back()->withErrors(['error' => 'This contact number is already registered in Franchise.']);

        }

        $v_code = rand(1000, 9999);

        $authKey = '373776616e616e38313500';
        $senderId = 'ONSTRU';
        $route = '2';
        $country = '91';
        $dltTeId = '1707172965885916248';
        $mobile = $request->phone;
        Session::put('phone', $mobile);
        $message = urlencode("Welcome to Onstru! Your verification code is: $v_code. Please enter this code to complete your registration. - ONSTRU");

        // Build the full URL
        $url = "http://promo.smso2.com/api/sendhttp.php?authkey=$authKey&mobiles=$mobile&message=$message&sender=$senderId&route=$route&country=$country&DLT_TE_ID=$dltTeId";
        $response = file_get_contents($url);

        // Log::info('SMS API response: '.$response);

        $slug = Str::uuid()->toString();

        $user = UserDetail::create([
            'name' => $request->name,
            'user_name' => $request->user_name,
            'otp' => $v_code,
            'you_are' => $request->you_are,
            'as_a' => $request->as_a,
            'type_of' => $request->type_of ? implode(',', $request->type_of) : null,
            'gender' => $request->gender,
            'number' => $mobile,
            'location' => $request->location,
            'password' => $request->password,
            'hash_password' => Hash::make($request->password),
            'slug' => $slug,
        ]);
        Session::put('otp', $v_code);
        Session::put('phone', $user->number);

        return redirect()->route('register.otp-verify')->with('success', 'Registration Successful!');
    }

    public function checkUsername(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
        ]);

        $exists = UserDetail::where('user_name', $request->user_name)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function checkPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
        ]);
        $existsInFranchise = Franchise::where('mobile', $request->phone)->exists();

        $existsInUserDetail = UserDetail::where('number', $request->phone)->exists();
        $exists = $existsInFranchise || $existsInUserDetail;

        return response()->json(['exists' => $exists]);
    }

    public function login_password_success(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6',
        ]);
        $password = $request->password;
        $phone = Session::get('phone');
        if (! $phone) {
            return redirect()->back()->withErrors(['error' => 'Session expired. Please try again.']);
        }
        // Retrieve user
        $user = DB::table('user_detail')->where('number', $phone)->first();
        if (! $user) {
            return redirect()->back()->withErrors(['error' => 'User not found.']);
        }
        DB::table('user_detail')->where('id', $user->id)->update([
            'password' => $password,
            'hash_password' => Hash::make($password),
        ]);
        Session::forget('otp');
        Session::forget('phone');

        return view('login.password_success');
    }

    public function login_password(Request $request)
    {
        $phone = Session::get('phone');
        $user = DB::table('user_detail')->where('number', $phone)->first();
        if (! $user) {
            return redirect()->back()->withErrors(['mobile' => 'Mobile number not found.']);
        }
        DB::table('user_detail')->where('id', $user->id)->update([
            'otp_status' => 'yes',
        ]);
        Session::forget('otp');
        Session::forget('mobile');

        return response()->json(['status' => 'success']);
    }

    public function login_password_view()
    {
        $notificationCount = 0;

        return view('login.password', compact('notificationCount'));
    }
}
