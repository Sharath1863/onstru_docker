<?php

namespace App\Http\Controllers;

use App\Models\Hashtag;
use Illuminate\Http\Request;

class Hashtag_cnt extends Controller
{
    public function suggest(Request $request)
    {
        $query = $request->get('q', '');
        $hashtags = Hashtag::where('tag_name', 'like', $query.'%')
            ->limit(10)
            ->get(['tag_name'])
            ->map(fn ($tag) => ['key' => $tag->tag_name, 'value' => $tag->tag_name]);

        return response()->json($hashtags);
    }

    // function to get all hashtags
    public function hash_tags(Request $request)
    {
        $request->validate([
            'search' => 'sometimes|string',
        ]);

        $query = $request->input('search', '');

        if (empty($query)) {
            // If search is empty, get latest 5 hashtags
            $hashtags = Hashtag::orderBy('created_at', 'desc')->select('id', 'tag_name')->take(5)->get();
        } else {
            // Search hashtags by query
            $hashtags = Hashtag::where('tag_name', 'like', '%'.$query.'%')->select('id', 'tag_name')->get();
        }

        return response()->json(['success' => true, 'data' => $hashtags]);
    }
}
