<?php

namespace App\Http\Controllers;

use App\Jobs\Video_process;
use App\Models\Follow;
use App\Models\Hashtag;
use App\Models\Notification;
use App\Models\Posts;
use App\Models\UserDetail;
use App\Services\Aws;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Reels_cnt extends Controller
{
    public function reels(Request $request)
    {
        // $posts = Posts::with('user')->where('file_type', 'video')->latest()->get();
        // return view('reels.index', compact('posts'));

        $users = UserDetail::where('id', '!=', Auth::id())->take(10)->get();
        $cursor = $request->input('reels_cursor');
        $followedUserIds = Follow::where('follower_id', Auth::id())
            ->pluck('following_id')
            ->toArray();

        $perPage = 20;
        $hasAsset = false;

        // Real DB reels
        $posts = Posts::with(['likedByAuth', 'post_save', 'post_report', 'user:id,name,user_name,profile_img'])
            ->where('created_by', '!=', Auth::id())
            ->where(function ($query) {
                $query->whereIn('file_type', ['video', 'post'])
                    ->orWhereNotNull('category');
            })
            // ->whereNotNull('category')
            ->where('status', 'active')
            ->orderByDesc('id')
            ->cursorPaginate($perPage, ['*'], 'reels_cursor', $cursor);

        // transform each post
        $posts->getCollection()->transform(function ($post) use ($followedUserIds) {
            $post->is_liked = $post->likedByAuth !== null;
            $post->is_saved = $post->post_save !== null;
            $post->is_reported = $post->post_report !== null;
            $post->is_followed = in_array($post->created_by, $followedUserIds);
            $post->type = 'db';

            return $post;
        });

        // 🔹 20% chance → inject dummy reel inside the collection
        if (rand(1, 100) <= 20) {
            $assetVideo = (object) [
                'id' => 'asset_1',
                'file_type' => 'premium',
                'file' => [asset('assets/images/dog.mp4')],
                'category' => 0,
                'caption' => 'Premium Content',
                'like_cnt' => 0,
                'com_cnt' => 0,
                'category_id' => 0,
                'created_by' => 0,
                'user' => (object) [
                    'id' => 0,
                    'name' => 'Onstru Premium',
                    'user_name' => 'onstru',
                    'profile_img' => asset('assets/images/Favicon.png'),
                    'badge' => 0,
                ],
                'is_liked' => false,
                'is_saved' => false,
                'is_followed' => false,
                'is_reported' => false,
                'likedByAuth' => null,
                'post_save' => null,
                'type' => 'asset',
                'created_at' => now(),
            ];

            $collection = $posts->getCollection();
            $randomIndex = rand(0, $collection->count());
            $collection->splice($randomIndex, 0, [$assetVideo]);
            $hasAsset = $collection->contains(function ($post) {
                return ($post->type ?? '') === 'asset';
            });

            $posts->setCollection($collection->values());
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('reels.reel-div', ['posts' => $posts])->render(),
                'next_cursor' => $posts->nextCursor()?->encode(),
                'posts_type' => $hasAsset,
            ]);
        }

        return view('reels.index', [
            'posts' => $posts,
            'users' => $users,
            'posts_type' => $hasAsset,
        ]);
    }

    public function store(Request $request, Aws $aws)
    {
        if ($request->post_id) {
            $updateData = [
                'caption' => $request->header('Authorization') ? $request->caption : $request->editcaption,
                'location' => $request->header('Authorization') ? $request->location : $request->editlocation,
            ];

            preg_match_all('/#(\w+)/u', $request->caption ?? '', $matches);
            $tags = collect($matches[1])
                ->map(fn($tag) => strtolower(trim($tag)))
                ->unique()
                ->filter();
            foreach ($tags as $tagName) {
                $hashtags = Hashtag::firstOrCreate(['tag_name' => $tagName]);
            }

            $exists = DB::table('posts')->where('id', $request->post_id)->exists();
            if (! $exists) {
                if ($request->header('Authorization')) {
                    return response()->json(['status' => 'error', 'message' => 'Post not found'], 404);
                }
                return back()->with('error', 'Post not found!');
            }
            $post_update = DB::table('posts')->where('id', $request->post_id)->update($updateData);
            // if ($post_update) {
            if ($request->header('Authorization')) {
                return response()->json(['status' => 'success', 'message' => 'Post Updated Successfully'], 200);
            } else {
                return back()->with('success', 'Post Updated Successfully!');
            }
        } else {
            $validator = Validator::make($request->all(), [
                'files' => 'required',
                'files.*' => [
                    'required',
                    'file',
                    function ($attribute, $value, $fail) {
                        $allowedImages = ['jpg', 'jpeg', 'png', 'webp'];
                        $allowedVideos = ['mp4', 'mov', 'avi', 'mkv'];
                        $extension = strtolower($value->getClientOriginalExtension());
                        if (! in_array($extension, array_merge($allowedImages, $allowedVideos))) {
                            $fail("The $attribute must be a valid image or video file.");
                        }
                    },
                ],
                'caption' => 'nullable|string',
                'location' => 'nullable|string',
            ]);

            preg_match_all('/#(\w+)/u', $request->caption ?? '', $matches);
            $tags = collect($matches[1])
                ->map(fn($tag) => strtolower(trim($tag)))
                ->unique()
                ->filter();
            foreach ($tags as $tagName) {
                $hashtags = Hashtag::firstOrCreate(['tag_name' => $tagName]);
            }

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $file = $request->file('files');
            if (! is_array($file)) {
                $file = [$file];
            }
            $folder = 'posts';
            $s3Key = $aws->common_upload_to_s3($file, $folder);
            $result = $aws->image_search($s3Key);
            $sensitiveImages = array_filter($result, function ($result) {
                return $result['level'] === 'sensitive';
            });
            $hasSensitive = count($sensitiveImages) > 0 ? 1 : 0;
            foreach ($file as $fl) {
                // detect file type (image or video)
                $mimeType = $fl->getMimeType();
                $fileType = str_contains($mimeType, 'video') ? 'video' : 'image';
                $storedFiles[] = [
                    'path' => $s3Key,
                    'type' => $fileType,
                ];
            }
            $contentType = count($storedFiles) > 1 ? 'multiple' : $storedFiles[0]['type'];
            Posts::create([
                'file_type' => $storedFiles[0]['type'], 
                'file' => $s3Key,
                'caption' => $request->caption,
                'location' => $request->location,
                'sense' => $hasSensitive ?? 0,
                'value' => json_encode($result, JSON_UNESCAPED_SLASHES),
                'status' => 'active',
                'created_by' => Auth::id(),
            ]);
            if ($request->header('Authorization')) {
                return response()->json(['status' => 'success', 'message' => 'Post Created Successfully!'], 200);
            }
            return redirect()->back()->with('success', 'Post Created Successfully!');
            // $file = $request->file('images');
            // foreach ($file as $fl) {
            //     $ext = strtolower($fl->getClientOriginalExtension());
            // }
            // $files = $request->file('files');
            // $storedFiles = [];
            // $fileType = null;
            // $contentType = null;

            // if ($files && count($files) > 0) {
            //     // Handle single video
            //     if (count($files) === 1 && $files[0]->getClientOriginalExtension() === 'mp4') {
            //         $fileType = 'video';
            //         $contentType = 'mp4';

            //         $file = $files[0];
            //         $path = $file->store('reels', 'public'); // stores in storage/app/public/reels
            //         $storedFiles[] = $path;
            //     } else {
            //         // Handle multiple images
            //         $fileType = 'image';
            //         foreach ($files as $file) {
            //             $ext = $file->getClientOriginalExtension();
            //             $contentType = $ext;

            //             $path = $file->store('posts', 'public'); // stores in storage/app/public/posts
            //             $storedFiles[] = $path;
            //         }
            //     }
            // }

            // Posts::create([
            //     'file_type' => $fileType,
            //     'content_type' => $contentType,
            //     'file' => json_encode($storedFiles),
            //     'caption' => $request->caption,
            //     'location' => $request->location,
            //     'status' => 'active',
            //     'created_by' => Auth::id(),
            // ]);

            // return redirect()->back()->with('success', 'Post created successfully!');
        }
    }

    public function post_delete(Request $request)
    {
        $postId = $request->id;
        if (! $postId) {
            return response()->json(['error' => 'Post ID is required.'], 400);
        }

        $updateData = ['status' => 'inactive'];
        $post_delete = DB::table('posts')->where('id', $postId)->update($updateData);
        if (! $post_delete) {
            return response()->json(['error' => 'Post not found or not updated.'], 404);
        }
        return response()->json(['success' => true, 'message' => 'Post deleted successfully.']);
    }
}
