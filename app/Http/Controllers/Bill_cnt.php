<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Badge;
use App\Models\Chat_bot;
use App\Models\Commission;
use App\Models\GstDetails;
use App\Models\JobBoost;
use App\Models\Jobs;
use App\Models\Lead;
use App\Models\Notification;
use App\Models\OrderProducts;
use App\Models\Orders;
use App\Models\OwnedLeads;
use App\Models\Premium;
use App\Models\PremiumUser;
use App\Models\ProductBoost;
use App\Models\Products;
use App\Models\Project;
use App\Models\ReadyToWork;
use App\Models\ReadyToWorkBoost;
use App\Models\Service;
use App\Models\ServiceBoost;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Bill_cnt extends Controller
{
    // Premium Invoice
    public function premium_bill($id, Request $request)
    {
        $premium = PremiumUser::where('id', $id)->first();
        $invoiceData = [
            'invoice_no' => 'ONSTRUPRM00'.$premium->id,
            'date' => Carbon::parse($premium->created_at)->format('d-m-Y'),
            'type' => 'premium',
            'customer' => [
                'name' => $premium->user->name,
                'role' => $premium->user->as_a,
                'address' => $premium->user->user_location->value,
                'contact' => $premium->user->number,
            ],
            'company' => [
                'name' => 'Onstru',
                'admin' => 'Fasmin',
                'address' => 'Salem - 636004',
                'gst' => 'GST123456789',
                'contact' => '8124519096',
            ],
            'items' => [
                [
                    'title' => 'Premium Subscription',
                    'clicks' => 0,
                    'quantity' => 1,
                    'month' => $premium->created_at->format('F'),
                    'date' => $premium->created_at->format('d-m-Y'),
                    'price' => $premium->price / 1.18,
                ],
            ],
            'tax_percent' => 18,
        ];
        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $invoiceData], 200);
        }

        return view('bill.invoice', compact('invoiceData'));
    }

    // Product Listing Invoice
    public function product_list_bill($id, Request $request)
    {
        $product = Products::find($id);
        $boost = ProductBoost::where('product_id', $id)->where('type', 'list')->first();
        $invoiceData = [
            'invoice_no' => 'ONSTRUPROL00'.$product->id,
            'date' => Carbon::parse($product->created_at)->format('d-m-Y'),
            'type' => 'list',
            'customer' => [
                'name' => $product->vendor->name,
                'role' => $product->vendor->as_a,
                'address' => $product->vendor->user_location->value,
                'contact' => $product->vendor->number,
            ],
            'company' => [
                'name' => 'Onstru',
                'admin' => 'admin',
                'address' => 'Salem - 636004',
                'gst' => 'GST123456789',
                'contact' => '8124519096',
            ],
            'items' => [
                [
                    'title' => $product->name,
                    'clicks' => 0,
                    'quantity' => 1,
                    'date' => $boost->created_at->format('d-m-Y'),
                    'price' => $boost->amount / 1.18,
                ],
            ],
            'tax_percent' => 18,
        ];
        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $invoiceData], 200);
        }

        return view('bill.invoice', compact('invoiceData'));
    }

    // Product Click Invoice
    public function product_click_bill($id, Request $request)
    {
        $boost = ProductBoost::where('id', $id)->where('type', 'click')->first();
        $product = Products::find($boost->product_id);
        $invoiceData = [
            'invoice_no' => 'ONSTRUPROC00'.$product->id,
            'date' => Carbon::parse($product->created_at)->format('d-m-Y'),
            'type' => 'click',
            'customer' => [
                'name' => $product->vendor->name,
                'role' => $product->vendor->as_a,
                'address' => $product->vendor->user_location->value,
                'contact' => $product->vendor->number,
            ],
            'company' => [
                'name' => 'Onstru',
                'admin' => 'admin',
                'address' => 'Salem - 636004',
                'gst' => 'GST123456789',
                'contact' => '8124519096',
            ],
            'items' => [
                [
                    'title' => $product->name,
                    'clicks' => $boost->click,
                    'quantity' => 1,
                    'price' => $boost->amount / 1.18,
                ],
            ],
            'tax_percent' => 18,
        ];
        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $invoiceData], 200);
        }

        return view('bill.invoice', compact('invoiceData'));
    }

    // Service Listing Invoice
    public function service_list_bill($id, Request $request)
    {
        // dd('here');
        $service = Service::find($id);
        $boost = ServiceBoost::where('service_id', $id)->where('type', 'list')->first();
        $invoiceData = [
            'invoice_no' => 'ONSTRUSERL00'.$service->id,
            'date' => Carbon::parse($service->created_at)->format('d-m-Y'),
            'type' => 'list',
            'customer' => [
                'name' => $service->creator->name,
                'role' => $service->creator->as_a,
                'address' => $service->creator->user_location->value,
                'contact' => $service->creator->number,
            ],
            'company' => [
                'name' => 'Onstru',
                'admin' => 'admin',
                'address' => 'Salem - 636004',
                'gst' => 'GST123456789',
                'contact' => '8124519096',
            ],
            'items' => [
                [
                    'title' => $service->title,
                    'clicks' => 0,
                    'quantity' => 1,
                    'date' => $boost->created_at->format('d-m-Y'),
                    'price' => $boost->amount / 1.18,
                ],
            ],
            'tax_percent' => 18,
        ];
        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $invoiceData], 200);
        }

        return view('bill.invoice', compact('invoiceData'));
    }

    // Service Click Invoice
    public function service_click_bill($id, Request $request)
    {
        $boost = ServiceBoost::where('id', $id)->where('type', 'click')->first();
        $service = Service::find($boost->service_id);
        // dd($service);
        $invoiceData = [
            'invoice_no' => 'ONSTRUSERC00'.$service->id,
            'date' => Carbon::parse($service->created_at)->format('d-m-Y'),
            'type' => 'click',
            'customer' => [
                'name' => $service->creator->name,
                'role' => $service->creator->as_a,
                'address' => $service->creator->user_location->value,
                'contact' => $service->creator->number,
            ],
            'company' => [
                'name' => 'Onstru',
                'admin' => 'admin',
                'address' => 'Salem - 636004',
                'gst' => 'GST123456789',
                'contact' => '8124519096',
            ],
            'items' => [
                [
                    'title' => $service->title,
                    'clicks' => $boost->click,
                    'quantity' => 1,
                    'price' => $boost->amount / 1.18,
                ],
            ],
            'tax_percent' => 18,
        ];
        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $invoiceData], 200);
        }

        return view('bill.invoice', compact('invoiceData'));
    }

    // Project Listing Invoice
    public function project_list_bill($id, Request $request)
    {
        $project = Project::find($id);
        $invoiceData = [
            'invoice_no' => 'ONSTRUPRJL00'.$project->id,
            'date' => Carbon::parse($project->created_at)->format('d-m-Y'),
            'type' => 'list',
            'customer' => [
                'name' => $project->creator->name,
                'role' => $project->creator->as_a,
                'address' => $project->creator->user_location->value,
                'contact' => $project->creator->number,
            ],
            'company' => [
                'name' => 'Onstru',
                'admin' => 'admin',
                'address' => 'Salem - 636004',
                'gst' => 'GST123456789',
                'contact' => '8124519096',
            ],
            'items' => [
                [
                    'title' => $project->title,
                    'clicks' => 0,
                    'quantity' => 1,
                    'date' => $project->created_at->format('d-m-Y'),
                    'price' => $project->amount / 1.18,
                ],
            ],
            'tax_percent' => 18,
        ];
        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $invoiceData], 200);
        }

        return view('bill.invoice', compact('invoiceData'));
    }

    // Job Boost Invoice
    public function job_boost_bill($id, Request $request)
    {
        $boost = JobBoost::where('id', $id)->firstOrFail();
        $from = Carbon::parse($boost->from);
        $to = Carbon::parse($boost->to);
        $totalDays = $from->diffInDays($to) + 1;
        $job = Jobs::find($boost->job_id);

        $invoiceData = [
            'invoice_no' => 'ONSTRUJOBB00'.$job->id,
            'date' => Carbon::parse($job->created_at)->format('d-m-Y'),
            'type' => 'boost',
            'customer' => [
                'name' => $job->user->name,
                'role' => $job->user->as_a,
                'address' => $job->user->user_location->value,
                'contact' => $job->user->number,
            ],
            'company' => [
                'name' => 'Onstru',
                'admin' => 'admin',
                'address' => 'Salem - 636004',
                'gst' => 'GST123456789',
                'contact' => '8124519096',
            ],
            'items' => [
                [
                    'title' => $job->title,
                    'clicks' => 0,
                    'quantity' => 1,
                    'days' => $totalDays,
                    'price' => $boost->amount / 1.18,
                ],
            ],
            'tax_percent' => 18,
        ];
        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $invoiceData], 200);
        }

        return view('bill.invoice', compact('invoiceData'));
    }

    // Lead Owned Invoice
    public function lead_owned_bill($id, Request $request)
    {
        $lead = Lead::find($id);
        $invoiceData = [
            'invoice_no' => 'ONSTRUPRJL00'.$lead->id,
            'date' => Carbon::parse($lead->created_at)->format('d-m-Y'),
            'type' => 'own',
            'customer' => [
                'name' => $lead->user->name,
                'role' => $lead->user->as_a,
                'address' => $lead->user->user_location->value,
                'contact' => $lead->user->number,
            ],
            'company' => [
                'name' => 'Onstru',
                'admin' => 'admin',
                'address' => 'Salem - 636004',
                'gst' => 'GST123456789',
                'contact' => '8124519096',
            ],
            'items' => [
                [
                    'title' => $lead->title,
                    'clicks' => 0,
                    'quantity' => 1,
                    'price' => $lead->admin_charge,
                ],
            ],
            'tax_percent' => 18,
        ];
        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $invoiceData], 200);
        }

        return view('bill.invoice', compact('invoiceData'));
    }

    // Ready To Work Invoice
    public function readytowork_bill($id, Request $request)
    {
        $boost = ReadyToWorkBoost::where('id', $id)->firstOrFail();
        // $ready = ReadyToWork::find($boost->user_id);
        $ready = ReadyToWork::where('id', $boost->user_id)->with(['user.user_location:id,value'])->firstOrFail();
        // dd($ready);  
        $invoiceData = [
            'invoice_no' => 'ONSTRURTW00'.$ready->id,
            'date' => Carbon::parse($ready->created_at)->format('d-m-Y'),
            'type' => 'boost',
            'customer' => [
                'name' => $ready->user->name,
                'role' => $ready->user->as_a,
                'address' => $ready->user->user_location->value,
                'contact' => $ready->user->number,
            ],
            'company' => [
                'name' => 'Onstru',
                'admin' => 'admin',
                'address' => 'Salem - 636004',
                'gst' => 'GST123456789',
                'contact' => '8124519096',
            ],
            'items' => [
                [
                    'title' => 'Ready To Work',
                    'clicks' => 0,
                    'quantity' => 1,
                    'days' => $boost->days,
                    'price' => $boost->amount / 1.18,
                ],
            ],
            'tax_percent' => 18,
        ];
        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $invoiceData], 200);
        }

        return view('bill.invoice', compact('invoiceData'));
    }

    // Badges Invoice
    public function badges_bill($id, Request $request)
    {
        $badge = Badge::where('id', $id)->with(['user.user_location:id,value'])->firstOrFail();
        if ($badge->badge == 5) {
            $title = 'Titan Seller Badge';
        } elseif ($badge->badge == 10) {
            $title = 'Crown Seller Badge';
        } elseif ($badge->badge == 15) {
            $title = 'Empire Seller Badge';
        }
        $invoiceData = [
            'invoice_no' => 'ONSTRUBDG00'.$badge->id,
            'date' => Carbon::parse($badge->created_at)->format('d-m-Y'),
            'type' => 'badge',
            'customer' => [
                'name' => $badge->user->name,
                'role' => $badge->user->as_a,
                'address' => $badge->user->user_location->value,
                'contact' => $badge->user->number,
            ],
            'company' => [
                'name' => 'Onstru',
                'admin' => 'admin',
                'address' => 'Salem - 636004',
                'gst' => 'GST123456789',
                'contact' => '8124519096',
            ],
            'items' => [
                [
                    'title' => $title,
                    'clicks' => 0,
                    'quantity' => 1,
                    'date' => $badge->created_at->format('d-m-Y'),
                    'price' => $badge->amount,
                ],
            ],
            'tax_percent' => 18,
        ];
        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $invoiceData], 200);
        }

        return view('bill.invoice', compact('invoiceData'));
    }

    // Chatbot Invoice
    public function chatbot_bill($id, Request $request)
    {
        $chatbot = Chat_bot::where('id', $id)->firstOrFail();
        $invoiceData = [
            'invoice_no' => 'ONSTRUBOT00'.$chatbot->id,
            'date' => Carbon::parse($chatbot->created_at)->format('d-m-Y'),
            'type' => 'bot',
            'customer' => [
                'name' => $chatbot->user->name,
                'role' => $chatbot->user->as_a,
                'address' => $chatbot->user->user_location->value,
                'contact' => $chatbot->user->number,
            ],
            'company' => [
                'name' => 'Onstru',
                'admin' => 'admin',
                'address' => 'Salem - 636004',
                'gst' => 'GST123456789',
                'contact' => '8124519096',
            ],
            'items' => [
                [
                    'title' => 'Chatbot Subscription',
                    'clicks' => 0,
                    'quantity' => 1,
                    'date' => $chatbot->created_at->format('d-m-Y'),
                    'price' => $chatbot->amount / 1.18,
                ],
            ],
            'tax_percent' => 18,
        ];
        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $invoiceData], 200);
        }

        return view('bill.invoice', compact('invoiceData'));
    }

    // PO
    public function purchase_order(Request $request)
    {
        $order = Orders::where('order_id', $request->order_id)->first();
        $address = Address::where('id', $order->address_id)->first();
        $orderProducts = OrderProducts::where('vendor_order', $request->vendor_id)
            ->with('product.vendor')
            ->get();

        $invoiceData = [
            'invoice_no' => $order->order_id,
            'date' => $order->created_at->format('d-m-Y'),
            'address' => $address,
            'gst_billing' => $address->gst_billing,
            'billing_gst' => $address->billing_gst,
            'shipping_gst' => $address->shipping_gst,
            'type' => 'order',
            'customer' => [
                'name' => $orderProducts->first()->product->vendor->name,
                'role' => $orderProducts->first()->product->vendor->as_a,
                'address' => $orderProducts->first()->product->vendor->address,
                'contact' => $orderProducts->first()->product->vendor->number,
            ],
            'company' => [
                'name' => 'Onstru',
                'admin' => 'ADMIN',
                'address' => 'Salem - 636004',
                'gst' => 'GST123456789',
                'contact' => '8124519096',
            ],
            'items' => [],
        ];

        foreach ($orderProducts as $op) {
            $shippingExcludingTax = round($op->shipping / (1 + ($op->tax / 100)), 2);
            $invoiceData['items'][] = [
                'title' => $op->product->name,
                'category' => $op->product->categoryRelation->value,
                'commission' => Commission::where('category_id', $op->product->category)->latest()->value('commission'),
                'price' => $op->base_price,
                'cashback' => $op->cashback,
                'tax' => $op->tax,
                'quantity' => $op->quantity,
                'shipping' => $shippingExcludingTax,
            ];
        }
        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $invoiceData], 200);
        }

        return view('bill.po', compact('invoiceData'));
    }

    // Vendor Invoice
    public function vendor_invoice(Request $request)
    {
        // dd($request->all());
        $order = Orders::where('order_id', $request->order_id)->first();
        $address = Address::where('id', $order->address_id)->first();
        $orderProducts = OrderProducts::where('vendor_order', $request->vendor_id)
            ->with('product.vendor')
            ->get();

        $invoiceData = [
            'invoice_no' => $order->order_id,
            'date' => $order->created_at->format('d-m-Y'),
            'address' => $address,
            'gst_billing' => $address->gst_billing,
            'billing_gst' => $address->billing_gst,
            'shipping_gst' => $address->shipping_gst,
            'type' => 'order',
            'customer' => [
                'name' => $orderProducts->first()->product->vendor->name,
                'role' => $orderProducts->first()->product->vendor->as_a,
                'address' => $orderProducts->first()->product->vendor->address,
                'contact' => $orderProducts->first()->product->vendor->number,
            ],
            'company' => [
                'name' => 'Onstru',
                'admin' => 'ADMIN',
                'address' => 'Salem - 636004',
                'gst' => 'GST123456789',
                'contact' => '8124519096',
            ],
            'items' => [],
        ];

        foreach ($orderProducts as $op) {
            $shippingExcludingTax = round($op->shipping / (1 + ($op->tax / 100)), 2);
            $invoiceData['items'][] = [
                'title' => $op->product->name,
                'category' => $op->product->categoryRelation->value,
                'commission' => Commission::where('category_id', $op->product->category)->latest()->value('commission'),
                'price' => $op->base_price,
                'cashback' => $op->cashback,
                'margin' => $op->product->margin,
                'tax' => $op->tax,
                'quantity' => $op->quantity,
                'shipping' => $shippingExcludingTax,
            ];
        }
        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $invoiceData], 200);
        }

        return view('bill.vendorinv', compact('invoiceData'));
    }

    // Customer Invoice
    public function customer_invoice(Request $request)
    {
        $order = Orders::where('order_id', $request->order_id)->first();
        $address = Address::where('id', $order->address_id)->first();
        $orderProducts = OrderProducts::where('order_id', $request->order_id)
            ->with('product.vendor')
            ->get();

        $invoiceData = [
            'invoice_no' => $order->order_id,
            'date' => $order->created_at->format('d-m-Y'),
            'address' => $address,
            'gst_billing' => $address->gst_billing,
            'billing_gst' => $address->billing_gst,
            'shipping_gst' => $address->shipping_gst,
            'type' => 'order',
            'customer' => [
                'name' => $orderProducts->first()->product->vendor->name,
                'role' => $orderProducts->first()->product->vendor->as_a,
                'address' => $orderProducts->first()->product->vendor->address,
                'contact' => $orderProducts->first()->product->vendor->number,
            ],
            'company' => [
                'name' => 'Onstru',
                'admin' => 'ADMIN',
                'address' => 'Salem - 636004',
                'gst' => 'GST123456789',
                'contact' => '8124519096',
            ],
            'items' => [],
        ];

        foreach ($orderProducts as $op) {
            $shippingExcludingTax = round($op->shipping / (1 + ($op->tax / 100)), 2);
            $invoiceData['items'][] = [
                'title' => $op->product->name,
                'category' => $op->product->categoryRelation->value,
                'commission' => Commission::where('category_id', $op->product->category)->latest()->value('commission'),
                'price' => $op->base_price,
                'cashback' => $op->cashback,
                'margin' => $op->margin,
                'tax' => $op->tax,
                'quantity' => $op->quantity,
                'shipping' => $shippingExcludingTax,
            ];
        }
        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $invoiceData], 200);
        }

        return view('bill.vendorinv', compact('invoiceData'));
    }

    // Commission Invoice
    public function commission_invoice(Request $request)
    {
        $order = Orders::where('order_id', $request->order_id)->first();
        $address = Address::where('id', $order->address_id)->first();
        $orderProducts = OrderProducts::where('vendor_order', $request->vendor_id)
            ->with('product.vendor')
            ->get();

        $invoiceData = [
            'invoice_no' => $order->order_id,
            'date' => $order->created_at->format('d-m-Y'),
            'address' => $address,
            'type' => 'order',
            'customer' => [
                'name' => $orderProducts->first()->product->vendor->name,
                'role' => $orderProducts->first()->product->vendor->as_a,
                'address' => $orderProducts->first()->product->vendor->address,
                'contact' => $orderProducts->first()->product->vendor->number,
            ],
            'company' => [
                'name' => 'Onstru',
                'admin' => 'ADMIN',
                'address' => 'Salem - 636004',
                'gst' => 'GST123456789',
                'contact' => '8124519096',
            ],
            'items' => [],
        ];

        foreach ($orderProducts as $op) {
            $shippingExcludingTax = round($op->shipping / (1 + ($op->tax / 100)), 2);
            $invoiceData['items'][] = [
                'title' => $op->product->name,
                'category' => $op->product->categoryRelation->value,
                'commission' => Commission::where('category_id', $op->product->category)->latest()->value('commission'),
                'price' => $op->base_price,
                'cashback' => $op->cashback,
                'tax' => $op->tax,
                'quantity' => $op->quantity,
                'shipping' => $shippingExcludingTax,
            ];
        }
        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'data' => $invoiceData], 200);
        }

        return view('bill.commissioninv', compact('invoiceData'));
    }

    // Overall Invoices
    public function invoices(Request $request)
    {
        $following = Auth::user()->following()->latest('follows.created_at')->get();
        $followers = Auth::user()->followers()->latest('follows.created_at')->get();
        $gstverified = GstDetails::where('user_id', Auth::id())->where('gst_verify', 'yes')->first();
        if ($gstverified === null) {
            $gstverified = 'no';
        } else {
            $gstverified = 'yes';
        }
        $user_id = Auth::id();
        $badges = Badge::where('created_by', $user_id)->get();
        $premium = PremiumUser::where('user_id', $user_id)->get();
        $projects = Project::where('created_by', $user_id)->where('amount', '>', 0)->get();
        $products = ProductBoost::with('product')->whereHas('product', function ($query) use ($user_id) {
            $query->where('created_by', $user_id);
        })
            ->get();
        $jobs = JobBoost::with('job')->whereHas('job', function ($query) use ($user_id) {
            $query->where('created_by', $user_id);
        })
            ->get();
        $services = ServiceBoost::with('service')->whereHas('service', function ($query) use ($user_id) {
            $query->where('created_by', $user_id);
        })
            ->get()->map(function ($service_data) {
                // $service_data->service->amount = $service_data->amount;
                $service_data->service->service_type_data = $service_data->service->serviceType->value;
                unset($service_data->service->serviceType);

                return $service_data;
            });

        $leads = OwnedLeads::with('lead')->whereHas('lead', function ($query) use ($user_id) {
            $query->where('created_by', $user_id);
        })
            ->get()->map(function ($lead_data) {
                // $lead_data->lead->amount = $lead_data->amount;
                $lead_data->lead->lead_type_data = $lead_data->lead->serviceRelation->value;
                unset($lead_data->lead->serviceRelation);

                return $lead_data;
            });
        $readyToWork = ReadyToWorkBoost::where('user_id', $user_id)->get();
        $chatbots = Chat_bot::where('c_by', $user_id)->get();

        if ($request->header('Authorization')) {

            $data = [
                'badges' => $badges,
                'premium' => $premium,
                'projects' => $projects,
                'products' => $products,
                'jobs' => $jobs,
                'services' => $services,
                'leads' => $leads,
                'readyToWork' => $readyToWork,
                'chatbots' => $chatbots,
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        }

        return view('invoices.index', compact('following', 'followers', 'gstverified', 'badges', 'premium', 'projects', 'products', 'jobs', 'services', 'leads', 'readyToWork', 'chatbots'));
    }

    // Static Invoice
    // public function invoice_bill($id, Request $request)
    // {
    //     // Example static data
    //     $invoiceData = [
    //         'invoice_no' => 'ONSTRUSRV1234',
    //         'date' => 'March 4th, 2025',
    //         'customer' => [
    //             'name' => 'Zahir Khan A',
    //             'role' => 'Contractor',
    //             'address' => 'Salem - 636001',
    //             'contact' => '7447970080',
    //         ],
    //         'company' => [
    //             'name' => 'Onstru',
    //             'admin' => 'admin',
    //             'address' => 'Salem - 636004',
    //             'contact' => '8124519096',
    //         ],
    //         'items' => [
    //             [
    //                 'title' => 'Crane Service - Plumbing Type',
    //                 'clicks' => 15,
    //                 'quantity' => 1,
    //                 'price' => 100.00,
    //             ],
    //             [
    //                 'title' => 'Excavator Hire',
    //                 'clicks' => 8,
    //                 'quantity' => 2,
    //                 'price' => 250.00,
    //             ]
    //         ],
    //         'tax_percent' => 18,
    //     ];

    //     return view('bill.invoice', compact('invoiceData'));
    // }

    // Static Invoice
    // public function invoice_bill($id, Request $request)
    // {
    //     // Example static data
    //     $invoiceData = [
    //         'invoice_no' => 'ONSTRUSRV1234',
    //         'date' => 'March 4th, 2025',
    //         'customer' => [
    //             'name' => 'Zahir Khan A',
    //             'role' => 'Contractor',
    //             'address' => 'Salem - 636001',
    //             'contact' => '7447970080',
    //         ],
    //         'company' => [
    //             'name' => 'Onstru',
    //             'admin' => 'admin',
    //             'address' => 'Salem - 636004',
    //             'contact' => '8124519096',
    //         ],
    //         'items' => [
    //             [
    //                 'title' => 'Crane Service - Plumbing Type',
    //                 'clicks' => 15,
    //                 'quantity' => 1,
    //                 'price' => 100.00,
    //             ],
    //             [
    //                 'title' => 'Excavator Hire',
    //                 'clicks' => 8,
    //                 'quantity' => 2,
    //                 'price' => 250.00,
    //             ]
    //         ],
    //         'tax_percent' => 18,
    //     ];

    //     return view('bill.invoice', compact('invoiceData'));
    // }
}
