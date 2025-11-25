<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class FaceController extends Controller
{
    //
    public function face_mob()
    {
        return view('face_mob');
    }

    public function faces_json()
    {
        $faces = DB::table('user_face')->get();

        $faces_array = [];

        foreach ($faces as $face) {
            $faces_array[] = [
                'name' => $face->user_id,
                'embedding' => json_decode($face->encode),
            ];
        }

        return response()->json($faces_array);
    }
}
