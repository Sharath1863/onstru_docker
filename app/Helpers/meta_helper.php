<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use App\Models\Jobs;
use App\Models\Notification;
use App\Models\Products;
use App\Models\Service;
use App\Models\UserDetail;
use App\Models\UserProfile;
use App\Models\GstDetails;
use Illuminate\Support\Facades\Auth;

if (!function_exists('getMetaTags')) {
    function getMetaTags()
    {
        $currentPath = Request::path();
        $meta = [
            'title' => 'Onstru',
            'description' => 'Onstru Social',
            'keywords' => 'onstru, construction',
            'image' => asset('assets/images/Logo_Admin.png'),
        ];

        /* Dynamic Meta Tags */
        // My Profile
        if (Request::is('my-profile')) {
            if ($user = UserDetail::with('user_location')->where('id', Auth::id())->first()) {
                return [
                    'title' => 'Onstru | My Profile',
                    'description' => 'My Profile Details',
                    'keywords' => 'onstru, construction, user, profile details, ' . $user->name . ', ' . $user->user_name . ',' . ($user->user_location?->value ?? ''),
                    'image' => asset($user->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $user->profile_img : 'assets/images/Avatar.png'),
                ];
            }
        } elseif (Request::is('edit-profile*')) {
            if ($user = UserDetail::with('user_location')->where('id', Auth::id())->first()) {
                return [
                    'title' => 'Onstru | Update Profile',
                    'description' => 'Update Profile Details',
                    'keywords' => 'onstru, construction, user, profile details, ' . $user->name . ', ' . $user->user_name . ',' . ($user->user_location?->value ?? ''),
                    'image' => asset($user->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $user->profile_img : 'assets/images/Avatar.png'),
                ];
            }
        }
        // Profile
        if (Request::is('profile')) {
            if ($user = UserDetail::with('user_location')->where('id', Auth::id())->first()) {
                return [
                    'title' => 'Onstru | ' . $user->name . ' Profile',
                    'description' => $user->name . ' Profile',
                    'keywords' => 'onstru, construction, user, profile, ' . $user->name . ', ' . $user->user_name . ',' . ($user->user_location?->value ?? ''),
                    'image' => asset($user->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $user->profile_img : 'assets/images/Avatar.png'),
                ];
            }
        }
        // Jobs
        if (Request::is('job-edit/*') || Request::is('job-details/*') || Request::is('applied-profiles/*') || Request::is('job_apply/*') || Request::is('view-job-highlight/*')) {
            $jobId = Request::segment(2);
            if ($job = Jobs::find($jobId)) {
                return [
                    'title' => 'Onstru | ' . $job->title,
                    'description' => Str::limit(strip_tags($job->description), 160),
                    'keywords' => 'onstru, construction, job, ' . $job->locationRelation->value . ', ' . $job->categoryRelation->value,
                    'image' => asset('assets/images/NoImage.png'),
                ];
            }
        }
        // Products
        if (Request::is('edit-product/*') || Request::is('individual-product/*') || Request::is('view-product-highlight/*')) {
            $productId = Request::segment(2);
            if ($product = Products::find($productId)) {
                return [
                    'title' => 'Onstru | ' . $product->name,
                    'description' => Str::limit(strip_tags($product->description), 160),
                    'keywords' => 'onstru, construction, product, ' . $product->brand_name . ', ' . $product->categoryRelation->value . ',' . $product->locationRelation->value,
                    'image' => asset($product->cover_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $product->cover_img : 'assets/images/NoImage.png'),
                ];
            }
        }
        // Services
        if (Request::is('view-services/*') || Request::is('individual-service/*') || Request::is('view-service-highlight/*')) {
            $serviceId = Request::segment(2);
            if ($service = Service::find($serviceId)) {
                return [
                    'title' => 'Onstru | ' . $service->title,
                    'description' => Str::limit(strip_tags($service->description), 160),
                    'keywords' => 'onstru, construction, service, ' . $service->serviceType->value . ',' . $service->locationRelation->value,
                    'image' => asset($service->image ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $service->image : 'assets/images/NoImage.png'),
                ];
            }
        }
        // User Profile
        if (Request::is('user-profile/*')) {
            $userId = Request::segment(2);
            if ($user = UserDetail::find($userId)) {
                return [
                    'title' => 'Onstru | ' . $user->name . ' Profile',
                    'description' => $user->name . ' Profile',
                    'keywords' => 'onstru, construction, user, ' . $user->user_name,
                    'image' => asset($user->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $user->profile_img : 'assets/images/NoImage.png'),
                ];
            }
        }

        /* Static Meta Tags */
        switch ($currentPath) {
            /* Register */
            case 'register':
                $meta['title'] = 'Onstru | Portal Register';
                $meta['description'] = 'Register';
                $meta['keywords'] = 'onstru, construction, register, user, portal';
                $meta['image'] = asset('assets/images/Portal_Register.png');
                break;
            case 'register-otp-verify':
                $meta['title'] = 'Onstru | OTP Verification';
                $meta['description'] = 'OTP Verification';
                $meta['keywords'] = 'onstru, construction, otp, verification, user, portal';
                $meta['image'] = asset('assets/images/Portal_OTP_Verify.png');
                break;
            case 'register-otp-view':
                $meta['title'] = 'Onstru | OTP Verification Successful';
                $meta['description'] = 'OTP Verified Successfully';
                $meta['keywords'] = 'onstru, construction, otp, verification, success, user, portal';
                $meta['image'] = asset('assets/images/Portal_OTP_Success.png');
                break;
            /* Login */
            case 'login':
                $meta['title'] = 'Onstru | Portal Login';
                $meta['description'] = 'Login';
                $meta['keywords'] = 'onstru, construction, login, user, portal';
                $meta['image'] = asset('assets/images/Portal_Login.png');
                break;
            /* Forgot Password */
            case 'forgot-password':
                $meta['title'] = 'Onstru | Forgot Password';
                $meta['description'] = 'Forgot Password';
                $meta['keywords'] = 'onstru, construction, forgot, password, user, portal';
                $meta['image'] = asset('assets/images/Portal_Forgot.png');
                break;
            case 'forgot-otp-verify':
                $meta['title'] = 'Onstru | OTP Verification';
                $meta['description'] = 'OTP Verification';
                $meta['keywords'] = 'onstru, construction, forgot, password, otp, verification, user, portal';
                $meta['image'] = asset('assets/images/Portal_OTP_Verify.png');
                break;
            case 'new-password-view':
                $meta['title'] = 'Onstru | Change Password';
                $meta['description'] = 'Change Password';
                $meta['keywords'] = 'onstru, construction, forgot, password, success, user, portal';
                $meta['image'] = asset('assets/images/Portal_New_Password.png');
                break;
            case 'password-success':
                $meta['title'] = 'Onstru | Password Updated';
                $meta['description'] = 'Password Updated Successfully';
                $meta['keywords'] = 'onstru, construction, password, verification, success, user, portal';
                $meta['image'] = asset('assets/images/Portal_OTP_Success.png');
                break;
            /* Home */
            case '/':
            case 'home':
                $meta['title'] = 'Onstru | Home';
                $meta['description'] = 'Home';
                $meta['keywords'] = 'onstru, construction, home, highlights, post, reels, hashtags, location, user';
                $meta['image'] = asset('assets/images/Favicon.png');
                break;
            /* Reels */
            case 'reels':
                $meta['title'] = 'Onstru | Reels';
                $meta['description'] = 'Reels';
                $meta['keywords'] = 'onstru, construction, highlights, post, reels, user';
                $meta['image'] = asset('assets/images/Favicon.png');
                break;
            /* Explore */
            case 'explore':
                $meta['title'] = 'Onstru | Explore';
                $meta['description'] = 'Explore';
                $meta['keywords'] = 'onstru, construction, home, post, reels, hashtags, search, user';
                $meta['image'] = asset('assets/images/Favicon.png');
                break;
            /* Jobs */
            case 'jobs':
                $meta['title'] = 'Onstru | Jobs';
                $meta['description'] = 'Jobs';
                $meta['keywords'] = 'onstru, construction, jobs, title, category, apply, vacancy, skills, qualification, location, boosting';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            case 'job-post':
                $meta['title'] = 'Onstru | Add Jobs';
                $meta['description'] = 'Add Job';
                $meta['keywords'] = 'onstru, construction, jobs, add, title, category, vacancy, skills, qualification, location';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            case 'applied-jobs':
                $meta['title'] = 'Onstru | Applied Jobs';
                $meta['description'] = 'Applied Job';
                $meta['keywords'] = 'onstru, construction, jobs, applied, title, category, vacancy, skills, qualification, location, user';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            case 'jobs-highlight':
                $meta['title'] = 'Onstru | Boosted Jobs';
                $meta['description'] = 'Boosted Jobs';
                $meta['keywords'] = 'onstru, construction, jobs, boosted, title, category, vacancy, skills, qualification, location, date, expire';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            /* Services */
            case 'services':
                $meta['title'] = 'Onstru | Services';
                $meta['description'] = 'Services';
                $meta['keywords'] = 'onstru, construction, services, title, serviceType, budget, location, highlighting';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            case 'requested-services':
                $meta['title'] = 'Onstru | Requested Services';
                $meta['description'] = 'Requested Services';
                $meta['keywords'] = 'onstru, construction, services, requested, title, serviceType, budget, location, highlighting';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            case 'services-request':
                $meta['title'] = 'Onstru | Service Requests';
                $meta['description'] = 'Service Requests';
                $meta['keywords'] = 'onstru, construction, services, request, title, serviceType, budget, location, highlighting';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            case 'services-highlight':
                $meta['title'] = 'Onstru | Highlighted Services';
                $meta['description'] = 'Highlighted Services';
                $meta['keywords'] = 'onstru, construction, services, highlighted, title, serviceType, budget, location, clicks, expire';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            /* Products */
            case 'products':
                $meta['title'] = 'Onstru | Products';
                $meta['description'] = 'Products';
                $meta['keywords'] = 'onstru, construction, products, title, category, brand, price, location, highlighting';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            case 'add-product':
                $meta['title'] = 'Onstru | Add Product';
                $meta['description'] = 'Add Product';
                $meta['keywords'] = 'onstru, construction, product, add, title, category, brand, price, location, listing';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            case 'products-highlight':
                $meta['title'] = 'Onstru | Highlighted Products';
                $meta['description'] = 'Highlighted Products';
                $meta['keywords'] = 'onstru, construction, products, highlighted, title, brand, category, price, location, clicks, expire';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            /* Cart */
            case 'cart':
                $meta['title'] = 'Onstru | Cart';
                $meta['description'] = 'Cart';
                $meta['keywords'] = 'onstru, construction, cart, products, orders, pincode, address, quantity, amount, cashback, place order, items';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            /* Orders */
            case 'orders':
                $meta['title'] = 'Onstru | Orders';
                $meta['description'] = 'Orders';
                $meta['keywords'] = 'onstru, construction, orders, products, amount, order id, quantity, shipping';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            case 'order-summary':
                $meta['title'] = 'Onstru | Order Summary';
                $meta['description'] = 'Order Summary';
                $meta['keywords'] = 'onstru, construction, orders, products, summary, amount, order id, quantity, shipping';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            case 'orders-status':
                $meta['title'] = 'Onstru | Order Status';
                $meta['description'] = 'Order Status';
                $meta['keywords'] = 'onstru, construction, orders, products, status, amount, order id, quantity, shipping';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            case 'order-accept/{id}':
                $meta['title'] = 'Onstru | Order Accept';
                $meta['description'] = 'Order Accept';
                $meta['keywords'] = 'onstru, construction, orders, products, accept, amount, order id, quantity, shipping';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            case 'order-outfordelivery':
                $meta['title'] = 'Onstru | Order Out for Delivery';
                $meta['description'] = 'Order Out for Delivery';
                $meta['keywords'] = 'onstru, construction, orders, products, out for delivery, amount, order id, quantity, shipping';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            case 'tracking/{id}':
                $meta['title'] = 'Onstru | Order Tracking';
                $meta['description'] = 'Order Tracking';
                $meta['keywords'] = 'onstru, construction, orders, tracking, products, summary, amount, order id, quantity, shipping';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            /* Wishlist */
            case 'wishlist':
                $meta['title'] = 'Onstru | Wishlist';
                $meta['description'] = 'Wishlist';
                $meta['keywords'] = 'onstru, construction, wishlist, notify, products, quantity';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            /* Leads */
            case 'leads':
                $meta['title'] = 'Onstru | Leads';
                $meta['description'] = 'Leads';
                $meta['keywords'] = 'onstru, construction, leads, title, serviceType, budget, location, repost';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            case 'owned-leads':
                $meta['title'] = 'Onstru | Owned Leads';
                $meta['description'] = 'Owned Leads';
                $meta['keywords'] = 'onstru, construction, leads, owned, title, serviceType, budget, location, reviews';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            /* People */
            case 'people':
                $meta['title'] = 'Onstru | Peoples';
                $meta['description'] = 'Peoples';
                $meta['keywords'] = 'onstru, construction, peoples, find, role, category, location, user';
                $meta['image'] = asset('assets/images/Favicon.png');
                break;
            /* Hire */
            case 'hire':
                $meta['title'] = 'Onstru | Hire';
                $meta['description'] = 'Hire';
                $meta['keywords'] = 'onstru, construction, candidate, find, role, category, location, hire, readytowork';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            /* Settings */
            case 'settings':
                $meta['title'] = 'Onstru | Settings';
                $meta['description'] = 'Settings';
                $meta['keywords'] = 'onstru, construction, settings, saved, jobs, post, reels, my orders, change password, password, delete, account, terms, privacy, refund, cancellation, policies, contact';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            /* Notification */
            case 'notification':
                $meta['title'] = 'Onstru | Notification';
                $meta['description'] = 'Notification';
                $meta['keywords'] = 'onstru, construction, notification, notify, user, follow, following, request, post, reels, comment, like, jobs, products, services, leads, orders, approved, pending, rejected';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            /* Premium */
            case 'premium':
                $meta['title'] = 'Onstru | Premium';
                $meta['description'] = 'Premium';
                $meta['keywords'] = 'onstru, construction, premium, subscription, post, reels, blog, caption, hashtags, location, admin';
                $meta['image'] = asset('assets/images/img_premium.png');
                break;
            /* Chatbot */
            case 'chatbot':
                $meta['title'] = 'Onstru | Chatbot';
                $meta['description'] = 'Chatbot';
                $meta['keywords'] = 'onstru, construction, chatbot, subscription, chat, artificial intelligence';
                $meta['image'] = asset('assets/images/img_bot.png');
                break;
            /* Invoices */
            case 'invoices':
                $meta['title'] = 'Onstru | Invoices';
                $meta['description'] = 'Invoices';
                $meta['keywords'] = 'onstru, construction, invoices, bills, print, projects, jobs, products, services, leads, highlighting, boosting';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            /* Cashback */
            case 'cashback':
                $meta['title'] = 'Onstru | Cashback';
                $meta['description'] = 'Cashback';
                $meta['keywords'] = 'onstru, construction, cashback, payment, vendor, user, products, orders';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            /* Wallet */
            case 'wallet':
                $meta['title'] = 'Onstru | Wallet';
                $meta['description'] = 'Wallet';
                $meta['keywords'] = 'onstru, construction, wallet, payment, transaction, payment gateway, recharge, balance';
                $meta['image'] = asset('assets/images/NoImage.png');
                break;
            default:
                break;
        }
        // dd($meta);
        return $meta;
    }

    function getNotify()
    {
        if (!Auth::check()) {
            return 0;
        }

        return Notification::where('reciever', Auth::id())
            ->where('status', 'active')
            ->count();
    }

    function getProfileCompletion($user_id = null)
    {
        if ($user_id === null) {
            if (!Auth::check()) {
                return 0;
            }
            $user = Auth::user();
        } else {
            $user = UserDetail::find($user_id);   // << Fetch user by ID
            if (!$user) {
                return 0;
            }
        }
        $profileParts = [];

        // Basic Details
        $user_details = UserDetail::select('id', 'name', 'user_name', 'gender', 'bio', 'email', 'location')
            ->where('id', $user->id)
            ->first();
        $fields = ['name', 'user_name', 'gender', 'bio', 'email', 'location'];
        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($user_details?->$field)) {
                $filled++;
            }
        }
        $profileCompletion = round(($filled / count($fields)) * 100);
        $profileParts[] = $profileCompletion;

        // Contractor / Consultant Additional Details
        if (in_array($user->as_a, ['Contractor', 'Consultant'])) {
            $additional = UserProfile::where('c_by', $user->id)->first();
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
                'income_tax'
            ];
            $filled = 0;
            foreach ($fields as $field) {
                if (!empty($additional?->$field)) {
                    $filled++;
                }
            }
            $profileParts[] = round(($filled / count($fields)) * 100);
        }

        // Vendor Details
        if ($user->as_a === 'Vendor') {
            $vendor = UserProfile::where('c_by', $user->id)->first();
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
                'location_catered'
            ];
            $filled = 0;
            foreach ($fields as $field) {
                if (!empty($vendor?->$field)) {
                    $filled++;
                }
            }
            $profileParts[] = round(($filled / count($fields)) * 100);
        }

        // Student (Professional > Student)
        if ($user->you_are === 'Professional' && isset($user->type_of_names[0]) && $user->type_of_names[0] === 'Student') {
            $student = UserProfile::where('c_by', $user->id)->first();
            $fields = ['professional_status', 'education', 'college', 'aadhar_no', 'pan_no'];
            $filled = 0;
            foreach ($fields as $field) {
                if (!empty($student?->$field)) {
                    $filled++;
                }
            }
            $profileParts[] = round(($filled / count($fields)) * 100);
        }

        // Working (Professional > Working)
        if ($user->you_are === 'Professional' && isset($user->type_of_names[0]) && $user->type_of_names[0] === 'Working') {
            $working = UserProfile::where('c_by', $user->id)->first();
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
                'pan_no'
            ];
            $filled = 0;
            foreach ($fields as $field) {
                if (!empty($working?->$field)) {
                    $filled++;
                }
            }
            $profileParts[] = round(($filled / count($fields)) * 100);
        }

        // GST Details (for Business)
        if ($user->you_are === 'Business') {
            $gst = GstDetails::where('user_id', $user->id)->first();
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
                'annual_turnover'
            ];
            $filled = 0;
            foreach ($fields as $field) {
                if (!empty($gst?->$field)) {
                    $filled++;
                }
            }
            $profileParts[] = round(($filled / count($fields)) * 100);
        }

        // Profile Image
        $profileParts[] = $user->profile_img ? 100 : 0;

        // Final Completion Average
        return count($profileParts) > 0
            ? round(array_sum($profileParts) / count($profileParts))
            : 0;
    }

}