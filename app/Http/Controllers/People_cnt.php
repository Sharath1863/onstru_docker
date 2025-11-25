<?php

namespace App\Http\Controllers;

use App\Models\DropdownList;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\log;

class People_cnt extends Controller
{
    public function peoples()
    {
        $location = Auth::user()->location ?? 0;
        $type = Auth::user()->as_a ?? null;
        $id = Auth::id();
        $users = UserDetail::where('id', '!=', $id)
            ->orderByRaw('
        CASE WHEN you_are = ? THEN 1 ELSE 0 END DESC,
        CASE WHEN location = ? THEN 1 ELSE 0 END DESC,
        created_at DESC
    ', [$type, $location])
            ->take(100)
            ->get();
        // $users = UserDetail::where('id', '!=', $id)
        //     ->orderByRaw("
        //         CASE WHEN you_are = {$type} THEN 1 ELSE 0 END DESC,
        //         CASE WHEN location = {$location} THEN 1 ELSE 0 END DESC,
        //         created_at DESC
        //     ")
        //     // ->orderByRaw("(CASE WHEN you_are = {$type} THEN 1 ELSE 0 END) DESC,(CASE WHEN location = {$location} THEN 0 ELSE 1 END)") // same location first
        //     // ->orderByDesc('created_at') // then newest first
        //     ->take(100)
        //     ->get();
        $locations = DropdownList::where('dropdown_id', 1)->pluck('value', 'id');

        return view('people.index', compact('locations', 'users'));
    }

    public function people(Request $req)
    {

        $user_location = Auth::user()->location ?? 0;
        $type = Auth::user()->as_a ?? null;
        $id = Auth::id();
        $locations = DropdownList::where('dropdown_id', 1)->pluck('value', 'id');
        $query = UserDetail::with('user_location', 'userProfile_search:id,c_by,services_offered')
            ->where('id', '!=', $id)
            ->where('status', 'active');

        // $api_user_detail = UserDetail::with('user_location')->where('id', '!=', $id)->where('status', 'active');

        if ($req->filled('keyword')) {
            $keyword = $req->keyword;

            // Log::info("message {$keyword}");

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%'.$keyword.'%')
                    ->orWhere('you_are', 'like', '%'.$keyword.'%')
                    ->orWhere('as_a', 'like', '%'.$keyword.'%')
                    ->orWhere('user_name', 'like', '%'.$keyword.'%')
                    ->orWhere('bio', 'like', '%'.$keyword.'%')
                    ->orWhereHas('user_location', function ($q2) use ($keyword) {
                        $q2->whereIn('dropdown_id', [1, 6, 7, 8, 9]) // 👈 trying to expand
                            ->where('value', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('userProfile_search', function ($q2) use ($keyword) {
                        $q2->where('services_offered', 'like', "%{$keyword}%");
                    });

            });
        }

        // Categories (array of values)
        if ($req->filled('locations')) {
            $query->whereHas('user_location', function ($q) use ($req) {
                $q->whereIn('value', $req->locations); // ✅ filter by categories.value
            });
        }

        if ($req->filled('categories')) {
            $query->whereIn('as_a', $req->categories); // adjust to your column
        }

        // $query->orderByRaw("
        //             CASE WHEN you_are = ? THEN 1 ELSE 0 END DESC,
        //             CASE WHEN location = ? THEN 1 ELSE 0 END DESC,
        //             created_at DESC,id ASC
        //         ", [$type, $user_location]);

        // $users = $query->cursorPaginate(3);

        // Cursor for pagination
        $cursor = $req->input('people_cursor'); // matches the key in cursorPaginate

        // Cursor pagination
        $users = $query->cursorPaginate(50, ['*'], 'people_cursor', $cursor);

        $next_page_url = $users->nextPageUrl();

        if ($req->ajax() || $req->wantsJson()) {
            // Log::info('Users query result:', $users->toArray());
            return response()->json([
                'data' => $users->isEmpty() ? [] : $users->items(), // empty array if no data
                'next_page_url' => $next_page_url,
                'next_cursor' => $users->nextCursor()?->encode(),
                'prev_cursor' => $users->previousCursor()?->encode(),
            ]);
        }

        // dd('not ajax');

        // If first load → return full view
        // $products = $query->cursorPaginate(5);
        // dd($products->all());
        return view('people.index', compact('locations', 'users', 'next_page_url'));
    }
}
