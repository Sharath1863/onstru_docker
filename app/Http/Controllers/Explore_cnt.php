<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Explore_cnt extends Controller
{
    public function explore(Request $request, $search = null)
    {
        $postsCursor = $request->input('posts_cursor') ?? null;
        $search = $search ?? trim($request->query('keyword'));

        $auth = Auth::user()->id ?? 0;

        // $query = Posts::where('status', 'active')
        //     ->whereNull('category');

        // Base query
        $query = Posts::where('status', 'active')
            ->whereNull('category');

        // Apply search filter if present
        if (($search !== null) && ($search !== '') && (strtolower($search) !== 'empty')) {
            // $hashtag = strtolower(ltrim($search, '#'));
            // Match captions containing this hashtag
            // $query->whereRaw('LOWER(REPLACE(caption, "#", "")) LIKE ?', ["%{$hashtag}%"]);

            $hashtag = strtolower(ltrim($search, '#'));

            // Search for the hashtag (case-insensitive)
            $query->whereRaw(
                'caption REGEXP ?',
                ['(^|[^a-zA-Z0-9_])#'.$hashtag.'([^a-zA-Z0-9_]|$)']
            );
        } else {
            // Exclude current user's posts for default feed
            $query->where('created_by', '!=', $auth);
        }

        // Fetch paginated results
        $post_data = $query
            // ->inRandomOrder()
            ->orderBy('id', 'desc')
            ->cursorPaginate(30, ['*'], 'posts_cursor', $postsCursor);

        if ($request->header('Authorization')) {
            return response()->json([
                'success' => true,
                'data' => $post_data->items(),
                // 'all_html' => $allHtml,
                'next_posts_cursor' => $post_data->nextCursor()?->encode(),
                'next_page_url' => $post_data->nextPageUrl(),
                'prev_page_url' => $post_data->previousPageUrl(),
                'has_more_pages' => $post_data->hasMorePages(),
                'count' => $post_data->count(),
            ]);
        }

        if ($request->ajax()) {
            // dd($search, $query->toSql(), $query->getBindings());
            // dd($post_data);
            $allHtml = ''; // initialize before loop

            foreach ($post_data as $explore) {

                $assets = is_array($explore->file) ? $explore->file : [$explore->file];
                $fileUrls = array_map(fn ($f) => 'https://onstru-social.s3.ap-south-1.amazonaws.com/'.$f, $assets);
                $extensions = array_map(fn ($f) => strtolower(pathinfo($f, PATHINFO_EXTENSION)), $fileUrls);
                $hasVideo = count(array_intersect($extensions, ['mp4', 'mov', 'avi', 'mkv'])) > 0;
                $file_type = $hasVideo ? 'video' : 'image';

                $dataAssets = htmlspecialchars(json_encode($fileUrls), ENT_QUOTES, 'UTF-8');

                // Start HTML block
                $html = '<div class="explore-card post-bg position-relative ind_post" data-bs-toggle="modal" data-bs-target="#ind_post" data-post-id="'.$explore->id.'" data-type="'.$file_type.'" data-assets="'.$dataAssets.'">';

                // Check file type
                if ($file_type === 'image') {
                    $html .= '<img src="https://onstru-social.s3.ap-south-1.amazonaws.com/'.$assets[0].'" 
                class="w-100 object-fit-cover" height="100%">';

                    // Check sensitive
                    if ($explore->sense == 1) {
                        $html .= '
                    <div class="sensitive-overlay z-3">
                        <div class="overlay-content text-center">
                            <img src="'.asset('assets/images/Sensitive.png').'" height="75px"
                                 class="d-flex mx-auto mb-2" alt="">
                            <h6 class="mb-2">This content may be sensitive</h6>
                        </div>
                    </div>';
                    }
                } else {
                    $html .= '
                <video height="100%" class="w-100 object-fit-cover">
                    <source src="https://onstru-social.s3.ap-south-1.amazonaws.com/'.$assets[0].'" type="video/mp4">
                </video>
                <h6 class="mb-0 position-absolute" style="top: 5%; right: 5%;">
                    <img src="'.asset('assets/images/icon_video.png').'" height="25px" alt="">
                </h6>';
                }

                // Close main div
                $html .= '</div>';

                // Append this post's HTML to full output
                $allHtml .= $html;
            }

            return response()->json([
                'posts' => $post_data->items(),
                'all_html' => $allHtml,
                'next_posts_cursor' => $post_data->nextCursor()?->encode(),
                'next_page_url' => $post_data->nextPageUrl(),
                'prev_page_url' => $post_data->previousPageUrl(),
                'has_more_pages' => $post_data->hasMorePages(),
                'count' => $post_data->count(),
            ]);
        }

        // return response()->json([
        //     'posts' => $post_data->items(),
        //     'next_page_url' => $post_data->nextPageUrl(),
        //     'prev_page_url' => $post_data->previousPageUrl(),
        //     'has_more_pages' => $post_data->hasMorePages(),
        // ]);

        return view('explore.index', ['post_data' => $post_data, 'next_page_url' => $post_data->nextPageUrl(), 'prev_page_url' => $post_data->previousPageUrl(), 'has_more_pages' => $post_data->hasMorePages(), 'next_posts_cursor' => $post_data->nextCursor()?->encode()]);
    }
}
