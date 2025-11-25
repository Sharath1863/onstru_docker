<?php

use App\Events\PrivateMessageSent;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Bill_cnt;
use App\Http\Controllers\Cashback_cnt;
use App\Http\Controllers\Chat_cnt;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\Explore_cnt;
use App\Http\Controllers\Hashtag_cnt;
use App\Http\Controllers\Highlights_cnt;
use App\Http\Controllers\Home_cnt;
use App\Http\Controllers\Job_cnt;
use App\Http\Controllers\Leads_cnt;
use App\Http\Controllers\Notify_cnt;
use App\Http\Controllers\Orders_cnt;
use App\Http\Controllers\Payment_cnt;
use App\Http\Controllers\People_cnt;
use App\Http\Controllers\Policy_cnt;
use App\Http\Controllers\Popup_cnt;
use App\Http\Controllers\Premium_cnt;
use App\Http\Controllers\Product_cnt;
use App\Http\Controllers\Profile_cnt;
use App\Http\Controllers\Project_cnt;
use App\Http\Controllers\Reels_cnt;
use App\Http\Controllers\Register_cnt;
use App\Http\Controllers\Report_cnt;
use App\Http\Controllers\Service_cnt;
use App\Http\Controllers\Settings_cnt;
use App\Http\Controllers\Wallet_cnt;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckUserAuth;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

// Route::get('/test-broadcast', function () {
//     $receiverId = Auth::id(); // send to current user for test
//     $message = [
//         'sender_id' => Auth::id(),
//         'receiver_id' => $receiverId,
//         'text' => 'Hello! This is a test message eeeeeeeeeeeeeeeeeee.',
//     ];

//     broadcast(new PrivateMessageSent($message, $receiverId));

//     return 'Message broadcasted!';
// })->middleware('auth');

// Route::group(function () {
//     header('Access-Control-Allow-Origin: *');
//     header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
//     header('Access-Control-Allow-Headers: Content-Type, Authorization');

//     // your existing routes here
//
// });

// Route::get('user_search', function (Request $request) {
//     // $search = $request->input('query');
//     // $results = App\Models\UserDetail::where('name', 'LIKE', "%{$search}%")
//     //     ->orWhere('user_name', 'LIKE', "%{$search}%")
//     //     ->limit(10)
//     //     ->get();

//     // $results = UserDetail::search('vendor type85')->get();

//     $results = UserDetail::search('', function ($meilisearch, $query, $options) {
//         $options['filter'] = 'type_of = "vendor type1"';

//         return $meilisearch->search($query, $options);
//     })->get();

//     // dd($results);

//     return response()->json(
//         $results->map(function ($user) {
//             return [
//                 'id' => $user->id,
//                 'name' => $user->name,
//                 'type_of' => $user->type_of_names, // array or string
//             ];
//         })
//     );
// })->name('user_search');

Route::get('/face_mob', [App\Http\Controllers\FaceController::class, 'face_mob'])->name('face_mob');
Route::get('/faces_json', [App\Http\Controllers\FaceController::class, 'faces_json'])->name('faces_json');

// payment integration
Route::view('/check', 'payment.check');
Route::post('/pay', [Payment_cnt::class, 'initiatePayment'])->name('pay');
Route::post('/product-pay', [Payment_cnt::class, 'productPayment'])->name('product-pay');
Route::get('/payment/callback/', [Payment_cnt::class, 'handleCallback'])->name('cashfree.callback');
Route::post('/payment/webhook', [Payment_cnt::class, 'handleWebhook'])->name('cashfree.webhook');
Route::post('/payment/webhook-product', [Payment_cnt::class, 'handleWebhookProduct'])->name('cashfree.product.webhook');

// Broadcast::routes(['middleware' => [CheckUserAuth::class]]);

// ----------------------------------------- PUBLIC ROUTES -----------------------------------------------------------
Route::get('/refferral', function (Request $request) {
    $id = $request->query('id'); // e.g. DDPER001

    // Build Google Play Store URL
    $playUrl = 'https://play.google.com/store/apps/details?id=com.onstru.superapp&referrer=id%' . $id;

    return redirect()->away($playUrl);
});

// product tracking
Route::get('track/{track_id}', [Orders_cnt::class, 'track_order'])->name('track');
Route::post('track-location', [Orders_cnt::class, 'track_location'])->name('track.location');
Route::post('update-track', [Orders_cnt::class, 'update_track'])->name('update.track');

// fcm token for wweb app
Route::post('/check_fcm', [Notify_cnt::class, 'token'])->name('store.fcm.token');

// ----------------------------------------- LOGIN / REGISTER -----------------------------------------------------------
Route::get('login', [Register_cnt::class, 'index'])->name('login');
Route::get('forgot-password', [Register_cnt::class, 'forgot_pass'])->name('forgot-password');
Route::match(['get', 'post'], 'forgot-otp-verify/{type?}', [Register_cnt::class, 'login_otp_verify'])->name('login.otp-verify');
Route::post('new-password', [Register_cnt::class, 'login_password'])->name('login.new-password');
Route::get('new-password-view', [Register_cnt::class, 'login_password_view'])->name('login.new-password-view');
Route::post('password-success', [Register_cnt::class, 'login_password_success'])->name('login.password-success');
Route::post('user_login', [Register_cnt::class, 'user_login'])->name('user_login');

// Register_cnt
Route::get('register', [Register_cnt::class, 'register'])->name('register');
Route::post('/validate-username', [Register_cnt::class, 'checkUsername']);
Route::post('/validate-phone', [Register_cnt::class, 'checkPhone']);
Route::get('register-otp-verify', [Register_cnt::class, 'register_otp_verify'])->name('register.otp-verify');
Route::get('register-otp-view', [Register_cnt::class, 'register_otp_view'])->name('register.otp-view');
Route::post('register-otp-success', [Register_cnt::class, 'register_otp_success'])->name('register.otp-success');
Route::post('register-post', [Register_cnt::class, 'store'])->name('register-post');

Broadcast::routes(['middleware' => [CheckUserAuth::class]]);

// ---------------------------------------- USER PANEL (After Authentication) ------------------------------------------------------------
Route::middleware([CheckUserAuth::class])->group(function () {

    // ChatBot
    Route::get('chatbot', [Chat_cnt::class, 'chatbot'])->name('chatbot');
    Route::post('chatbot-subscribe', [Chat_cnt::class, 'subscribe'])->name('chatbot.subscribe');

    // Check User Authentication
    Route::get('/', [Home_cnt::class, 'home'])->name('home');
    Route::get('home', [Home_cnt::class, 'home'])->name('home.index');
    Route::get('feed', [Home_cnt::class, 'home'])->name('feed.home');
    Route::get('logout', [Register_cnt::class, 'logout'])->name('logout');

    // Explore
    Route::get('explore/{search?}', [Explore_cnt::class, 'explore'])->name('explore');

    // likes and comment toogle
    Route::post('/toggle-like', [Home_cnt::class, 'toggle_like'])->name('toggle.like');
    Route::post('/toggle-save', [Home_cnt::class, 'toggle_save'])->name('toggle.save');
    Route::post('/toggle-like-list', [Home_cnt::class, 'getLikesList'])->name('toggle.getLikesList');
    Route::post('/toggle-comment', [Home_cnt::class, 'toggle_comment'])->name('toggle.comment');
    Route::post('/store-comment', [Home_cnt::class, 'storeComment'])->name('toggle.storeComment');
    Route::post('/toggle-comment-list', [Home_cnt::class, 'getCommentList'])->name('toggle.getCommentList');
    Route::post('/toggle-share-list', [Home_cnt::class, 'getShareList'])->name('toggle.getShareList');

    // search share users
    Route::post('/search-share-users', [Chat_cnt::class, 'search_share_users'])->name('search.share.users');

    // share post
    Route::post('/share-post', [Chat_cnt::class, 'chat_msg_arr'])->name('share.post');

    // report
    Route::post('/toggle-report', [Home_cnt::class, 'toggle_report'])->name('toggle.report');
    Route::post('/user/report', [Report_cnt::class, 'user_report'])->name('user.report');
    Route::post('/post/report', [Report_cnt::class, 'post_report'])->name('post.report');

    // Jobs
    Route::get('jobs', [Job_cnt::class, 'index'])->name('jobs');
    Route::post('job_location', [Job_cnt::class, 'job_location'])->name('job_location');
    Route::get('job-post', [Job_cnt::class, 'job_post'])->name('job.post');
    Route::post('jobs/store', [Job_cnt::class, 'store'])->name('jobs.store');
    Route::get('job-edit/{id}', [Job_cnt::class, 'job_edit'])->name('job.edit');
    Route::put('update-job/{id}', [Job_cnt::class, 'update_job'])->name('update-job');
    Route::get('job-details/{id?}', [Job_cnt::class, 'job_details'])->name('job.details');
    Route::get('job-filter', [Job_cnt::class, 'filter'])->name('job.filter');
    Route::post('job_apply/{id}', [Job_cnt::class, 'apply'])->name('job.apply');
    Route::get('job_apply/{id}', [Job_cnt::class, 'applyForm'])->name('job.apply.form');
    Route::get('application-submitted', [Job_cnt::class, 'job_apply_submit'])->name('jobs.apply_submit');
    Route::post('/jobs/{id}/toggle-save', [Job_cnt::class, 'toggleSave'])->name('jobs.toggleSave');
    Route::post('/job/boost', [Job_cnt::class, 'boost_job'])->name('job.boost');
    Route::get('applied-jobs', [Job_cnt::class, 'jobs_applied'])->name('applied-jobs');

    // POPUP for individual post
    Route::post('ind_post', [Popup_cnt::class, 'ind_post'])->name('ind_post');

    // Job_cnts (Profile)
    Route::get('job-profile', [Job_cnt::class, 'job_prof']);
    Route::post('/jobs/toggle-status/{id}', [Job_cnt::class, 'togglejobStatus'])->name('jobs.toggleStatus');
    Route::get('applied-profiles/{id}', [Job_cnt::class, 'applied_list']);
    Route::get('resume/download/{id}', [Job_cnt::class, 'downloadResume'])->name('resume.download');

    // Products
    Route::post('/get-driving-distance', [Product_cnt::class, 'getDrivingDistance'])->name('distance.calculate');

    // Route::get('products', [Product_cnt::class, 'products'])->name('products');
    Route::match(['get', 'post'], '/products', [Product_cnt::class, 'products']);
    // Route::get('products_search', [Product_cnt::class, 'products_search'])->name('products_search');
    Route::post('product/store', [Product_cnt::class, 'store'])->name('product.store');
    Route::get('wishlist', [Product_cnt::class, 'wishlist'])->name('wishlist');
    Route::get('individual-product/{id?}', [Product_cnt::class, 'individual_product'])->name('individual-product');
    Route::get('individual-product-dup/{id}', [Product_cnt::class, 'individual_product_dup'])->name('individual-product-dup');
    Route::get('add-product', [Product_cnt::class, 'add_product'])->name('add_product');
    Route::get('edit-product/{id}', [Product_cnt::class, 'edit_product'])->name('edit-product');
    Route::put('update-product/{id}', [Product_cnt::class, 'update_product'])->name('update-product/{id}');
    Route::post('product/store', [Product_cnt::class, 'store'])->name('product.store');
    Route::post('toggle-saved-product', [Product_cnt::class, 'toggleSavedProduct'])->name('toggle.saved.product');
    Route::post('/reviews', [Product_cnt::class, 'review'])->name('reviews.store');
    Route::post('product/highlight', [Product_cnt::class, 'boost_product'])->name('product.highlight');
    Route::post('/product/toggle-status', [Product_cnt::class, 'toggleProductStatus'])->name('product.toggleStatus');
    Route::get('/get-commission/{categoryId}', [Product_cnt::class, 'getCommissionByCategory'])->name('get-commission');
    Route::post('/update-product-price', [Product_cnt::class, 'updatePrice'])->name('update.product.price');

    // hubs
    Route::post('/hubs/store', [Product_cnt::class, 'hubStore'])->name('hubs.store');
    Route::post('/hubs/update', [Product_cnt::class, 'hubUpdate'])->name('hubs.update');

    // Order Summary
    Route::get('order-summary', [Orders_cnt::class, 'order_summary'])->name('order-summary');
    Route::get('payment-success', [Orders_cnt::class, 'payment_success'])->name('payment-success');
    Route::get('order-accept/{order_id}', [Orders_cnt::class, 'order_accept'])->name('order-accept');
    Route::post('order-outfordelivery', [Orders_cnt::class, 'order_outfordelivery'])->name('order-outfordelivery');
    Route::get('orders', [Orders_cnt::class, 'orders'])->name('orders');
    Route::get('orders-status', [Orders_cnt::class, 'order_status'])->name('orders-status');
    Route::put('order-update', [Orders_cnt::class, 'order_update'])->name('order-update');
    Route::post('order-otp-update', [Orders_cnt::class, 'order_otp_update'])->name('order-otp-update');
    Route::post('order-tracking-update', [Orders_cnt::class, 'orderTrackingUpdate'])->name('order-tracking-update');
    Route::get('tracking/{id}', [Orders_cnt::class, 'tracking'])->name('tracking');

    Route::get('checkout', [Product_cnt::class, 'checkout'])->name('checkout');
    Route::post('gst-checkout-verify', [Product_cnt::class, 'gst_verify'])->name('gst.verify');
    // Route::post('/gst/verify/start', [Product_cnt::class, 'startVerification'])->name('gst.verify.start');
    // Route::get('/gst/verify/status', [Product_cnt::class, 'checkStatus'])->name('gst.verify.status');
    Route::post('store-address', [Product_cnt::class, 'address_store'])->name('address.store');
    Route::get('address/{id}', [Product_cnt::class, 'getAddress'])->name('address.get');
    Route::get('payment-process', [Product_cnt::class, 'payment_process'])->name('payment-process');

    // Cart
    Route::get('cart', [Product_cnt::class, 'cart'])->name('cart');
    Route::post('add-to-cart', [Product_cnt::class, 'cart_store'])->name('cart.store');
    // Route::delete('cart-rm_cart/{id}', [Product_cnt::class, 'rm_cart'])->name('cart.rm_cart');
    // Route::post('cart-update-qty', [Product_cnt::class, 'updateQty']);
    // Route::post('cart/apply-cashback', [Product_cnt::class, 'applyCashback'])->name('cart.applyCashback');
    Route::get('cart-summary', [Product_cnt::class, 'getCartSummary'])->name('cart.summary');
    // Route::post('cart/update-status/{id}', [Product_cnt::class, 'updateStatus'])->name('cart.updateStatus');
    // Route::delete('cart/remove/{id}', [Product_cnt::class, 'removeItem'])->name('cart.remove');

    Route::post('/cart/update-quantity', [Product_cnt::class, 'updateQuantity'])->name('cart.update.quantity');
    Route::post('/cart/remove', [Product_cnt::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/save-for-later', [Product_cnt::class, 'saveForLater'])->name('cart.save_for_later');
    Route::post('/cart/move-to-cart', [Product_cnt::class, 'moveToCart'])->name('cart.move');
    Route::post('/cart/apply-cashback', [Product_cnt::class, 'applyCashback'])->name('cart.apply.cashback');
    Route::post('/cart/store-deliverable', [Product_cnt::class, 'storeDeliverables'])->name('cart.store.deliverable');

    // Profile
    Route::get('profile', [Profile_cnt::class, 'profile'])->name('profile');
    Route::get('user-profile/{id}/{post_id?}/{type?}', [Profile_cnt::class, 'userProfile'])->name('user-profile');
    Route::post('ready-to-work', [Profile_cnt::class, 'storeReadyToWork'])->name('ready-to-work.store');
    Route::put('ready-to-work/{id}', [Profile_cnt::class, 'updateReadyToWork'])->name('ready-to-work.update');
    Route::patch('ready-to-work/{id}/toggle-status', [Profile_cnt::class, 'toggleReadyToWorkStatus'])->name('ready-to-work.toggle-status');
    Route::delete('ready-to-work/{id}', [Profile_cnt::class, 'deleteReadyToWork'])->name('ready-to-work.delete');
    Route::post('/buy-badge', [Profile_cnt::class, 'buyBadge'])->name('buy.badge');

    // Follow & UnFollow
    Route::post('/users/follow/{user}', [Profile_cnt::class, 'follow'])->name('users.follow');
    Route::delete('/users/follow/{user}', [Profile_cnt::class, 'unfollow'])->name('users.unfollow');

    // Projects
    Route::post('projects/store', [Project_cnt::class, 'storeProject'])->name('projects.store');
    Route::get('projects/{id}/edit', [Project_cnt::class, 'getProject'])->name('projects.edit');
    Route::put('projects/{id}', [Project_cnt::class, 'updateProject'])->name('projects.update');
    Route::delete('projects/{id}', [Project_cnt::class, 'deleteProject'])->name('projects.destroy');

    // Service
    Route::match(['get', 'post'], '/services', [Service_cnt::class, 'index'])->name('services');
    Route::get('services/{id}/edit', [Service_cnt::class, 'getService'])->name('services.edit');
    Route::put('services/{id}', [Service_cnt::class, 'updateService'])->name('services.update');
    Route::delete('services/{id}', [Service_cnt::class, 'deleteService'])->name('services.delete');
    Route::post('services_store', [Service_cnt::class, 'storeService'])->name('services.store');
    Route::post('services/highlight', [Service_cnt::class, 'highlightService'])->name('services.highlight');
    Route::post('service/request/store', [Service_cnt::class, 'storeServiceRequest'])->name('service.request.store')->middleware('auth');

    Route::get('individual-service/{id?}', [Service_cnt::class, 'show'])->name('service.show');
    Route::get('services-request', [Service_cnt::class, 'request'])->name('service.request');
    Route::get('requested-services', [Service_cnt::class, 'requested'])->name('service.request');
    Route::get('view-services/{id}', [Service_cnt::class, 'viewService'])->name('services.view');
    Route::post('/service/toggle-status', [Service_cnt::class, 'toggleServiceStatus'])->name('service.toggleStatus');
    Route::post('/service-reviews', [Service_cnt::class, 'review'])->name('service-reviews.store');

    // Leads
    Route::get('leads', [Leads_cnt::class, 'leads'])->name('leads.index');
    Route::get('leads/{id}/details', [Leads_cnt::class, 'leadDetails'])->name('leads.details');
    Route::post('leads/store', [Leads_cnt::class, 'storeLeads'])->name('leads.store');
    Route::get('leads/{id}/edit', [Leads_cnt::class, 'editLeads'])->name('leads.edit');
    Route::put('leads/{id}', [Leads_cnt::class, 'updateLeads'])->name('leads.update');
    Route::delete('leads/{id}', [Leads_cnt::class, 'destroyLeads'])->name('leads.destroy');
    Route::get('owned-leads', [Leads_cnt::class, 'owned'])->name('leads.owned');
    Route::post('/buy-lead/{id}', [Leads_cnt::class, 'buyLead'])->name('buy.lead');
    Route::post('/lead-reviews', [Leads_cnt::class, 'review'])->name('lead-reviews.store');
    Route::put('leads-repost/{id}', [Leads_cnt::class, 'repostLeads'])->name('leads.repost');

    // Hire
    Route::get('hire', [Leads_cnt::class, 'hire'])->name('hire');

    // Reels
    Route::get('reels', [Reels_cnt::class, 'reels'])->name('reels');
    Route::post('posts', [Reels_cnt::class, 'store'])->name('posts.store');
    Route::post('post-delete', [Reels_cnt::class, 'post_delete'])->name('post.delete');
    Route::get('hashtags.suggest', [Hashtag_cnt::class, 'suggest'])->name('hashtags.suggest');

    // Route::post('/post-delete/{id}', [PostController::class, 'post_delete'])->name('post.delete');

    // People
    // Route::get('people', [People_cnt::class, 'people'])->name('people');
    Route::match(['get', 'post'], '/people', [People_cnt::class, 'people'])->name('people');

    // Update Profile
    Route::get('my-profile', [Profile_cnt::class, 'myProfile'])->name('my-profile');
    Route::get('edit-profile', [Profile_cnt::class, 'editProfile'])->name('edit-profile');
    Route::post('edit-profile/store', [Profile_cnt::class, 'store'])->name('profile.store');
    Route::put('store-profile-image', [Profile_cnt::class, 'update_profile_image'])->name('update-profile-image');
    Route::get('income-tax/view/{id}', [Profile_cnt::class, 'viewIncomeTax'])->name('view.income_tax');
    Route::post('gst-verify', [Profile_cnt::class, 'gst_verify'])->name('gst.add');    // new add saranya
    Route::post('gst-details/store', [Profile_cnt::class, 'gst_store'])->name('gst_store');
    Route::post('/check-username', [Profile_cnt::class, 'checkUsername'])->name('check.username');

    // Wallet
    Route::get('wallet', [Wallet_cnt::class, 'wallet'])->name('wallet');

    // chat card
    Route::get('chat_card', [Chat_cnt::class, 'chat'])->name('chat_card');
    Route::post('chat_msg', [Chat_cnt::class, 'chat_msg'])->name('chat_msg');
    Route::post('chat_msg_ind', [Chat_cnt::class, 'chat_msg_ind'])->name('chat_msg_ind');
    Route::post('chat_rec_send', [Chat_cnt::class, 'chat_rec_send'])->name('chat_rec_send');
    Route::post('chat_open_update', [Chat_cnt::class, 'chat_open_update'])->name('chat_open_update');
    Route::post('chat_unseen', [Chat_cnt::class, 'unseen_count'])->name('chat_unseen');

    // Cashback
    Route::get('cashback', [Cashback_cnt::class, 'cashback'])->name('cashback');

    // Settings_cnt
    Route::get('settings', [Settings_cnt::class, 'settings'])->name('settings');
    Route::post('change-password', [Settings_cnt::class, 'changePassword'])->name('password.change');

    // Highlights
    Route::get('products-highlight', [Highlights_cnt::class, 'highlight_products'])->name('products-highlight');
    Route::get('view-product-highlight/{id?}', [Highlights_cnt::class, 'view_products'])->name('view.product.highlight');
    Route::get('services-highlight', [Highlights_cnt::class, 'highlight_services'])->name('services-highlight');
    Route::get('view-service-highlight/{id?}', [Highlights_cnt::class, 'view_service'])->name('view-service-highlight');
    Route::get('jobs-highlight', [Highlights_cnt::class, 'highlight_jobs'])->name('jobs-highlight');
    Route::get('view-job-highlight/{id?}', [Highlights_cnt::class, 'view_jobs'])->name('view-job-highlight');
    Route::get('/boost-clicks/{boost_id}', [Highlights_cnt::class, 'getClicks']);

    // Premium
    Route::get('premium', [Premium_cnt::class, 'premium'])->name('premium');
    Route::post('/premium/subscribe', [Premium_cnt::class, 'subscribe'])->name('premium.subscribe');

    // Notification
    Route::get('notification', [Notify_cnt::class, 'notification'])->name('notification');

    // Bill Invoice
    // Route::get('invoice-bill/{id}', [Bill_cnt::class, 'invoice_bill'])->name('invoice-bill/{id}');
    // Listing
    Route::get('product-list-bill/{id}', [Bill_cnt::class, 'product_list_bill'])->name('product-list-bill/{id}');
    Route::get('service-list-bill/{id}', [Bill_cnt::class, 'service_list_bill'])->name('service-list-bill/{id}');
    Route::get('project-list-bill/{id}', [Bill_cnt::class, 'project_list_bill'])->name('project-list-bill/{id}');
    Route::get('lead-owned-bill/{id}', [Bill_cnt::class, 'lead_owned_bill'])->name('lead-owned-bill/{id}');
    // Highlights
    Route::get('product-click-bill/{id}', [Bill_cnt::class, 'product_click_bill'])->name('product-click-bill/{id}');
    Route::get('service-click-bill/{id}', [Bill_cnt::class, 'service_click_bill'])->name('service-click-bill/{id}');
    Route::get('job-boost-bill/{id}', [Bill_cnt::class, 'job_boost_bill'])->name('job-boost-bill/{id}');
    // Premium
    Route::get('premium-bill/{id}', [Bill_cnt::class, 'premium_bill'])->name('premium-bill/{id}');
    // Ready To Work
    Route::get('ready-to-work-bill/{id}', [Bill_cnt::class, 'readytowork_bill'])->name('ready-to-work-bill/{id}');
    // Badges
    Route::get('badges-bill/{id}', [Bill_cnt::class, 'badges_bill'])->name('badges-bill/{id}');
    // ChatBot
    Route::get('chatbot-bill/{id}', [Bill_cnt::class, 'chatbot_bill'])->name('chatbot-bill/{id}');
    // Invoices
    Route::get('invoices', [Bill_cnt::class, 'invoices'])->name('invoices');

});

// Policy
Route::get('terms-and-condition', [Policy_cnt::class, 'terms'])->name('terms-and-condition');
Route::get('privacy-policy', [Policy_cnt::class, 'privacy'])->name('privacy-policy');
Route::get('refund-and-cancellation', [Policy_cnt::class, 'refund'])->name('refund-and-cancellation');
Route::get('contact-us', [Policy_cnt::class, 'contact_us'])->name('contact-us');
Route::get('delete-my-account', [Policy_cnt::class, 'delete_my_account'])->name('delete-my-account');

// Invoice
Route::post('order-po', [Bill_cnt::class, 'purchase_order'])->name('order-po');
Route::post('vendor-inv', [Bill_cnt::class, 'vendor_invoice'])->name('vendor-inv');
Route::post('customer-inv', [Bill_cnt::class, 'customer_invoice'])->name('customer-inv');
Route::post('commission-inv', [Bill_cnt::class, 'commission_invoice'])->name('commission-inv');

// ------------------------------------------- ADMIN PANEL ---------------------------------------------------------
Route::get('/admin', [Register_cnt::class, 'admin_index'])->name('admin');
Route::post('/admin_login', [Admin::class, 'login'])->name('admin-login');
Route::post('admin_logout', [Admin::class, 'admin_logout'])->name('admin_logout');

Route::middleware([CheckAdmin::class])->group(function () {

    // Admin Dashboard
    Route::get('dashboard_admin', [Admin::class, 'dashboard'])->name('dashboard_admin');

    // Users
    Route::get('user_vendor', [Admin::class, 'user_vendor'])->name('user_vendor');
    Route::get('user_contractor', [Admin::class, 'user_contractor'])->name('user_contractor');
    Route::get('user_consultant', [Admin::class, 'user_consultant'])->name('user_consultant');
    Route::get('user_consumer', [Admin::class, 'user_consumer'])->name('user_consumer');
    Route::get('user_professional', [Admin::class, 'user_professional'])->name('user_professional');
    Route::get('user_detail/{id}', [Admin::class, 'show_user'])->name('user_detail/{id}');

    // Products
    Route::get('product_list', [Admin::class, 'product_list'])->name('product_list');
    Route::post('/product/status/{id}', [Admin::class, 'productStatus'])->name('product.changeStatus');
    Route::get('/product/{id}', [Admin::class, 'show_product'])->name('product_detail');

    // Commissions
    Route::get('commission_list', [Admin::class, 'commission_list'])->name('commission_list');
    Route::post('add-commission', [Admin::class, 'addCommission'])->name('add-commission');

    // Leads
    Route::get('leads_list', [Admin::class, 'leads_list'])->name('leads_list');
    Route::post('/lead/status/{id}', [Admin::class, 'leadsStatus'])->name('leads.changeStatus');
    Route::get('/lead/{id}', [Admin::class, 'show_lead'])->name('lead_detail');

    // Jobs
    Route::get('/job_list', [Admin::class, 'job_list'])->name('job_list');
    Route::post('/job/status/{id}', [Admin::class, 'jobStatus'])->name('job.changeStatus');
    Route::get('/job/{id}', [Admin::class, 'show_job'])->name('job_detail');

    // Dropdowns
    Route::get('dropdown_list', [DropdownController::class, 'dropdown'])->name('dropdown_list');
    Route::post('/dropdown-list/store', [DropdownController::class, 'storelist'])->name('dropdownlist.store');
    Route::post('/dropdown-list-view', [DropdownController::class, 'showList'])->name('dropdown.show');
    Route::put('/dropdown-list/update/{id}', [DropdownController::class, 'update'])->name('dropdown.update');

    // Services
    Route::get('/service_list', [Admin::class, 'service'])->name('service_list');
    Route::post('/service/status/{id}', [Admin::class, 'serviceStatus'])->name('service.changeStatus');
    Route::get('/service/{id}', [Admin::class, 'show_service'])->name('service_detail');

    // Orders
    Route::get('orders_list', [Admin::class, 'orders_list'])->name('orders_list');
    Route::get('/order/{id}', [Admin::class, 'show_order'])->name('orders_detail');
    Route::get('orders_settlement', [Admin::class, 'settlement_list'])->name('orders_settlement');
    Route::post('orders-settlement-action', [Admin::class, 'settlement_action'])->name('orders_settlement_action');

    // Charges
    Route::get('charges_list', [Admin::class, 'charges_list'])->name('charges_list');
    Route::post('add-charges', [Admin::class, 'addCharges'])->name('add-charges');

    // Cashback
    Route::get('cashback_list', [Admin::class, 'cashback_list'])->name('cashback_list');

    // Premium
    Route::get('premium_list', [Admin::class, 'premium_list'])->name('premium_list');
    Route::post('premium/store', [Admin::class, 'store'])->name('premium.store');
    Route::post('premium/update/{id}', [Admin::class, 'update'])->name('premium.update');
    Route::get('premium_users', [Admin::class, 'premium_users_list'])->name('premium_users');

    // Refresh
    Route::post('/admin/jobs/refresh-highlights', [Admin::class, 'refreshJobHighlights'])->name('admin.jobs.refreshHighlights');
    Route::post('/admin/redytowork/refresh-highlights', [Admin::class, 'refreshreadytowork'])->name('admin.refreshreadytowork');

    // Highlights
    Route::get('highlight_products_list', [Admin::class, 'highlight_products'])->name('highlight_products_list');
    Route::get('highlight_services_list', [Admin::class, 'highlight_services'])->name('highlight_services_list');
    Route::get('highlight_jobs_list', [Admin::class, 'highlight_jobs'])->name('highlight_jobs_list');

    // Insights
    Route::get('insight_list', [Admin::class, 'insight_list'])->name('insight_list');
    Route::get('insight_detail/{id}', [Admin::class, 'show_insight'])->name('insight_detail/{id}');
    Route::post('deactivate-post', [Admin::class, 'deactivatePost'])->name('deactivatePost');
    Route::post('activate-post', [Admin::class, 'activatePost'])->name('activatePost');
    Route::post('deactivate-user', [Admin::class, 'deactivateUser'])->name('deactivateUser');
    Route::post('activate-user', [Admin::class, 'activateUser'])->name('activateUser');

    // Projects
    Route::get('/project/{id}', [Admin::class, 'show_project'])->name('project_detail');

    // Reports
    Route::get('report_products', [Admin::class, 'report_products'])->name('report_products');
    Route::get('report_services', [Admin::class, 'report_services'])->name('report_services');
    Route::get('report_jobs', [Admin::class, 'report_jobs'])->name('report_jobs');
    Route::get('report_projects', [Admin::class, 'report_projects'])->name('report_projects');
    Route::get('report_leads', [Admin::class, 'report_leads'])->name('report_leads');
    Route::get('report_premium', [Admin::class, 'report_premium'])->name('report_premium');
    Route::get('report_readytowork', [Admin::class, 'report_readytowork'])->name('report_readytowork');
    Route::get('report_badges', [Admin::class, 'report_badges'])->name('report_badges');
    Route::get('report_chatbot', [Admin::class, 'report_chatbot'])->name('report_chatbot');

    // Franchise
    Route::get('franchise_dashboard', [Admin::class, 'franchise_dashboard'])->name('franchise_dashboard');
    Route::get('franchise_list', [Admin::class, 'franchise_list'])->name('franchise_list');
    Route::get('franchise_detail', [Admin::class, 'show_franchise'])->name('franchise_detail');
    Route::get('franchise_settlement', [Admin::class, 'franchise_settlement'])->name('franchise_settlement');
    Route::get('franchise_users', [Admin::class, 'franchise_users'])->name('franchise_users');
    Route::get('franchise_amt/{user_id}/{frn_id}/{type}', [Admin::class, 'franchise_amt'])->name('franchise_amt');
    Route::match(['get', 'post'], '/franchise_amount_settle', [Admin::class, 'franchise_amount_settle'])->name('franchise_amount_settle');
    Route::post('franchise_store', [Admin::class, 'franchise_amount_store'])->name('franchise_amount_store');

    // Route::post('franchise_amount_settle', [Admin::class, 'franchise_amount_settle'])->name('franchise_amount_settle');
});
