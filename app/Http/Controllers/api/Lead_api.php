<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Lead;
use App\Models\owned_leads;
use App\Models\OwnedLeads;
use App\Models\LeadReview;

class Lead_api extends Controller
{
    // function to add lead request
    public function add_lead_request(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'service_id' => 'required|integer',
                'build_area' => 'required|decimal:0,2',
                'start_date' => 'required|date|after_or_equal:today',
                'budget' => 'required|numeric',
                'location' => 'nullable|string',
                // 'contact' => 'nullable|string',
                // 'mail' => 'nullable|email',
                'description' => 'nullable|string',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $lead =  Lead::insert([

            'service_type' => $request->service_id,
            'buildup_area' => $request->build_area,
            'budget' => $request->budget,
            'start_date' => $request->start_date,
            'location' => $request->location,
            'description' => $request->description,
            // 'approval_status' => 0, // default to pending
            'created_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Lead request added successfully'], 201);
    }

    // function to get created leads
    /*
    public function get_created_leads(Request $request)
    {
        $leads = Lead::with(['user:id,name', 'serviceRelation:id,value'])
            ->where('created_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $own_leads = OwnedLeads::where('created_by', Auth::id())->pluck('lead_id')->toArray();

        $own = Lead::with(['user:id,name', 'serviceRelation:id,value'])
            ->whereIn('id', $own_leads)
            ->orderBy('created_at', 'desc')
            ->get();


        return response()->json(['leads' => $leads, 'own' => $own], 200);
    }
        */

    // function to get lead list
    public function lead_list_api(Request $request)
    {
        $leads = Lead::with(['user:id,name,user_name,number,email', 'serviceRelation:id,value'])
            ->where('approval_status', 'approved') // only approved leads
            ->where('created_by', '!=', Auth::id()) // exclude leads created by the user
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['leads' => $leads], 200);
    }


    // function to view lead details
    public function lead_view(Request $req)
    {
        try {
            $lead = Lead::with('locationRelation')
                ->with('serviceRelation')
                ->where('id', $req->lead_id)
                ->firstOrFail();
            return response()->json(['success' => true, 'data' => $lead], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lead not found'], 404);
        }
    }
    public function get_created_leads(Request $req)
    {

        $id = Auth::id();
        $leads = Lead::where('created_by', $id)
        ->withCount('reviews')
        ->withAvg('reviews', 'stars')
        ->with('locationRelation')
        ->with('serviceRelation')
        ->latest()
        ->get();
        if($leads){
            
                return response()->json([
                    'success' => true,
                    'Lead list' => $leads,
                ], 200);  
        }
        else {
            return response()->json([
                'status' => 'true',
                'message' => 'Lead not found',
            ], 500);
        }
    }

    //View individual Leads
    public function lead_profile_api(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'lead_id' => 'required|exists:leads,id'],

                [
                'lead_id.required'  => 'lead id is required.',               
          ]);
          $id = Auth::id();
          $lead = Lead::with('locationRelation')
          ->with('serviceRelation')
          ->where('id', $request->lead_id)
          ->where('created_by',$id)
          ->withCount('reviews')
          ->withAvg('reviews', 'stars')
          ->firstOrFail();

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        if($lead){
            
            return response()->json([
                'success' => true,
                'Lead details' => $lead,
            ], 200);  
        }
        else {
            return response()->json([
                'status' => 'true',
                'message' => 'Lead not found',
            ], 500);
        }
    }

    //buy lead
    public function view_lead_profile(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'lead_id' => 'required|exists:leads,id'],

                [
                'lead_id.required'  => 'lead id is required.',               
          ]);
          if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
          $id = Auth::id();
          //dd($id);
          $lead = Lead::with('locationRelation')
          ->with('serviceRelation')
          ->where('id', $request->lead_id)
          ->where('created_by', '!=', $id)
          ->first();

       
        if(!$lead){
            
            return response()->json([
              'success' => true,
                'message' => 'Lead not found',
            ], 200);  
        }
        else {
            return response()->json([
                'status' => true,
                'lead details' => $lead,
            ], 200);
        }
    }


    public function owned_lead_profile(Request $request)
{ 
    //dd(Auth::id());
    $owned = OwnedLeads::where('created_by', Auth::id())
        ->where('lead_id', $request->lead_id)
        ->first();

    $lead = $owned
        ? Lead::where('id', $owned->lead_id)
            ->with('user') // eager load user
            ->with('locationRelation')
            ->with('serviceRelation')
            ->latest()
            ->first()
        : null;
    $userReviews = LeadReview::where('c_by', Auth::id())
            ->where('lead_id', $owned->lead_id)
            ->exists();
           

    if (!$owned) {
        return response()->json([
            'success' => false,
            'message' => 'Lead not found or not owned by user',
        ], 404);
    } else if (!$lead) {
        return response()->json([
            'success' => false,
            'message' => 'Lead data missing',
        ], 404);
    } else {
        return response()->json([
            'success' => true,
            'lead' => $lead, // Return actual lead with user
            'userReviews'=> $userReviews
        ], 200);
    }
}

//repost lead fetch
    public function repost_lead_fetch(Request $request)
    { 
    

          $lead=Lead::where('id', $request->lead_id)
              
                ->where('created_by',Auth::id())
                ->with('locationRelation')
                ->with('serviceRelation')
                ->latest()
                ->first();
            
             if (!$lead) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lead data missing',
                ], 404);
            } else {
                return response()->json([
                    'success' => true,
                    'lead' => $lead, // Return actual lead with user
                    
                ], 200);
            }
    }
}
