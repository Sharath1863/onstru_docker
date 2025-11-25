<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Popup_cnt extends Controller
{
    // function for ind_post popup
    public function ind_post(Request $request, Home_cnt $home_cnt)
    {
        $post_id = $request->post_id;
        $post = Posts::with(['likedByAuth', 'post_save', 'post_report'])->where('id', $post_id)->first();
        $post->is_liked = $post->likedByAuth ? true : false;
        $post->is_saved = $post->post_save ? true : false;
        $post->is_reported = $post->post_report !== null ? true : false;
        $like_icon = $post->is_liked ? 'fa-solid active' : 'fa-regular';
        $save_icon = $post->is_saved ? 'fa-solid active' : 'fa-regular';
        // $report_icon = $post->is_reported ? 'fa-solid active' : 'fa-regular';
        if ($post->like_cnt >= 1000) {
            $cnt = number_format($post->like_cnt / 1000, 1).'k Likes';
        } elseif ($post->like_cnt == 0) {
            $cnt = ' likes';
        } else {
            $cnt = $post->like_cnt.' likes';
        }

        $comments = DB::table('comment_list')
            ->join('user_detail as users', 'comment_list.user_id', '=', 'users.id')
            ->join('posts', 'comment_list.post_id', '=', 'posts.id')
            ->where('comment_list.post_id', $request->post_id)
            ->latest('comment_list.created_at')
            ->select('comment_list.id as com_id', 'users.id', 'users.name', 'users.user_name', 'users.badge', 'users.profile_img', 'comment_list.comment', 'comment_list.created_at as c_at', 'posts.created_by as post_owner', 'comment_list.user_id as created_by')
            ->get();

        $html = '<div class="d-flex justify-content-between align-items-center pt-2 pb-4 position-sticky sticky-top bg-white">
                <div class="modal-user">
                    <div class="d-flex align-items-center justify-content-start gap-2">
                        <div class="avatar-div-30 position-relative">
                            <img id="post_cby_img"
                                src="'.($post->user->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.$post->user->profile_img : asset('assets/images/Avatar.png')).'"
                                class="avatar-30" alt="">
                                <img src="'.(asset($post->user->badge ? 'assets/images/Badge_'.$post->user->badge.'.png' : 'assets/images/Badge_0.png')).'"
                                    class="badge-30" alt="">
                            <img src="'.(asset($post->user->badge ? 'assets/images/Badge_'.$post->user->badge.'.png' : 'assets/images/Badge_0.png')).'"
                                    class="badge-30" alt="">
                        </div>
                        <div class="user-content">
                            <h6 id="post_cby" class="m-0 text-lowercase">'.$post->user->user_name.'</h6>
                        </div>
                    </div>
                </div>
                    <div class="dropdown">
                        <a data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis text-dark"></i>
                        </a>
                        <ul class="dropdown-menu">';
        if (Auth::id() == $post->created_by) {
            $html .= '
                            
                            <div class="auth-dropdown">
                                <li class="mb-1">
                                    <a class="dropdown-item edit-post-btn" data-bs-toggle="modal" data-bs-target="#editPost">
                                        <i class="fas fa-pen-to-square pe-1"></i>Edit
                                    </a>
                                </li>
                                <li class="mb-1">
                                    <a href="javascript:void(0);" class="dropdown-item delete-post-btn" data-post-id="'.$post->id.'">
                                        <i class="fas fa-trash text-danger pe-1"></i>Delete
                                    </a>
                                </li>
                            </div>';
        } else {
            if ($post->is_reported) {
                $html .= '           
                            <div class="user-dropdown">
                                <li>
                                    <a class="dropdown-item" data-bs-toggle="modal">
                                        <i class="fas fa-circle-check text-success pe-1"></i>Reported
                                    </a>
                                </li>
                            </div>';
            } else {
                $html .= '           
                            <div class="user-dropdown">
                                <li>
                                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#postReport" data-id="'.$post->id.'">
                                        <i class="fas fa-triangle-exclamation text-danger pe-1"></i>Report
                                    </a>
                                </li>
                            </div>';
            }
        }
        $html .= '
                        </ul>
                    </div>
                </div>
        <div id="commentList">';

        if ($comments->count() > 0) {
        foreach ($comments as $comment) {
            $profileImg = $comment->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.$comment->profile_img : asset('assets/images/Avatar.png');
            $badgeImg = asset($comment->badge ? 'assets/images/Badge_'.$comment->badge.'.png' : 'assets/images/Badge_0.png');
            $html .= '
                <input type="hidden"  id="post_comment_id" value="'.$post_id.'">
                <div class="modal-user comment-modal mb-3">
                    <div class="dropdown">
                        <div class="user-content d-flex align-items-start justify-content-between" data-bs-toggle="dropdown" aria-expanded="false" id="comment-'.$comment->com_id.'">
                            <div class="d-flex align-items-start justify-content-start column-gap-2">
                                <div class="avatar-div-30 position-relative">
                                    <img src="'.$profileImg.'" class="avatar-30" alt="">
                                    <img src="'.$badgeImg.'" class="badge-30" alt="">
                                </div>
                                <div>
                                    <h5 class="mb-1">'.e($comment->user_name).'</h5>
                                    <h6>'.e($comment->comment).'</h6>
                                </div>
                            </div>
                            <h6>'.e(Carbon::parse($comment->c_at)->diffForHumans(Carbon::now(), true)).'</h6>
                        </div>
                    <ul class="dropdown-menu">';
            if ((Auth::id() == $comment->post_owner) || (Auth::id() == $comment->created_by)) {
                $html .= '
                            <li class="mb-1">
                                <a class="dropdown-item comment-action" 
                                    data-id="'.$comment->com_id.'" 
                                    data-action="delete" 
                                    href="#">
                                        <i class="fas fa-trash text-danger pe-1"></i> Delete
                                </a>
                            </li>';
            }
            $html .= '
                            <li>
                                <a class="dropdown-item"  href="'.route('user-profile', $comment->created_by).'">
                                    <i class="fas fa-copy text-primary pe-1"></i> Profile
                                </a>
                            </li>';
            $html .= '
                        </ul>
                    </div>
                </div>';
        }
        } else {
            $emptyComment = asset('assets/images/img_comment.png');
            $html .= '
                <div class="d-flex align-items-center justify-content-center flex-column gap-2 mt-2">
                    <img src="'.$emptyComment.'" height="30px" alt="">
                    <h6 class="text-muted text-center" style="font-size: 12px;">No Comments Yet!</h6>
                </div>
            ';
        }

        // <!-- Like, Share, Comment Controls -->
        $html .= '
        </div>
            <div class="modal-user pt-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
                    <div class="d-flex align-items-center gap-3">
                        <a><i class="'.$like_icon.' fa-heart like-btn" id="like_icon" data-post-id="'.$post->id.'"></i></a>

                        <a data-open-comments data-bs-toggle="modal" data-bs-target="#sharePopup">
                            <i class="far fa-paper-plane share-btn" data-post-id="'.$post->id.'" id="comment_popup_post_id"
                                data-share-type="post"></i>
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a><i class="'.$save_icon.' fa-bookmark save-btn" id="save_icon" data-post-id="'.$post->id.'"></i></a>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <h5 class="mb-1 view-like likes-count" data-bs-toggle="modal" data-post-id="'.$post->id.'"
                        data-bs-target="#likesPopup">'.$cnt.'
                    </h5>
                    <h5 class="mb-1 text-capitalize">
                        <i class="fas fa-location-dot pe-1"></i>
                        <span>'.$post->location.'</span>
                    </h5>
                </div>
                <h6 class="caption" id="caption'.$post->id.'">'.e($post->caption).'</h6>
                <h6 class="mb-2 bio see-more text-muted" id="see-more'.$post->id.'" style="cursor: pointer;">See more</h6>
                <h6 class="mb-2">On '.$post->created_at->format('M d').'</h6>

                <!-- Comment Box -->
                <div class="input-group">
                    <input type="text" class="form-control" id="commentInput"
                        placeholder="Add a comment...">
                    <button class="formbtn pop_up_php" data-post-id="'.$post->id.'" id="postCommentBtn">Post</button>
                </div>
            </div>';

        return response()->json(['html' => $html]);
    }
}
