<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;

class Report_cnt extends Controller
{
    public function user_report(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'message' => 'required|string',
            'f_id' => 'required|integer',
        ]);

        $userId = Auth::id();


        $exists = Report::where('type', 'user')
            ->where('f_id', $validated['f_id'])
            ->where('user_id', $userId)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'You have already reported this user.']);
        }

       
        Report::create([
            'type' => 'user',
            'f_id' => $validated['f_id'],
            'user_id' => $userId,
            'message' => $validated['message'],
            'status' => 'pending',
        ]);

        return response()->json(['success' => true,'message'=>'User Reported!']);
    }

    public function post_report(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'message' => 'required|string',
            'f_id' => 'required|integer',
        ]);

        $userId = Auth::id();

       $exists = Report::where('type', 'post')
            ->where('f_id', $validated['f_id'])
            ->where('user_id', $userId)
            ->exists();

        if($exists){
            return response()->json(['success' => false, 'message' => 'You have already reported this post.']);
        }

        Report::create([
            'type' => 'post',
            'f_id' => $validated['f_id'],
            'user_id' => $userId,
            'message' => $validated['message'],
            'status' => 'pending',
        ]);

        return response()->json(['success' => true,'message'=> 'Post Reported!']);
    }
    
}
