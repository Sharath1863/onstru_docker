<?php

namespace App\Http\Controllers;

use App\Events\PrivateMessageSent;
use App\Models\Charge;
use App\Models\Chat;
use App\Models\Chat_bot;
use App\Models\Follow;
use App\Models\GstDetails;
use App\Models\Jobs;
use App\Models\Notification;
use App\Models\Posts;
use App\Models\Products;
use App\Models\Service;
use App\Models\UserDetail;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Chat_cnt extends Controller
{
    public function chat()
    {
        $authId = Auth::id();
        $latestMessages = DB::table('chat as c1')
            ->select('c1.*')
            ->where(function ($q) use ($authId) {
                $q->where('c1.sender', $authId)
                    ->orWhere('c1.receiver', $authId);
            })
            ->whereRaw('c1.id = (
        SELECT c2.id FROM chat c2
        WHERE (
            (c2.sender = c1.sender AND c2.receiver = c1.receiver)
            OR
            (c2.sender = c1.receiver AND c2.receiver = c1.sender)
        )
        ORDER BY c2.created_at DESC
        LIMIT 1
             )')
            ->orderBy('c1.created_at', 'desc')
            ->get();

        $userIds = $latestMessages->map(function ($chat) use ($authId) {
            return $chat->sender == $authId ? $chat->receiver : $chat->sender;
        })->unique()->values();

        $users = DB::table('user_detail')
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id');

        $unseenCounts = DB::table('chat')
            ->select('sender', 'receiver', DB::raw('COUNT(*) as unseen_count'))
            ->where('seen', 0)
            ->where('receiver', $authId)
            ->groupBy('sender', 'receiver')
            ->get()
            ->keyBy('sender');

        $latestMessages = $latestMessages->map(function ($chat) use ($authId, $users, $unseenCounts) {
            $userId = $chat->sender == $authId ? $chat->receiver : $chat->sender;
            $user = $users[$userId];

            $unseen = 0;
            if ($chat->receiver == $authId) {
                $unseen = $unseenCounts[$userId]->unseen_count ?? 0;
            }

            // check the user last update based on that we will show online or offline
            $user_active = UserDetail::where('id', $userId)->first();
            $lastUpdate = Carbon::parse($user_active->updated_at);
            $now = Carbon::now();
            $diffInMinutes = round($lastUpdate->diffInMinutes($now));

            $last_time = $diffInMinutes <= 3 ? true : false;

            return (object) [
                'chat_id' => $chat->id,
                'message' => $chat->message,
                'created_at' => $chat->created_at,
                'user_id' => $user->id,
                'bio_name' => $user->name,
                'user_name' => $user->user_name,
                'user_email' => $user->email,
                'profile_img' => $user->profile_img,
                'unseen' => $unseen,
                'type' => $chat->type,
                'badge' => $user?->badge,
                'as_a' => $user?->as_a,
                'is_online' => ($last_time == false) ? $user_active->updated_at->diffForHumans() : 'Online', // online if last active within 3 minutes
            ];
        });

        $html = '';

        if ($latestMessages->count() > 0) {
            foreach ($latestMessages as $chat) {

                if ($chat->type && in_array($chat->type, ['job', 'product', 'service', 'post', 'profile'])) {
                    $parts = explode('|', $chat->message);
                    $messages = $parts[0];
                } else {
                    $messages = $chat->message;
                }

                $lastTime = \Carbon\Carbon::parse($chat->created_at)->diffForHumans();
                $image = $chat->profile_img
                    ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.($chat->profile_img)
                    : asset('assets/images/Avatar.png');
                $badge = $chat->badge ? asset('assets/images/Badge_'.$chat->badge.'.png') : asset('assets/images/Badge_0.png');

                $html .= '
                    <div class="chat-user-card filter-chats" 
                        data-id="'.e($chat->user_id).'" 
                        data-name="'.e($chat->user_name).'"
                        data-image="'.e($chat->profile_img).'"
                        data-badge="'.e($chat->badge).'"
                        data-role="'.e($chat->as_a ?? 'Consumer').'">
                        
                        <div class="chat-card-grid">
                            <div class="d-flex align-items-center justify-content-start column-gap-2">
                                <div class="avatar-div-30 position-relative">
                                    <img src="'.$image.'" class="avatar-30">
                                    <img src="'.$badge.'" class="badge-30" alt="">
                                    <span class="dotOnline '.(($chat->is_online == 'Online') ? 'online' : '').'"></span>
                                </div>
                                <div class="chat-user'.($chat->unseen ? ' unseen' : '').'">
                                    <h5 class="mb-1">'.e($chat->user_name).'</h5>
                                    <h6 class="mb-0 chat-long">'.nl2br($messages).'</h6>
                                </div>
                            </div>
                            <div class="chat-user ms-auto">
                                <h6 class="mb-1 ms-auto unseen-count'.($chat->unseen ? ' d-flex' : 'd-none').'">'.($chat->unseen > 0 ? $chat->unseen : '').'</h6>
                                <h6 class="mb-0">'.$lastTime.'</h6>
                            </div>
                        </div>
                        <hr class="chat-hr">
                    </div>
                ';
            }
        } else {
            $noChat = asset('assets/images/Empty/NoChats.png');
            $html .= '';
        }

        // Followers who are not in chats
        $followersNotInChats = DB::table('follows as f')
            ->join('user_detail as u', 'u.id', '=', 'f.following_id')
            ->where('f.follower_id', $authId)
            ->whereNotIn('u.id', $userIds) // exclude already in chats
            ->get();

        if ($followersNotInChats->count() > 0) {
            foreach ($followersNotInChats as $follower) {
                $image = $follower->profile_img
                    ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.($follower->profile_img)
                    : asset('assets/images/Avatar.png');
                $badge = $follower->badge ? asset('assets/images/Badge_'.$follower->badge.'.png') : asset('assets/images/Badge_0.png');

                $html .= '
                    <div class="chat-user-card filter-chats new-chat"
                        data-id="'.e($follower->id).'"
                        data-name="'.e($follower->user_name).'"
                        data-image="'.e($follower->profile_img).'"
                        data-badge="'.e($follower->badge).'"
                        data-role="'.e($follower->as_a ?? 'Consumer').'">

                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center justify-content-start column-gap-2">
                                <div class="avatar-div-30 position-relative">
                                    <img src="'.$image.'" class="avatar-30">
                                    <img src="'.$badge.'" class="badge-30" alt="">
                                    <span class="dotOnline"></span>
                                </div>
                                <div class="chat-user">
                                    <h5 class="mb-1">'.e($follower->user_name).'</h5>
                                    <h6 class="mb-0 text-muted">Start a new chat!❤️</h6>
                                </div>
                            </div>
                        </div>
                        <hr class="chat-hr">
                    </div>
                ';
            }
        } else {
            $noChat = asset('assets/images/Empty/NoChats.png');
            $html .= '';
        }

        return response()->json(['html' => $html]);
    }

    public function chat_msg(Request $req)
    {
        $receiverId = $req->rec_id;
        if (! $receiverId) {
            return response()->json(['html' => '', 'msg_id' => null]);
        }
        $authId = Auth::id();
        $profile = UserDetail::find($receiverId);
        $messages = Chat::where(function ($q) use ($authId, $receiverId) {
            $q->where('sender', $authId)->where('receiver', $receiverId);
        })->orWhere(function ($q) use ($authId, $receiverId) {
            $q->where('sender', $receiverId)->where('receiver', $authId);
        })->where('created_at', '>=', now()->subMonths(6))->orderBy('created_at', 'asc')->get();

        // check the user last update based on that we will show online or offline
        $user_active = UserDetail::where('id', $receiverId)->first();
        $lastUpdate = Carbon::parse($user_active->updated_at);
        $now = Carbon::now();
        $diffInMinutes = round($lastUpdate->diffInMinutes($now));
        $last_time = $diffInMinutes <= 3 ? true : false;

        $html = '';
        if ($messages->isEmpty()) {
            $html = '';
            $lastId = null;

            return response()->json(['html' => $html, 'msg_id' => $lastId]);
        } else {
            try {
                foreach ($messages as $msg) {
                    if ($msg->sender == $authId) {

                        if ($msg->type && in_array($msg->type, ['job', 'product', 'service', 'post', 'profile'])) {
                            $parts = explode('|', $msg->message);

                            $shareType = $parts[0];
                            // $shareTitle = $parts[1] ?? 'No Title';
                            $shareId = $parts[1];
                            // $shareUrl = $parts[3] ?? '#';
                            // $link = $parts[3];
                            // log::info('Shared message parts', ['parts' => $parts]);

                            if ($msg->type == 'post') {
                                $post_data = Posts::with('user:id,user_name,profile_img,badge')->where('id', $shareId)->first();
                                $shareUrl = 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.$post_data->file[0] ?? null;
                                $shareTitle = $post_data->caption ?? null;
                                $shareSubTitle = $shareType;
                                $userProfileImg = asset($post_data->user->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $post_data->user->profile_img : 'assets/images/Avatar.png');
                                $badgeImg = asset('assets/images/Badge_'.$post_data->user->badge.'.png') ?? asset('assets/images/Badge_0.png');
                                $sensitiveImg = asset('assets/images/Sensitive.png');
                                $link = env('BASE_URL').'user-profile/'.$post_data->created_by.'/'.$post_data->id.'/'.$post_data->file_type;
                                $sense = $post_data->sense ?? 0;
                            } elseif ($msg->type == 'profile') {
                                $user_data = UserDetail::where('id', $shareId)->first();
                                $profileImg = $user_data->profile_img ?? 'avatar.png';
                                $shareUrl = asset($user_data->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $user_data->profile_img : 'assets/images/Avatar.png');
                                $badge = $user_data?->badge ?? 0;
                                $badgeImg = asset('assets/images/Badge_'.($user_data->badge ?? 0).'.png');
                                $link = env('BASE_URL').'user-profile/'.$user_data->id;
                                $shareTitle = $user_data->user_name ?? null;
                                $shareSubTitle = $user_data->as_a ?? 'Consumer';
                            } elseif ($msg->type == 'job') {
                                $job_data = Jobs::where('id', $shareId)->first();
                                $shareUrl = env('BASE_URL').'assets/images/NoImage.png';
                                $shareTitle = $job_data->title ?? null;
                                $shareSubTitle = 'Job - '.$job_data->categoryRelation->value ?? null;
                                $link = env('BASE_URL').'job-details/'.$job_data->id;
                            } elseif ($msg->type == 'product') {
                                $product_data = Products::where('id', $shareId)->first();
                                $shareUrl = 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.$product_data->cover_img ?? null;
                                $shareTitle = $product_data->name ?? null;
                                $shareSubTitle = 'Products - '.$product_data->categoryRelation->value ?? null;
                                $link = env('BASE_URL').'individual-product/'.$product_data->id;
                            } elseif ($msg->type == 'service') {
                                $service_data = Service::where('id', $shareId)->first();
                                $shareUrl = 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.$service_data->image ?? null;
                                $shareTitle = $service_data->title ?? null;
                                $shareSubTitle = 'Services - '.$service_data->serviceType->value ?? null;
                                $link = env('BASE_URL').'individual-service/'.$service_data->id;
                            }

                            $html .= '<div class="message outgoing">';
                            if (($msg->type == 'post') && ($post_data->file_type == 'video')) {
                                $html .= '
                                <a href="'.$link.'" target="_blank" class="w-100">
                                    <div class="message-content w-100 ms-auto">
                                        <div class="d-flex align-items-center justify-content-start column-gap-2 pb-2">
                                            <div class="avatar-div-30 position-relative">
                                                <img src="'.$userProfileImg.'" class="avatar-30" alt="User">
                                                <img src="'.$badgeImg.'" class="badge-30" alt="Badge">
                                            </div>
                                            <h6 class="mb-0">'.$post_data->user->user_name.'</h6>
                                        </div>
                                        <div class="mb-1">
                                            <div class="item message-main-div position-relative">
                                                <video class="w-100 object-fit-cover rounded-3" height="175px">
                                                    <source src="'.$shareUrl.'" type="video/mp4"> </video>
                                            </div>
                                        </div>
                                        <h6 class="mb-1 caption" style="width: 100%; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 1; overflow: hidden; text-overflow: ellipsis;">
                                                '.$shareTitle.'
                                        </h6>
                                        <span class="time">'.$msg->created_at->format('h:i A | M d, Y').'</span>
                                    </div>
                                </a>
                            ';
                            } elseif (($msg->type == 'post') && ($post_data->file_type == 'image')) {
                                $html .= '
                                <a href="'.$link.'" target="_blank">
                                    <div class="message-content w-100 ms-auto">
                                        <div class="d-flex align-items-center justify-content-start column-gap-2 pb-2">
                                            <div class="avatar-div-30 position-relative">
                                                <img src="'.$userProfileImg.'" class="avatar-30" alt="User">
                                                <img src="'.$badgeImg.'" class="badge-30" alt="Badge">
                                            </div>
                                            <h6 class="mb-0">'.$post_data->user->user_name.'</h6>
                                        </div>
                                        <div class="mb-2">
                                            <div class="item message-main-div position-relative">
                                                <img src="'.$shareUrl.'"class="w-100 object-fit-cover rounded-3" height="175px" alt="Image">
                                ';
                                if ($sense == 1) {
                                    $html .= '
                                    <div class="sensitive-overlay z-3 rounded-3">
                                        <div class="overlay-content text-center">
                                            <img src="'.$sensitiveImg.'" height="50px" class="d-flex mx-auto mb-2" alt="">
                                        </div>
                                    </div>
                                ';
                                }
                                $html .= '
                                            </div>
                                        </div>
                                        <h6 class="mb-1 caption" style="width: 100%; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 1; overflow: hidden; text-overflow: ellipsis;">
                                                '.$shareTitle.'
                                        </h6>
                                        <span class="time">'.$msg->created_at->format('h:i A | M d, Y').'</span>
                                    </div>
                                </a>
                            ';
                            } elseif ($msg->type == 'profile') {
                                $html .= '
                                <div class="message-content ms-auto">
                                    <div class="d-flex align-items-center justify-content-start column-gap-2">
                                        <div class="avatar-div-70 position-relative">
                                            <img src="'.$shareUrl.'"class="avatar-70" alt="User">
                                            <img src="'.$badgeImg.'" class="badge-70" alt="Badge">
                                        </div>
                                        <div>
                                            <h6 class="mb-1">
                                                '.$shareTitle.'
                                            </h6>
                                            <h6 class="mb-1 small-h6">
                                                '.$shareSubTitle.'
                                            </h6>
                                            <a href="'.$link.'" target="_blank">
                                                <button class="removebtn w-100 py-1">View Profile</button>
                                            </a>
                                        </div>
                                    </div>
                                    <span class="time">'.$msg->created_at->format('h:i A | M d, Y').'</span>
                                </div>
                            ';
                            } elseif ($msg->type == 'job') {
                                $html .= '
                                <div class="message-content w-75 ms-auto">
                                    <div class="mb-2">
                                        <div class="item message-main-div position-relative">
                                            <img src="'.$shareUrl.'"class="w-100 object-fit-cover rounded-3" height="175px" alt="Job">
                                        </div>
                                    </div>
                                    <h6 class="mb-1 small-h6">
                                        '.$shareSubTitle.'
                                    </h6>
                                    <h6 class="mb-1 caption" style="width: 100%; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 1; overflow: hidden; text-overflow: ellipsis;">
                                            '.$shareTitle.'
                                    </h6>
                                    <a href="'.$link.'" target="_blank">
                                        <button class="removebtn w-100 py-1">View Job</button>
                                    </a>
                                    <span class="time">'.$msg->created_at->format('h:i A | M d, Y').'</span>
                                </div>
                            ';
                            } elseif ($msg->type == 'product') {
                                $html .= '
                                <div class="message-content w-75 ms-auto">
                                    <div class="mb-2">
                                        <div class="item message-main-div position-relative">
                                            <img src="'.$shareUrl.'"class="w-100 object-fit-cover rounded-3" height="175px" alt="Job">
                                        </div>
                                    </div>
                                    <h6 class="mb-1 small-h6">
                                        '.$shareSubTitle.'
                                    </h6>
                                    <h6 class="mb-1 caption" style="width: 100%; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 1; overflow: hidden; text-overflow: ellipsis;">
                                            '.$shareTitle.'
                                    </h6>
                                    <a href="'.$link.'" target="_blank">
                                        <button class="removebtn w-100 py-1">View Product</button>
                                    </a>
                                    <span class="time">'.$msg->created_at->format('h:i A | M d, Y').'</span>
                                </div>
                            ';
                            } elseif ($msg->type == 'service') {
                                $html .= '
                                <div class="message-content w-75 ms-auto">
                                    <div class="mb-2">
                                        <div class="item message-main-div position-relative">
                                            <img src="'.$shareUrl.'"class="w-100 object-fit-cover rounded-3" height="175px" alt="Job">
                                        </div>
                                    </div>
                                    <h6 class="mb-1 small-h6">
                                        '.$shareSubTitle.'
                                    </h6>
                                    <h6 class="mb-1 caption" style="width: 100%; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 1; overflow: hidden; text-overflow: ellipsis;">
                                            '.$shareTitle.'
                                    </h6>
                                    <a href="'.$link.'" target="_blank">
                                        <button class="removebtn w-100 py-1">View Service</button>
                                    </a>
                                    <span class="time">'.$msg->created_at->format('h:i A | M d, Y').'</span>
                                </div>
                            ';
                            }
                            $html .= '</div>';
                        } else {
                            $html .= '
                            <div class="message outgoing w-auto">
                                <div class="message-content ms-auto">
                                    <h6>'.nl2br($msg->message).'</h6>
                                    <span class="time">'.$msg->created_at->format('h:i A | M d, Y').'</span>
                                </div>
                            </div>';
                        }

                    } else {
                        // Incoming message
                        $update = Chat::where('id', $msg->id)->update(['seen' => 1]);
                        $update_chat = UserDetail::where('id', $authId)->update(['open_chat' => $receiverId]);
                        if ($msg->type && in_array($msg->type, ['job', 'product', 'service', 'post', 'profile'])) {
                            // $parts = explode('_', $msg->message, 4);

                            // // log::info('Shared message parts', ['parts' => $parts]);
                            // $shareType = $parts[0];
                            // $shareTitle = $parts[1] ?? 'No Title';
                            // $shareId = $parts[2] ?? '0';
                            // $shareUrl = $parts[3] ?? '#';
                            // $link = $parts[3];

                            $parts = explode('|', $msg->message);

                            $shareType = $parts[0];
                            // $shareTitle = $parts[1] ?? 'No Title';
                            $shareId = $parts[1];

                            if ($msg->type == 'post') {
                                $post_data = Posts::with('user:id,user_name,profile_img,badge')->where('id', $shareId)->first();
                                $shareUrl = 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.$post_data->file[0] ?? null;
                                $shareTitle = $post_data->caption ?? null;
                                $shareSubTitle = 'Sent a post';
                                $userProfileImg = asset($post_data->user->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $post_data->user->profile_img : 'assets/images/Avatar.png');
                                $badgeImg = asset('assets/images/Badge_'.$post_data->user->badge.'.png') ?? asset('assets/images/Badge_0.png');
                                $sensitiveImg = asset('assets/images/Sensitive.png');
                                $sense = $post_data->sense ?? 0;
                                $link = env('BASE_URL').'user-profile/'.$post_data->created_by.'/'.$post_data->id.'/'.$post_data->file_type;

                            } elseif ($msg->type == 'profile') {
                                $user_data = UserDetail::where('id', $shareId)->first();
                                $shareUrl = asset($user_data->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $user_data->profile_img : 'assets/images/Avatar.png');
                                $badgeImg = asset('assets/images/Badge_'.$user_data->badge.'.png') ?? asset('assets/images/Badge_0.png');
                                $shareTitle = $user_data->user_name ?? null;
                                $shareSubTitle = $user_data->as_a ?? 'Consumer';
                                $link = env('BASE_URL').'user-profile/'.$user_data->id;
                            } elseif ($msg->type == 'job') {
                                $job_data = Jobs::where('id', $shareId)->first();
                                $shareUrl = env('BASE_URL').'assets/images/NoImage_1.png';
                                $shareTitle = $job_data->title ?? null;
                                $shareSubTitle = 'Job - '.$job_data->categoryRelation->value ?? null;
                                $link = env('BASE_URL').'job-details/'.$job_data->id;
                            } elseif ($msg->type == 'product') {
                                $product_data = Products::where('id', $shareId)->first();
                                $shareUrl = 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.$product_data->cover_img ?? null;
                                $shareTitle = $product_data->name ?? null;
                                $shareSubTitle = 'Products - '.$product_data->categoryRelation->value ?? null;
                                $link = env('BASE_URL').'individual-product/'.$product_data->id;
                            } elseif ($msg->type == 'service') {
                                $service_data = Service::where('id', $shareId)->first();
                                $shareUrl = 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.$service_data->image ?? null;
                                $shareTitle = $service_data->title ?? null;
                                $shareSubTitle = 'Services - '.$service_data->serviceType->value ?? null;
                                $link = env('BASE_URL').'individual-service/'.$service_data->id;
                            }

                            $html .= '
                                <div class="message incoming">
                                <img src="'.'https://onstru-social.s3.ap-south-1.amazonaws.com/'.($profile->profile_img).'" class="avatar-25 message-avatar" alt="User">';
                            if (($msg->type == 'post') && ($post_data->file_type == 'video')) {
                                $html .= '
                                <a href="'.$link.'" target="_blank" class="w-100">
                                    <div class="message-content w-100 me-auto">
                                        <div class="d-flex align-items-center justify-content-start column-gap-2 pb-2">
                                            <div class="avatar-div-30 position-relative">
                                                <img src="'.$userProfileImg.'" class="avatar-30" alt="User">
                                                <img src="'.$badgeImg.'" class="badge-30" alt="Badge">
                                            </div>
                                            <h6 class="mb-0">'.$post_data->user->user_name.'</h6>
                                        </div>
                                        <div class="mb-1">
                                            <div class="item message-main-div position-relative">
                                                <video class="w-100 object-fit-cover rounded-3" height="175px">
                                                    <source src="'.$shareUrl.'" type="video/mp4"> </video>
                                            </div>
                                        </div>
                                        <h6 class="mb-1 caption" style="width: 100%; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 1; overflow: hidden; text-overflow: ellipsis;">
                                                '.$shareTitle.'
                                        </h6>
                                        <span class="time">'.$msg->created_at->format('h:i A | M d, Y').'</span>
                                    </div>
                                </a>
                            ';
                            } elseif (($msg->type == 'post') && ($post_data->file_type == 'image')) {
                                $html .= '
                                    <a href="'.$link.'" target="_blank">
                                        <div class="message-content w-100 me-auto">
                                            <div class="d-flex align-items-center justify-content-start column-gap-2 pb-2">
                                                <div class="avatar-div-30 position-relative">
                                                    <img src="'.$userProfileImg.'" class="avatar-30" alt="User">
                                                    <img src="'.$badgeImg.'" class="badge-30" alt="Badge">
                                                </div>
                                                <h6 class="mb-0">'.$post_data->user->user_name.'</h6>
                                            </div>
                                            <div class="mb-2">
                                                <div class="item message-main-div position-relative">
                                                    <img src="'.$shareUrl.'"class="w-100 object-fit-cover rounded-3" height="175px" alt="Image">
                                                    ';
                                if ($sense == 1) {
                                    $html .= '
                                        <div class="sensitive-overlay z-3 rounded-3">
                                            <div class="overlay-content text-center">
                                                <img src="'.$sensitiveImg.'" height="50px" class="d-flex mx-auto mb-2" alt="">
                                            </div>
                                        </div>
                                    ';
                                }
                                $html .= '
                                            </div>
                                        </div>
                                        <h6 class="mb-1 caption" style="width: 100%; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 1; overflow: hidden; text-overflow: ellipsis;">
                                                '.$shareTitle.'
                                        </h6>
                                        <span class="time">'.$msg->created_at->format('h:i A | M d, Y').'</span>
                                    </div>
                                </a>
                            ';
                            } elseif ($msg->type == 'profile') {
                                $html .= '
                                <div class="message-content w-auto me-auto">
                                    <div class="d-flex align-items-center justify-content-start column-gap-2">
                                        <div class="avatar-div-70 position-relative">
                                            <img src="'.$shareUrl.'"class="avatar-70" alt="User">
                                            <img src="'.$badgeImg.'" class="badge-70" alt="Badge">
                                        </div>
                                        <div>
                                            <h6 class="mb-1">
                                                '.$shareTitle.'
                                            </h6>
                                            <h6 class="mb-1 small-h6">
                                                '.$shareSubTitle.'
                                            </h6>
                                            <a href="'.$link.'" target="_blank">
                                                <button class="removebtn w-100 py-1">View Profile</button>
                                            </a>
                                        </div>
                                    </div>
                                    <span class="time">'.$msg->created_at->format('h:i A | M d, Y').'</span>
                                </div>
                            ';
                            } elseif ($msg->type == 'job') {
                                $html .= '
                                <div class="message-content w-75 me-auto">
                                    <div class="mb-2">
                                        <div class="item message-main-div position-relative">
                                            <img src="'.$shareUrl.'"class="w-100 object-fit-cover rounded-3" height="175px" alt="Job">
                                        </div>
                                    </div>
                                    <h6 class="mb-1 small-h6">
                                        '.$shareSubTitle.'
                                    </h6>
                                    <h6 class="mb-1 caption" style="width: 100%; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 1; overflow: hidden; text-overflow: ellipsis;">
                                            '.$shareTitle.'
                                    </h6>
                                    <a href="'.$link.'" target="_blank">
                                        <button class="removebtn w-100 py-1">View Job</button>
                                    </a>
                                    <span class="time">'.$msg->created_at->format('h:i A | M d, Y').'</span>
                                </div>
                            ';
                            } elseif ($msg->type == 'product') {
                                $html .= '
                                <div class="message-content w-75 me-auto">
                                    <div class="mb-2">
                                        <div class="item message-main-div position-relative">
                                            <img src="'.$shareUrl.'"class="w-100 object-fit-cover rounded-3" height="175px" alt="Job">
                                        </div>
                                    </div>
                                    <h6 class="mb-1 small-h6">
                                        '.$shareSubTitle.'
                                    </h6>
                                    <h6 class="mb-1 caption" style="width: 100%; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 1; overflow: hidden; text-overflow: ellipsis;">
                                            '.$shareTitle.'
                                    </h6>
                                    <a href="'.$link.'" target="_blank">
                                        <button class="removebtn w-100 py-1">View Product</button>
                                    </a>
                                    <span class="time">'.$msg->created_at->format('h:i A | M d, Y').'</span>
                                </div>
                            ';
                            } elseif ($msg->type == 'service') {
                                $html .= '
                                <div class="message-content w-75 me-auto">
                                    <div class="mb-2">
                                        <div class="item message-main-div position-relative">
                                            <img src="'.$shareUrl.'"class="w-100 object-fit-cover rounded-3" height="175px" alt="Job">
                                        </div>
                                    </div>
                                    <h6 class="mb-1 small-h6">
                                        '.$shareSubTitle.'
                                    </h6>
                                    <h6 class="mb-1 caption" style="width: 100%; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 1; overflow: hidden; text-overflow: ellipsis;">
                                            '.$shareTitle.'
                                    </h6>
                                    <a href="'.$link.'" target="_blank">
                                        <button class="removebtn w-100 py-1">View Service</button>
                                    </a>
                                    <span class="time">'.$msg->created_at->format('h:i A | M d, Y').'</span>
                                </div>
                            ';
                            }
                            $html .= '</div>';
                        } else {
                            $html .= '
                        <div class="message incoming me-auto">
                            <img src="'.'https://onstru-social.s3.ap-south-1.amazonaws.com/'.($profile->profile_img).'" class="avatar-25 message-avatar" alt="User">
                            <div class="message-content nrml me-auto">
                                <h6>'.nl2br($msg->message).'</h6>
                                <span class="time">'.$msg->created_at->format('h:i A | M d, Y').'</span>
                            </div>
                        </div>';
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Message error', ['error' => $e->getMessage(), 'line' => $e->getLine()]);
            }
        }

        $lastId = $messages->last()?->id;

        return response()->json(['html' => $html, 'msg_id' => $lastId, 'is_online' => ($last_time == false) ? $user_active->updated_at->diffForHumans() : 'Online']);
    }

    public function chat_msg_ind(Request $req)
    {
        // log::info('chat_msg_ind called', ['request' => $req->all()]);
        $receiverId = $req->rec_id;
        $authId = Auth::id() ?? 4;
        $msg = Chat::create([
            'sender' => $authId, // ID of the logged-in user
            'receiver' => $receiverId, // ID of the recipient
            'message' => $req->msg, // Message content
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $profile = UserDetail::find($receiverId);

        $chat = Chat::where('id', $msg->id)->first();
        $open_chat = UserDetail::where('id', $receiverId)->pluck('open_chat')->first();

        // log::info('token', ['web_token' => $chat->receiverUser->web_token, 'mob_token' => $chat->receiverUser->mob_token]);
        if ($open_chat != $authId) {
            if (($chat->receiverUser->web_token || $chat->receiverUser->mob_token)) {
                $data = [
                    'web_token' => $chat->receiverUser->web_token ?? null,
                    'mob_token' => $chat->receiverUser->mob_token ?? null,
                    'title' => 'New Message',
                    'body' => 'You have received a new message from '.(Auth::user()?->name ?? 'Guest User'),
                    'id' => $chat->sender,
                    'link' => route('chat_card'),
                ];

                app(NotificationService::class)->token($data);
            }
        }

        try {
            Log::info('Broadcasting message', ['message' => $msg->message, 'to' => $receiverId]);
            broadcast(new PrivateMessageSent($msg->message, $authId, $receiverId))->toOthers();
        } catch (\Exception $e) {
            Log::error('Broadcast failed', ['error' => $e->getMessage()]);
        }

        if ($req->is('api/*')) {

            try {
                Log::info('Broadcasting message API', ['message' => $msg->message, 'to' => $receiverId]);
                broadcast(new PrivateMessageSent($msg->message, $authId, $receiverId))->toOthers();
            } catch (\Exception $e) {
                Log::error('Broadcast failed', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'msg_id' => $msg->id,
                'sender' => $authId,
                'receiver' => $receiverId,
                'message' => $msg->message,
                'created' => $msg->created_at->toDateTimeString(),
            ]);
        }
        // Render HTML for the new message
        $html = '';
        $html .= '
        <div class="message outgoing ms-auto w-auto">
            <div class="message-content ms-auto">
                <h6>'.nl2br(e($msg->message)).'</h6>
                <span class="time">'.$msg->created_at->format('h:i A | M d, Y').'</span>
            </div>
        </div>';

        return response()->json(['html' => $html, 'msg_id' => $msg->id]);
    }

    public function chat_rec_send(Request $req)
    {
        $authId = Auth::id();
        $receiverId = $req->rec_id; // the person you're chatting with
        $lastId = (int) $req->last_id; // last message id client has
        $profile = UserDetail::find($receiverId);
        $hasNew = Chat::where('sender', $receiverId) // only messages from other person
            ->where('receiver', $authId) // directed to me
            ->where('id', '>', $lastId) // newer than last seen
            ->exists();
        if (! $hasNew) {
            return response()->json([
                'html' => '', // empty => frontend skips append
                'last_id' => $lastId, // unchanged
                'has_new' => false,
            ]);
        }
        // There are new messages — fetch them (ordered oldest -> newest)
        $newMessages = Chat::where('sender', $receiverId)
            ->where('receiver', $authId)
            ->where('id', '>', $lastId)
            ->orderBy('id', 'asc')
            ->first();

        // Render HTML from a blade partial for each message (clean & maintainable)
        $html = '';
        if ($newMessages) {
            $html = '
        <div class="message incoming">
            <img src="'.'https://onstru-social.s3.ap-south-1.amazonaws.com/'.($profile->profile_img).'" class="avatar-30 message-avatar" alt="User">
            <div class="message-content">
                <h6>'.nl2br(($newMessages->message)).'</h6>
                <span class="time">'.$newMessages->created_at->format('h:i A | M d, Y').'</span>
            </div>
        </div>';
        }

        return response()->json([
            'html' => $html,
            'last_id' => $newMessages->id ? $newMessages->id : $lastId,
            'has_new' => (bool) $newMessages, // true if new msg exists
        ]);
    }

    public function chat_msg_arr(Request $req)
    {
        $receiverId = $req->selected;
        $post_id = $req->post_id;
        if ($req->share_type == 'job') {
            $job_data = Jobs::where('id', $post_id)->first();
            $url = 'Job Shared|'.$job_data->id;
            // $url = 'Sent a job'.'_'.$job_data->title.'_'.$job_data->id.'_'.env('BASE_URL').'job-details/'.$job_data->id;
        } elseif ($req->share_type == 'product') {
            $product_data = Products::where('id', $post_id)->first();
            // $url = 'Sent a product'.'_'.$product_data->name.'_'.$product_data->id.'_'.env('BASE_URL').'individual-product/'.$product_data->id;
            $url = 'Product Shared|'.$product_data->id;
        } elseif ($req->share_type == 'service') {
            $service_data = Service::where('id', $post_id)->first();
            // $url = 'Sent a service'.'_'.$service_data->title.'_'.$service_data->id.'_'.env('BASE_URL').'individual-service/'.$service_data->id;
            $url = 'Service Shared|'.$service_data->id;
        } elseif ($req->share_type == 'post') {
            $post_data = Posts::where('id', $post_id)->first();
            if ($post_data->file_type == 'video') {
                $url = 'Reel Shared|'.$post_data->id;
                // $url = 'Sent a reel'.'_'.$post_data->caption.'_'.$post_data->id.'_'.env('BASE_URL').'user-profile/'.$post_data->created_by.'/'.$post_data->id.'/'.$post_data->file_type;
            } else {
                $url = 'Post Shared|'.$post_data->id;
                // $url = 'Sent a post'.'_'.$post_data->caption.'_'.$post_data->id.'_'.env('BASE_URL').'user-profile/'.$post_data->created_by.'/'.$post_data->id.'/'.$post_data->file_type;
            }

        } else {
            $user_data = UserDetail::where('id', $post_id)->first();
            $url = 'Profile Shared|'.$user_data->id;
            // $url = 'Sent a profile'.'_'.$user_data->name.'_'.$user_data->id.'_'.env('BASE_URL').'user-profile/'.$user_data->id;
        }
        $authId = Auth::id();
        foreach ($receiverId as $send) {
            $msg = Chat::create([
                'sender' => Auth::id(),
                'receiver' => $send,
                'type' => $req->share_type,
                'message' => $url,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            broadcast(new PrivateMessageSent($url, $authId, $send));
            $receiverUser = UserDetail::where('id', $send)->first();
            if ($receiverUser && ($receiverUser->web_token || $receiverUser->mob_token)) {
                $data = [
                    'web_token' => $receiverUser->web_token ?? null,
                    'mob_token' => $receiverUser->mob_token ?? null,
                    'title' => 'New Message',
                    'body' => $url.(Auth::user()->name ?? 'Guest User'),
                    'id' => 1,
                    // 'link' => route('chat_card'),
                ];
                app(NotificationService::class)->token($data);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully!',
        ]);
    }

    public function chat_open_update(Request $req)
    {
        $authId = Auth::id();
        $update_chat = UserDetail::where('id', $authId)->update(['open_chat' => 0]);

        return response()->json([
            'success' => true,
            'message' => 'Chat open status updated successfully!',
        ]);
    }

    public function unseen_count(Request $req)
    {
        $authId = Auth::id();
        $unseenCounts = Chat::where('receiver', $authId)->where('seen', 0)->count();
        $notify_unseen = Notification::where('reciever', $authId)->where('status', 'active')->where('seen', 0)->count();

        return response()->json(['status' => true, 'unseenCounts' => $unseenCounts, 'notify_unseen' => $notify_unseen]);
    }

    public function chatbot(Request $request)
    {
        $hasSubscription = 0;

        $following = Auth::user()->following()->latest('follows.created_at')->get();
        $followers = Auth::user()->followers()->latest('follows.created_at')->get();
        $gstverified = GstDetails::where('user_id', Auth::id())->where('gst_verify', 'yes')->first();
        if ($gstverified === null) {
            $gstverified = 'no';
        } else {
            $gstverified = 'yes';
        }
        $charge = Charge::where('category', 'chat_bot')->latest()->value('charge') * 1.18;
        $botlist = Chat_bot::where('c_by', Auth::id())->latest()->get();
        $botlatest = Chat_bot::where('c_by', Auth::id())->latest()->first();
        if ($botlatest) {
            $hasSubscription = ($botlatest->token > $botlatest->used);
        } else {
            $hasSubscription = false;
        }

        if ($request->header('Authorization')) {
            return response()->json([
                'success' => true,
                'botlist' => $botlist,
                'charge' => $charge,
                'hasSubscription' => false,
            ]);
        }

        return view('chatbot.index', compact('botlist', 'charge', 'hasSubscription', 'gstverified', 'hasSubscription', 'following', 'followers'));
    }

    public function subscribe(Request $req)
    {
        $user = Auth::user();
        $charge = Charge::where('category', 'chat_bot')->latest()->value('charge') * 1.18;
        UserDetail::where('id', $user->id)
            ->update([
                'balance' => $user->balance - $charge,
            ]);
        Chat_bot::create([
            'c_by' => $user->id,
            'amount' => $charge,
            'token' => 1000,
            'used' => 0,
            'status' => 'active',
        ]);

        if ($req->header('Authorization')) {
            return response()->json([
                'success' => true,
                'message' => 'Chat bot Subscribed Successfully!',
            ]);
        }

        return back()->with('success', 'Chat bot Subscribed Successfully!');
    }

    // // function for third party smartbot....

    // public function token_exceed(Request $req)
    // {

    //     $slug = $req->user_data;

    //     $user = UserDetail::where('slug', $slug)->first();
    //     if (! $user) {
    //         return response()->json(['status' => 'error', 'message' => 'User not found']);
    //     }

    //     $token_ex = Chat_bot::where('c_by', $user->id)->latest();

    //     if ($token_ex->count() > 0) {
    //         $token_ex = $token_ex->first();
    //         if ($token_ex->used >= $token_ex->token) {
    //             return response()->json(['status' => 'error', 'message' => 'Token limit exceeded', 'token_exceed' => true]);
    //         } else {
    //             return response()->json(['status' => 'success', 'message' => 'Token available', 'token_exceed' => false]);
    //         }
    //     } else {
    //         return response()->json(['status' => 'success', 'message' => 'No token found', 'token_exceed' => true]);
    //     }
    // }

    // // function to update the token used count
    // public function update_token_count(Request $req)
    // {
    //     $slug = $req->user_data;
    //     $used_token = $req->used_token;

    //     $user = UserDetail::where('slug', $slug)->first();
    //     if (! $user) {
    //         return response()->json(['status' => 'error', 'message' => 'User not found']);
    //     }

    //     $token_ex = Chat_bot::where('c_by', $user->id)->latest();

    //     if ($token_ex->count() > 0) {
    //         $token_ex = $token_ex->first();

    //         $new_used = $token_ex->used + $used_token;

    //         $token_ex->used = $new_used;
    //         $token_ex->save();

    //         return response()->json(['status' => 'success', 'message' => 'Token count updated']);

    //     } else {
    //         return response()->json(['status' => 'error', 'message' => 'No token found']);
    //     }
    // }

    public function token_exceed(Request $req)
    {

        try {
            $slug = $req->user_data;
            $user = UserDetail::where('slug', $slug)->first();
            // if (! $user) {
            //     log::info('User_check', ['token_exceed' => $user]);
            //     return response()->json(['status' => 'error', 'message' => 'User not found']);
            // }
            $token_ex = Chat_bot::where('c_by', $user->id)->latest()->first();
            log::info('Token exceed check for user', ['chat_bot' => $token_ex]);
            if (! $token_ex) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No token found',
                    'token_exceed' => false,
                ]);
            }
            $exceeded = $token_ex->used >= $token_ex->token;
            Log::info('Token exceed check for user', [
                'user_id' => $user->id,
                'token_exceed' => $exceeded,
            ]);

            return response()->json([
                'status' => $exceeded ? 'error' : 'success',
                'message' => $exceeded ? 'Token limit exceeded' : 'Token available',
                'token_exceed' => $exceeded,
            ]);
            // if ($token_ex) {
            //     if ($token_ex->used >= $token_ex->token) {
            //         return response()->json(['status' => 'error', 'message' => 'Token limit exceeded', 'token_exceed' => true]);

            //         log::info('Token exceed check for user', ['token_exceed' => true]);

            //     } else {
            //         return response()->json(['status' => 'success', 'message' => 'Token available', 'token_exceed' => false]);

            //         log::info('Token exceed check for user', ['token_exceed' => false]);

            //         }
            // } else {
            //     log::info('Token exceed check for user', ['message' => 'No token found']);
            //     return response()->json(['status' => 'success', 'message' => 'No token found', 'token_exceed' => false]);
            // }
        } catch (\Exception $e) {
            log::error('Token exceed check failed', ['error' => $e->getMessage()]);

            return response()->json(['status' => 'error', 'message' => 'An error occurred']);
        }
    }

    // function to update the token used count
    public function update_token_count(Request $req)
    {
        $slug = $req->user_data;
        $used_token = $req->used_token;
        log::info('Update token count request', ['slug' => $slug, 'used_token' => $used_token]);
        $user = UserDetail::where('slug', $slug)->first();
        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'User not found']);
        }
        $token_ex = Chat_bot::where('c_by', $user->id)->latest();
        if ($token_ex->count() > 0) {
            $token_ex = $token_ex->first();
            $new_used = $token_ex->used + $used_token;
            $token_ex->used = $new_used;
            $token_ex->save();

            return response()->json(['status' => 'success', 'message' => 'Token count updated']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'No token found']);
        }
    }

    // function for share search users
    public function search_share_users(Request $req)
    {
        $search = $req->keyword;
        $authId = Auth::id();
        // $users = Follow::where('follower_id', $authId)
        //     ->whereHas('followingUser', function ($query) use ($search, $authId) {
        //         $query->where('id', '!=', $authId)
        //             ->where(function ($q) use ($search) {
        //                 $q->where('name', 'like', '%'.$search.'%')
        //                     ->orWhere('user_name', 'like', '%'.$search.'%');
        //             });
        //     })
        //     ->with(['followingUser' => function ($query) {
        //         $query->select('id', 'name', 'user_name', 'profile_img', 'slug');
        //     }])
        //     ->get();
        $users = Follow::select('id', 'follower_id', 'following_id')
            ->where(function ($q) use ($authId) {
                $q->where('follower_id', $authId)
                    ->orWhere('following_id', $authId);
            })
            ->with([
                'followerUser:id,name,user_name,profile_img,slug',
                'followingUser:id,name,user_name,profile_img,slug',
            ])
            ->get()
            ->map(function ($item) use ($authId) {
                // Tag each record as either 'following' or 'follower'
                $item->type = $item->follower_id == $authId ? 'following' : 'follower';
                $item->user = $item->follower_id == $authId
                    ? $item->followingUser
                    : $item->followerUser;

                $item->user_data = $item->user;
                unset($item->followerUser, $item->followingUser);

                return [
                    'type' => $item->type,
                    'id' => $item->user->id ?? null,
                    'name' => $item->user->name ?? null,
                    'user_name' => $item->user->user_name ?? null,
                    'profile_img' => $item->user->profile_img ?? null,
                    'slug' => $item->user->slug ?? null,
                ];
            });

        return response()->json([
            'users' => $users,
        ]);
    }
}
