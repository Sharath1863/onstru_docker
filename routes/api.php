<?php

use App\Http\Controllers\api\Franchise_api;
use App\Http\Controllers\api\Job_api;
use App\Http\Controllers\api\Lead_api;
use App\Http\Controllers\api\Post_api;
use App\Http\Controllers\api\Product_api;
use App\Http\Controllers\api\Project_api;
use App\Http\Controllers\api\Ready_work_api;
use App\Http\Controllers\api\Register_api;
use App\Http\Controllers\api\Service_api;
use App\Http\Controllers\Bill_cnt;
use App\Http\Controllers\Cashback_cnt;
use App\Http\Controllers\Chat_cnt;
use App\Http\COntrollers\Explore_cnt;
use App\Http\Controllers\Hashtag_cnt;
use App\Http\Controllers\Highlights_cnt;
use App\Http\Controllers\Home_cnt;
use App\Http\Controllers\Job_cnt;
use App\Http\Controllers\Leads_cnt;
use App\Http\Controllers\Orders_cnt;
use App\Http\Controllers\People_cnt;
use App\Http\Controllers\Premium_cnt;
use App\Http\Controllers\Product_cnt;
use App\Http\Controllers\Profile_cnt;
use App\Http\Controllers\Project_cnt;
use App\Http\Controllers\Reels_cnt;
use App\Http\Controllers\Report_cnt;
use App\Http\Controllers\Service_cnt;
use App\Http\Controllers\Wallet_cnt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// boardcast test

// Broadcast::routes([
//     'prefix' => 'api',
//     'middleware' => ['auth:sanctum'],
// ]);

// Broadcast::routes(['middleware' => ['auth:sanctum']]);

// Route::get('check', function () {
//     return response()->json(['message' => 'API is working']);
// });

// upate popup api
Route::post('update_popup', [Register_api::class, 'update_popup']);

// Route::middleware('throttle:60,1')->group(  //sa-new
//     function () {                   //sa-new
Route::post('/register', [Register_api::class, 'register']);
Route::post('/otp_verify', [Register_api::class, 'otp_verify']);
Route::post('/resend_otp', [Register_api::class, 'resend_otp']);
Route::post('/contact_exist', [Register_api::class, 'contact_exist']);
Route::post('/dropdown_list_type', [Register_api::class, 'dropdown_list']); // sa-new
Route::post('/location_list', [Register_api::class, 'location_list']);  // sa-new
Route::post('/search-user', [Register_api::class, 'searchUser']); // sa-new
Route::post('/search-mobile', [Register_api::class, 'searchMobile']);  // sa-new
Route::post('/otp_status_update', [Register_api::class, 'otp_status_update']);  // sa-new
Route::post('forgot_password', [Register_api::class, 'forgot_password']);
Route::post('forgot_pswd_update', [Register_api::class, 'forgot_pswd_update']);
Route::post('franchise_register', [Franchise_api::class, 'franchise_register']);
Route::post('franchise_login', [Franchise_api::class, 'franchise_login']);
Route::post('/franchise_otp_verify', [Franchise_api::class, 'franchise_otp_verify']);
Route::post('/franchise_resend_otp', [Franchise_api::class, 'franchise_resend_otp']);
Route::post('/franchise_contact', [Franchise_api::class, 'franchise_contact']);  // sa-new
Route::post('/franchise_otp_update', [Franchise_api::class, 'franchise_otp_update']);
Route::post('/franchise_transaction', [Franchise_api::class, 'franchise_transaction']);
Route::post('frn_forgot_password', [Franchise_api::class, 'frn_forgot_password']);
Route::post('frn_pswd_update', [Franchise_api::class, 'frn_pswd_update']);
Route::post('frn_update_popup', [Franchise_api::class, 'frn_update_popup']);

// }  //sa-new
// );  //sa-new

Route::post('/login', [Register_api::class, 'login'])->middleware('throttle:60,1');

Route::post('get_video_details', [Register_api::class, 'get_video_details']);
Route::post('create_video', [Register_api::class, 'create_video']);

// third party token exceed

Route::post('token_exceed', [Chat_cnt::class, 'token_exceed']);
Route::post('update_token_count', [Chat_cnt::class, 'update_token_count']);

// home screen apis

// Route::post('/comment_api', [Home_cnt::class, 'comment_api']);
// Route::post('/share_api', [Home_cnt::class, 'share_api']);
// Route::post('/follow_api', [Home_cnt::class, 'follow_api']);

// Route::post('/save_post_api', [Home_cnt::class, 'save_post_api']);

// Route::get('search_product', [Post_api::class, 'searchProducts']);

// Route::post('view-service-highlight', [Highlights_cnt::class, 'view_service']);

Route::middleware('auth:sanctum')->group(function () {

    // post API  for create post
    Route::post('create_post', [Reels_cnt::class, 'store']);

    // notification list
    Route::post('notification_list', [Register_api::class, 'notification_list']);

    // hashtag
    Route::post('hashtag_list', [Post_api::class, 'hashtag_search']);

    // home screen feed
    Route::post('home_feed', [Post_api::class, 'post_feed']);
    Route::post('post-delete', [Reels_cnt::class, 'post_delete']);

    // reels screen

    Route::post('reel_feed', [Post_api::class, 'reel_feed']);
    Route::post('ind_post', [Post_api::class, 'ind_post']);
    Route::post('explore_reel', [Post_api::class, 'explore_reel']);

    Route::post('/like_api', [Home_cnt::class, 'toggle_like']);
    Route::post('/save_post', [Home_cnt::class, 'toggle_save']);
    Route::post('/follow', [Home_cnt::class, 'follow']);
    Route::post('/like_list', [Home_cnt::class, 'getLikesList']);
    // Route::post('/comment_list', [Home_cnt::class, 'getCommentList']);
    Route::post('/store_comment', [Home_cnt::class, 'storeComment']);

    Route::post('profile_card', [Post_api::class, 'profile_card']);
    Route::post('profile_post_reel', [Post_api::class, 'profile_post_reel']);

    // comment list
    Route::post('comment_list', [Post_api::class, 'comment_list']);
    Route::post('share_list', [Post_api::class, 'share_list']);
    Route::post('comment_delete', [Home_cnt::class, 'toggle_report']);

    Route::match(['get', 'post'], '/people', [People_cnt::class, 'people'])->name('people');

    // premium
    Route::post('premium', [Post_api::class, 'premium']);
    Route::post('premium_subscribe', [Premium_cnt::class, 'subscribe']);

    Route::match(['get', 'post'], 'saved_posts', [Post_api::class, 'saved_posts']);
    Route::match(['get', 'post'], 'liked_posts', [Post_api::class, 'liked_posts']);
    Route::match(['get', 'post'], 'services-highlight', [Highlights_cnt::class, 'highlight_services'])->name('services-highlight');
    Route::match(['get', 'post'], 'jobs-highlight', [Highlights_cnt::class, 'highlight_jobs'])->name('jobs-highlight');
    Route::match(['get', 'post'], 'products-highlight', [Highlights_cnt::class, 'highlight_products']);

    // boost clicks increase

    Route::post('boost_clicks', [Highlights_cnt::class, 'boost_clicks']);

    Route::post('job_highlight_users', [Highlights_cnt::class, 'job_highlight_users']);

    Route::post('view-job-highlight', [Highlights_cnt::class, 'view_jobs']);
    Route::match(['get', 'post'], 'view-service-highlight/{id?}', [Highlights_cnt::class, 'view_service']);
    Route::match(['get', 'post'], 'view-products-highlight', [Highlights_cnt::class, 'view_products']);

    // jobs details
    Route::match(['get', 'post'], 'job-details/{id?}', [Job_cnt::class, 'job_details']);

    // requested service view screen
    Route::match(['get', 'post'], 'individual-service/{id?}', [Service_cnt::class, 'show']);
    Route::post('service-review-store', [Service_cnt::class, 'review']);

    // Wallet
    Route::get('wallet', [Wallet_cnt::class, 'wallet'])->name('wallet');
    // Route::post('wallet_transaction', [Wallet_cnt::class, 'wallet_transaction'])->name('wallet_transaction');
    Route::post('wallet_insert', [Wallet_cnt::class, 'wallet_insert'])->name('wallet_insert');

    // Cashback
    Route::get('cashback', [Cashback_cnt::class, 'cashback'])->name('cashback');
    Route::post('buy_badge', [Home_cnt::class, 'buyBadge']);
    Route::post('badge_cost', [Post_api::class, 'badge_cost']);

    // report
    Route::post('user_report', [Report_cnt::class, 'user_report']);
    Route::post('post_report', [Report_cnt::class, 'post_report']);

    // follow & follwing list
    Route::post('follow_list', [Post_api::class, 'follow_list']);

    // product api
    Route::post('add_product', [Product_cnt::class, 'store']);
    Route::post('products', [Product_cnt::class, 'products']);
    Route::post('add_to_cart', [Product_cnt::class, 'cart_store']);
    Route::post('remove_from_cart', [Product_cnt::class, 'removeFromCart']);
    Route::post('wish_list', [Product_cnt::class, 'wishlist']);
    Route::post('wish_list_add', [Product_cnt::class, 'toggleSavedProduct']);
    Route::post('product_created_list', [Product_api::class, 'product_created_list']);
    Route::post('product_category_list', [Product_cnt::class, 'product_category_list']);
    Route::post('individual-product/{id?}', [Product_cnt::class, 'individual_product']);
    Route::post('boost_product', [Product_cnt::class, 'boost_product']);
    Route::post('product_status', [Product_cnt::class, 'toggleProductStatus']);
    Route::post('product_price_update_api', [Product_cnt::class, 'updatePrice']);
    Route::post('get_commission_api', [Product_cnt::class, 'getCommission']);

    // invoices
    Route::post('product-list-bill_api/{id}', [Bill_cnt::class, 'product_list_bill']);
    Route::post('service-list-bill_api/{id}', [Bill_cnt::class, 'service_list_bill']);
    Route::post('project-list-bill_api/{id}', [Bill_cnt::class, 'project_list_bill']);
    Route::post('lead-owned-bill_api/{id}', [Bill_cnt::class, 'lead_owned_bill']);
    Route::post('invoices', [Bill_cnt::class, 'invoices'])->name('invoices');
    // Highlights
    Route::post('product-click-bill_api/{id}', [Bill_cnt::class, 'product_click_bill']);
    Route::post('service-click-bill_api/{id}', [Bill_cnt::class, 'service_click_bill']);
    Route::post('job-boost-bill_api/{id}', [Bill_cnt::class, 'job_boost_bill']);
    // Premium
    Route::post('premium-bill_api/{id}', [Bill_cnt::class, 'premium_bill']);
    // Ready To Work
    Route::post('ready-to-work-bill_api/{id}', [Bill_cnt::class, 'readytowork_bill']);
    // Badges
    Route::post('badges-bill_api/{id}', [Bill_cnt::class, 'badges_bill']);
    // ChatBot
    Route::post('chatbot-bill_api/{id}', [Bill_cnt::class, 'chatbot_bill']);
    Route::post('chatbot', [Chat_cnt::class, 'chatbot'])->name('chatbot');
    Route::post('chatbot-subscribe', [Chat_cnt::class, 'subscribe'])->name('chatbot.subscribe');

    Route::post('purchase_order-bill_api', [Bill_cnt::class, 'purchase_order']);
    Route::post('vendor_invoice-bill_api', [Bill_cnt::class, 'vendor_invoice']);
    Route::post('customer_invoice-bill_api', [Bill_cnt::class, 'customer_invoice']);
    Route::post('commission_invoice-bill_api', [Bill_cnt::class, 'commission_invoice']);

    // cart api

    Route::post('cart', [Product_api::class, 'cart_api']);
    Route::post('updateQuantity_api', [Product_api::class, 'updateQuantity_api']);
    Route::post('cart_save_for_later', [Product_api::class, 'cart_savelater_api']);
    Route::post('cart_toggle', [Product_api::class, 'cart_toggle_api']);
    Route::post('cart_item_remove', [Product_api::class, 'removeFromCart_api']);
    Route::post('store_address', [Product_cnt::class, 'address_store']);
    Route::post('gst_checkout_verify', [Product_cnt::class, 'gst_verify']);
    Route::post('previous_address_api', [Product_api::class, 'previous_address']);
    Route::post('place_order_check_api', [Product_api::class, 'place_order_check']);
    Route::post('place_order_api', [Product_api::class, 'place_order']);

    // order api
    Route::post('order_list', [Product_api::class, 'order_list_api']);
    Route::post('buyer_product_list', [Product_api::class, 'buyer_product_list_api']);
    Route::post('order_products', [Product_api::class, 'order_products_api']);
    Route::post('order_products_list_api', [Product_api::class, 'order_products_list_api']);
    Route::post('vendor_pending_orders_api', [Product_api::class, 'vendor_pending_orders_api']);
    Route::post('vendor_processing_orders_api', [Product_api::class, 'vendor_processing_orders_api']);
    Route::post('vendor_shipped_orders_api', [Product_api::class, 'vendor_shipped_orders_api']);
    Route::post('vendor_delivered_orders_api', [Product_api::class, 'vendor_delivered_orders_api']);
    Route::post('order_tracking_update', [Orders_cnt::class, 'orderTrackingUpdate']);
    Route::post('order_accept_api', [Product_api::class, 'order_accept_api']);
    Route::post('tracking_list_api', [Product_api::class, 'tracking_list_api']);
    Route::post('product_tracking_api', [Product_api::class, 'product_tracking']);
    Route::post('order_otp_update_api', [Orders_cnt::class, 'order_otp_update']);
    Route::post('track_api', [Product_api::class, 'track_api']);

    // Route::post('edit_product/{id?}', [Product_cnt::class, 'edit']);

    // product distance caluculate

    Route::post('get_distance', [Product_cnt::class, 'getDrivingDistance']);

    // logout

    Route::post('/logout', [Register_api::class, 'logout']);

    // share the POST,PROFILE
    Route::post('share_content', [Chat_cnt::class, 'chat_msg_arr']);

    // explore screen

    // // Explore
    // Route::post('explore', [Explore_cnt::class, 'explore'])->name('explore');

    // chat API

    Route::post('chat_list', [Post_api::class, 'chat_list']);
    Route::post('chat_ind_list', [Post_api::class, 'chat_ind_list']);
    Route::post('chat_msg_ind', [Chat_cnt::class, 'chat_msg_ind']);
    Route::post('chat_unseen', [Chat_cnt::class, 'unseen_count']);
    Route::post('chat_open_update', [Chat_cnt::class, 'chat_open_update']);

    // hub API FOR Store

    Route::post('add_hub', [Product_cnt::class, 'hubStore']);

    // leads API

    // Route::get('/user-details', [App\Http\Controllers\api\UserDetailController::class, 'index']);
    // Route::post('/user-details', [App\Http\Controllers\api\UserDetailController::class, 'store']);
    // Route::put('/user-details/{id}', [App\Http\Controllers\api\UserDetailController::class, 'update']);
    // Route::delete('/user-details/{id}', [App\Http\Controllers\api\UserDetailController::class, 'destroy']);

    //   //sa-commentout

    // Route::post('/jobs/{id?}/toggle-save', [Job_cnt::class, 'toggleSave']);

    // service request

    // update service

    // lead request

    // Route::post('add_lead_request', [Lead_api::class, 'add_lead_request']);
    // Route::post('lead_profile_api', [Service_api::class, 'lead_profile_api']);
    // Route::post('request_lead_api', [Service_api::class, 'request_lead_api']);
    // Route::post('lead_request_list_api', [Service_api::class, 'lead_request_list_api']);

    // Route::post('ready_to_work_profile_api', [Service_api::class, 'ready_to_work_profile_api']);

    // ----------------------------------------saraniya BOUNDARY------------------------------

    // gst
    Route::post('check_balance', [Register_api::class, 'check_balance']); // sa-new

    Route::post('gst-details/store', [Profile_cnt::class, 'gst_store']);

    // service api creator list
    Route::post('/services_type_list', [Register_api::class, 'services_type_list']);
    Route::post('services', [Service_cnt::class, 'storeService']);
    Route::post('get_created_services', [Service_api::class, 'get_created_services']);
    Route::post('service_profile_api', [Service_api::class, 'service_profile_api']);
    Route::post('services/highlight', [Service_cnt::class, 'highlightService']);
    Route::post('services_review_list', [Service_api::class, 'services_review_list']);
    Route::post('services_request_list', [Service_api::class, 'services_request_list']);
    Route::post('services/edit/{id?}', [Service_cnt::class, 'getService']);
    Route::post('services_update', [Service_api::class, 'service_update']);
    Route::post('services_status_update', [Service_api::class, 'services_status_update']);
    Route::match(['get', 'post'], 'services-request', [Service_cnt::class, 'request']);  // admin recieve request
    // Route::get('services-request', [Service_cnt::class, 'request']);    //contractor view services
    // Route::get('services', [Service_cnt::class, 'index']);
    // consumer
    Route::match(['get', 'post'], 'view_services_list', [Service_cnt::class, 'index']);
    Route::match(['get', 'post'], 'view-service-profile/{id?}', [Service_cnt::class, 'show']);
    Route::post('request_service_details', [Service_api::class, 'request_service_details']);
    Route::post('service/request/store', [Service_cnt::class, 'storeServiceRequest']);
    Route::match(['get', 'post'], 'requested-services', [Service_cnt::class, 'requested']);  // admin request the service
    Route::post('/service-reviews', [Service_cnt::class, 'review']);
    // Route::get('requested-services', [Service_cnt::class, 'requested']);
    // Route::get('services', [Service_cnt::class, 'index']);
    Route::post('add_service_request', [Service_api::class, 'add_service_request']);
    Route::post('service_list_api', [Service_api::class, 'service_list_api']);
    Route::post('request_service_api', [Service_api::class, 'request_service_api']);
    Route::post('service_request_list_api', [Service_api::class, 'service_request_list_api']);

    // jobs api list creator
    Route::post('charges', [Job_api::class, 'charges']);
    Route::post('/jobs_category_list', [Register_api::class, 'jobs_category_list']);
    Route::post('jobs/store', [Job_cnt::class, 'store']);
    Route::post('jobcreated_list_api', [Job_api::class, 'jobcreated_list_api']);
    Route::post('job_profile_api', [Job_api::class, 'job_profile_api']);
    Route::post('job-edit/{id?}', [Job_cnt::class, 'job_edit']);
    Route::post('update-job/{id?}', [Job_cnt::class, 'update_job']);
    Route::post('job_applicant_details', [Job_api::class, 'job_applicant_details']);
    Route::post('job_boost_store', [Job_api::class, 'job_boost_store']);

    // job consumer view
    Route::post('job_apply/{id?}', [Job_cnt::class, 'apply']);
    Route::post('job_list_api', [Job_api::class, 'job_list_api']);      // date time change
    Route::post('view_job_profile', [Job_api::class, 'view_job_profile']);      // to check
    Route::post('/jobs/toggle-save/{id?}', [Job_cnt::class, 'toggleSave']);
    Route::post('jobs_status_update', [Job_api::class, 'jobs_status_update']); // jobs/toggle-status
    Route::post('get_sublocation', [Job_api::class, 'get_sublocation']);

    // To check
    Route::post('create_job', [Job_cnt::class, 'store']);
    Route::post('saved_applied_jobs_api', [Job_cnt::class, 'saved_applied_jobs_api']);
    // Route::get('jobcreated_details/{id}', [Job_cnt::class, 'applied_list']);
    // Route::post('job_list_api', [Job_cnt::class, 'job_list_api']);        //sa-commentout

    // creator lead list api
    Route::post('add_leads_api', [Leads_cnt::class, 'storeLeads']);
    Route::post('get_created_leads', [Lead_api::class, 'get_created_leads']);
    Route::match(['get', 'post'], 'leads/edit/{id?}', [Leads_cnt::class, 'editLeads']);
    Route::match(['get', 'post'], 'leads/{id?}', [Leads_cnt::class, 'updateLeads']);
    Route::post('lead_profile_api', [Lead_api::class, 'lead_profile_api']);
    Route::post('repost_lead_fetch', [Lead_api::class, 'repost_lead_fetch']);
    Route::match(['get', 'post'], 'leads-repost/{id?}', [Leads_cnt::class, 'repostLeads']);

    // consumer lead list api
    Route::match(['get', 'post'], 'view_leads_list', [Leads_cnt::class, 'leads']);
    Route::match(['get', 'post'], 'view_lead_profile', [Lead_api::class, 'view_lead_profile']);
    Route::post('/buy-lead/{id?}', [Leads_cnt::class, 'buyLead']);
    Route::match(['get', 'post'], 'owned-leads', [Leads_cnt::class, 'owned']);
    Route::match(['get', 'post'], 'owned_lead_profile', [Lead_api::class, 'owned_lead_profile']);
    Route::post('/lead-reviews', [Leads_cnt::class, 'review']);
    // Route::match(['get', 'post'], 'leads-repost/{id?}', [Leads_cnt::class, 'repostLeads']);
    // Route::post('lead_list_api', [Lead_api::class, 'lead_list_api']);
    // Route::post('edit_leads_api', [Leads_cnt::class, 'updateLeads']);
    // Route::post('lead_view', [Lead_api::class, 'lead_view']);

    // project API  To-Check
    // Route::post('projects/store', [Project_cnt::class, 'storeProject']); // sa-new 13-09-2025
    Route::post('project_count', [Project_cnt::class, 'project_count']);
    Route::post('add_project_api', [Project_cnt::class, 'storeProject']);
    Route::post('project_created_list', [Project_api::class, 'project_created_list']);
    Route::post('project_profile_api', [Project_api::class, 'project_profile_api']);
    Route::post('projects/edit', [Project_api::class, 'getProject']);
    Route::post('projects_update', [Project_api::class, 'projects_update']);

    // products  To-Check
    Route::post('product_list_api', [Service_api::class, 'product_list_api']);
    Route::post('product_profile_api', [Product_api::class, 'product_profile_api']);

    Route::post('product_created_by', [Product_api::class, 'product_created_by']);

    // ready to works creator
    Route::post('readytowork_charge', [Ready_work_api::class, 'readytowork_charge']);
    Route::post('ready-to-work', [Profile_cnt::class, 'storeReadyToWork']);
    Route::match(['get', 'post'], 'readywork/edit', [Ready_work_api::class, 'getreadywork']);
    Route::post('ready-to-work-update/{id?}', [profile_cnt::class, 'updateReadyToWork']);
    Route::match(['get', 'post'], 'ready-toggle-status', [Profile_cnt::class, 'toggleReadyToWorkStatus']);
    // ready to work view
    Route::match(['get', 'post'], 'hire', [Leads_cnt::class, 'hire']);

    // Route::get('profile', [Profile_cnt::class, 'profile']);doubt
    Route::post('add_ready_to_work', [Ready_work_api::class, 'add_ready_to_work']);
    Route::post('ready_to_work_list_api', [Ready_work_api::class, 'ready_to_work_list_api']);

    // user profile data
    Route::match(['get', 'post'], 'my-profile', [Profile_cnt::class, 'myProfile']);
    Route::match(['get', 'post'], 'edit-profile', [Profile_cnt::class, 'editProfile']);
    Route::post('edit-profile/store', [Profile_cnt::class, 'store']);
    Route::match(['get', 'post'], 'store-profile-image', [Profile_cnt::class, 'update_profile_image']);

    // change password
    Route::post('get_old_password', [Register_api::class, 'get_old_password']);
    Route::post('change_password', [Register_api::class, 'change_password']);

    // hashtag
    Route::post('hash_tags', [Hashtag_cnt::class, 'hash_tags']);
});

Route::middleware('auth:franchise_api')->group(function () {
    Route::post('/franchise_home', [Franchise_api::class, 'franchise_home']);
    Route::post('/franchise_recharge', [Franchise_api::class, 'franchise_recharge']);
    Route::post('/follow_users_list', [Franchise_api::class, 'follow_users_list']);
    Route::post('/franchise_logout', [Franchise_api::class, 'franchise_logout']);
    Route::post('/franchise_commision', [Franchise_api::class, 'franchise_commision']);
    Route::post('/choose_plan', [Franchise_api::class, 'choose_plan']);
    Route::post('/franchise_settlemenet', [Franchise_api::class, 'franchise_settlemenet']);
    Route::post('franchise_old_password', [Franchise_api::class, 'franchise_old_password']);
    Route::post('franchise_commision_list', [Franchise_api::class, 'franchise_commision_list']);

});
