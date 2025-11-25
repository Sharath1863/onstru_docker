<?php

namespace App\Http\Controllers;

use App\Models\DropdownList;
use App\Models\Lead;
use App\Models\LeadReview;
use App\Models\Notification;
use App\Models\OwnedLeads;
use App\Models\ReadyToWork;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Leads_cnt extends Controller
{
    public function leads(Request $request)
    {
        $location = Auth::user()->location ?? 0;
        $userId = Auth::id();
        $ownedLeadIds = OwnedLeads::where('created_by', $userId)->pluck('lead_id');
        $leads = Cache::remember('leads_cache_' . $userId, 2, function () use ($location, $ownedLeadIds) {
            return Lead::where('approval_status', 'approved')->withCount('reviews')
                ->withAvg('reviews', 'stars')
                ->where('created_by', '!=', Auth::id())
                ->where('status', 'active')
                ->where('created_at', '>=', now()->subMonths(3))
                ->whereNotIn('id', $ownedLeadIds)
                ->orderByRaw("location = '{$location}' DESC")
                ->orderBy('created_at', 'DESC')
                ->with('locationRelation')
                ->with('serviceRelation')
                ->get();
        });
        $serviceTypes = DropdownList::where('dropdown_id', 5)->pluck('value', 'id');
        $locations = DropdownList::where('dropdown_id', 1)->pluck('value', 'id');
        if ($request->header('Authorization')) {
            return response()->json([

                'success' => true,
                'leads list' => $leads,
                'tax_amount' => 1.18,
                'locations' => $locations,
            ], 200);
        }
        return view('leads.index', compact('leads', 'serviceTypes', 'locations'));
    }

    public function storeLeads(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'service_type' => 'required|string|max:255',
                'buildup_area' => 'required|numeric',
                'budget' => 'required|numeric',
                'start_date' => 'required|date',
                'location' => 'required|string|max:255',
                'description' => 'required|string',
            ]);
            Lead::create([
                'title' => $request->title,
                'service_type' => $request->service_type,
                'buildup_area' => $request->buildup_area,
                'budget' => $request->budget,
                'start_date' => $request->start_date,
                'location' => $request->location,
                'description' => $request->description,
                'created_by' => $request->user_id ?? Auth::id(),
            ]);

            if ($request->header('Authorization')) {
                return response()->json(['success' => true, 'message' => 'Lead added Successfully'], 200);
            }
            return redirect()->back()->with('success', 'Lead Added Successfully!');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lead not found'], 404);
        }
    }

    public function updateLeads(Request $request, $id = null)
    {
        try {
            $lead_id = $id ?? $request->lead_id;
            $request->validate([
                'title' => 'required|string|max:255',
                'service_type' => 'required|string|max:255',
                'buildup_area' => 'required|numeric',
                'budget' => 'required|numeric',
                'start_date' => 'required|date',
                'location' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $lead = Lead::where('id', $lead_id)->where('created_by', Auth::id())->firstOrFail();
            if (!$lead) {
                if ($request->header('Authorization')) {
                    return response()->json(['success' => false, 'message' => 'Lead not found'], 200);
                }
                return redirect()->back()->with('success', 'Lead Updated Successfully!');
            }

            $lead->update([
                'title' => $request->title,
                'service_type' => $request->service_type,
                'buildup_area' => $request->buildup_area,
                'budget' => $request->budget,
                'start_date' => $request->start_date,
                'location' => $request->location,
                'description' => $request->description,
            ]);

            if ($request->header('Authorization')) {
                return response()->json(['success' => true, 'message' => 'Lead Updated Successfully!'], 200);
            }
            return redirect()->back()->with('success', 'Lead Updated Successfully!');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lead not found'], 404);
        }
    }

    public function editLeads(Request $request, $id = null)
    {
        try {
            if ($request->lead_id) {
                $id = $request->lead_id;
            }
            $lead = Lead::with('locationRelation')
                ->with('serviceRelation')
                ->where('id', $id)
                ->where('created_by', Auth::id())
                ->firstOrFail();
            if ($request->header('Authorization')) {
                return response()->json(['success' => true, 'Leads details' => $lead], 200);
            }
            return response()->json($lead);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lead not found'], 404);
        }
    }

    public function destroyLeads($id)
    {
        $lead = Lead::where('id', $id)->where('created_by', Auth::id())->firstOrFail();
        $lead->delete();
        return redirect()->back()->with('success', 'Lead Deleted Successfully!');
    }

    public function hire(Request $request)
    {
        // Fetch all active ready to work entries with user relationship
        // $location = Auth::user()->location ?? 0;
        // $hires = ReadyToWork::where('status', 'active')
        //     ->with('user')
        //     ->orderByRaw("JSON_CONTAINS(locations, '\"{$location}\"') DESC")
        //     ->orderBy('created_at', 'DESC')
        //     ->get()
        //     ->map(function ($readyToWork) {
        //         return [
        //             'id' => $readyToWork->id,
        //             'name' => $readyToWork->user->name ?? 'N/A',
        //             'profile_img' => $readyToWork->user->profile_img ?? 'N/A',
        //             'category' => $readyToWork->job_titles_text,
        //             'experience' => $readyToWork->experience,
        //             'location' => $readyToWork->locations_text, // Using the accessor method
        //             'type' => $readyToWork->work_types_text,
        //             'payments' => $readyToWork->payments_text, // Using the accessor method
        //             'resume' => $readyToWork->resume_path,
        //             'user_id' => $readyToWork->created_by
        //         ];
        //     });
        // dd($hires);

        $userLocation = (string) (Auth::user()->location ?? 0);
        $locations = Cache::remember('locations_cache', 2, function () {
            return DropdownList::where('dropdown_id', 1)->get();
        });
        $category = Cache::remember('category_cache', 2, function () {
            return DropdownList::where('dropdown_id', 4)->get();
        });

        $hires = ReadyToWork::where('status', 'active')
            ->with('user')
            ->orderByRaw("JSON_CONTAINS(locations, '\"$userLocation\"') DESC")
            ->orderBy('created_at', 'DESC')
            ->get()
            ->map(function ($readyToWork) {
                $signedResumeUrl = $readyToWork->resume_path
                    ? Storage::disk('s3')->temporaryUrl(
                        $readyToWork->resume_path,
                        now()->addMinutes(10),
                        ['ResponseContentDisposition' => 'attachment; filename="' . ($readyToWork->user->name ?? 'resume') . '_resume.pdf"']
                    )
                    : null;

                return [
                    'id' => $readyToWork->id,
                    'name' => $readyToWork->user->name ?? 'N/A',
                    'profile_img' => $readyToWork->user->profile_img ?? 'default.png',
                    'category' => $readyToWork->job_titles_text,
                    'experience' => $readyToWork->experience,
                    'location' => DropdownList::whereIn('id', $readyToWork->locations ?? [])
                        ->pluck('value')
                        ->implode(', '),
                    'type' => $readyToWork->work_types_text,
                    'payments' => $readyToWork->payments_text ?? '',
                    'resume' => $readyToWork->resume_path,
                    'signed_resume_url' => $signedResumeUrl,
                    'user_id' => $readyToWork->created_by,
                ];
            });

        if ($request->header('Authorization')) {
            return response()->json([
                'success' => true,
                'message' => 'Hire List',
                'hires' => $hires,
                'location' => $locations,
                'category' => $category,
                // 'job_id' => $job->id,

            ], 200);
        }
        return view('hire.index', compact('hires', 'locations', 'category'));
    }

    public function owned(Request $request)
    {
        $owned = OwnedLeads::where('created_by', Auth::id())->get();
        $leadIds = $owned->pluck('lead_id');
        $leads = $leadIds->isNotEmpty()
            ? Lead::whereIn('id', $leadIds)
                ->with('user')
                ->latest()
                ->get()
            : collect();
        $userReviews = LeadReview::where('c_by', Auth::id())
            ->whereIn('lead_id', $owned->pluck('lead_id'))
            ->get()
            ->keyBy('lead_id');
        $serviceTypes = DropdownList::where('dropdown_id', 5)->pluck('value', 'id');
        $locations = DropdownList::where('dropdown_id', 1)->pluck('value', 'id');
        if ($request->header('Authorization')) {
            return response()->json([
                'success' => true,
                'owned lead list' => $leads,
                'serviceTypes' => $serviceTypes,
                'location' => $locations,
                'owned lead list' => $leads,
                'serviceTypes' => $serviceTypes,
                'location' => $locations,

            ], 200);
        }
        return view('leads.owned', compact('leads', 'serviceTypes', 'locations', 'userReviews'));
    }

    public function buyLead(Request $request, $id = null)
    {
        if ($request->lead_id) {
            $id = $request->lead_id;
        }
        $userId = Auth::id();
        $lead = Lead::find($id);
        if (!$lead) {
            if ($request->header('Authorization')) {
                return response()->json([
                    'message' => 'Lead not found.',
                    'error' => false
                ], 500);
            }

            return redirect()->back()->with('error', 'Lead not found.');
        }
        $alreadyOwned = OwnedLeads::where('lead_id', $id)->where('created_by', $userId)->exists();
        if ($alreadyOwned) {
            if ($request->header('Authorization')) {
                return response()->json([
                    'message' => 'You have already purchased this lead.',
                    'error' => false
                ], 500);
            }

            return redirect()->back()->with('error', 'You have already purchased this lead.');
        }

        $userDetail = UserDetail::where('id', $userId)->first();

        if (!$userDetail || $userDetail->balance < $lead->admin_charge) {
            if ($request->header('Authorization')) {
                return response()->json([
                    'message' => 'Insufficient balance.',
                    'error' => false
                ], 500);
            }

            return redirect()->back()->with('error', 'Insufficient balance.');
        }
        $adminCharge = $lead->admin_charge;
        $gst = $adminCharge * 0.18;
        $totalCharge = $adminCharge + $gst;

        $userDetail->decrement('balance', $totalCharge);
        OwnedLeads::create([
            'lead_id' => $id,
            'created_by' => $userId,
        ]);
        Lead::where('id', $id)->update([
            'status' => 'inactive',
        ]);
        if ($request->header('Authorization')) {
            return response()->json([
                'message' => 'Lead purchased successfully!',
                'success' => true
            ], 200);
        }

        return redirect()->route('leads.owned')->with('success', 'Lead purchased successfully!');
    }

    public function review(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
        ]);
        LeadReview::create([
            'lead_id' => $request->lead_id,
            'c_by' => Auth::id(),
            'review' => $request->review,
            'stars' => $request->rating,
        ]);

        if ($request->header('Authorization')) {
            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully!',

            ]);
        }
        return response()->json(['success' => 'Review Posted Successfully', 'lead_id' => $request->lead_id,]);
    }

    public function repostLeads(Request $request, $id = null)
    {
        if ($request->lead_id) {
            $id = $request->lead_id;
        }
        $request->validate([
            'start_date' => 'required|date',
        ]);

        $lead = Lead::where('id', $id)->where('created_by', Auth::id())->firstOrFail();

        $lead->update([
            'start_date' => $request->start_date,
            'repost' => $lead->repost + 1,
            'status' => 'active',
            'approval_status' => 'pending',
        ]);

        if ($request->header('Authorization')) {
            return response()->json([
                'success' => true,
                'message' => 'Lead Reposted successfully!',
            ]);
        }
        return redirect()->back()->with('success', 'Lead Reposted Successfully!');
    }
}