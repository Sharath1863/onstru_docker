<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Cashback;
use App\Models\Charge;
use App\Models\Click;
use App\Models\Commission;
use App\Models\DropdownList;
use App\Models\Hub;
use App\Models\Posts;
use App\Models\ProductBoost;
use App\Models\Products;
use App\Models\Review;
use App\Models\SavedProduct;
use App\Models\UserDetail;
use App\Services\Aws;
use App\Services\NotificationService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Product_cnt extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    private function notifyUser($userId, $title, $body, $category_id)
    {
        $this->notificationService->create([
            'category' => 'product',
            'category_id' => $category_id,
            'reciever' => $userId,
            'title' => $title,
            'body' => $body,
            'status' => 'active',
            'seen' => false,
            'c_by' => Auth::id(),
            'remainder' => null,
        ]);
    }

    // private function pruneAppliedCashbacks($userId)
    // {
    //     $appliedCashbacks = session()->get('appliedCashbacks', []);
    //     $activeVendors = Cart::where('c_by', $userId)->pluck('vendor_id')->unique();
    //     foreach (array_keys($appliedCashbacks) as $vendor) {
    //         if (!in_array($vendor, $activeVendors->toArray())) {
    //             unset($appliedCashbacks[$vendor]); // Remove cashback for vendors with no items left
    //         }
    //     }
    //     session()->put('appliedCashbacks', $appliedCashbacks);
    //     // dd(session()->all());
    // }
    // public function products()
    // {
    //     $category = Cache::remember('category_cache', 2, function () {
    //         return DropdownList::where('dropdown_id', 3)->get();
    //     });

    //     $locations = Cache::remember('locations_cache', 2, function () {
    //         return DropdownList::where('dropdown_id', 1)->get();
    //     });

    //     $location = Auth::user()->location ?? 0;
    //     $products = Cache::remember('products_cache', 2, function () use ($location) {
    //         return Products::withCount('reviews')
    //             ->withAvg('reviews', 'stars')
    //             ->where('status', 'active')
    //             ->where('availability', 'In Stock')
    //             ->where('approvalstatus', 'approved')
    //             ->where('created_by', '!=', Auth::id())
    //             ->where('created_at', '>=', now()->subMonths(6))
    //             ->orderByRaw("
    //         CASE
    //             WHEN highlighted = 1 AND location = '{$location}' THEN 1
    //             WHEN highlighted = 1 THEN 2
    //             WHEN location = '{$location}' THEN 3
    //             ELSE 4
    //         END, created_at DESC
    //     ")->get();
    //     });

    //     $savedProducts = [];
    //     $cartItems = [];

    //     if (Auth::check()) {
    //         $savedProducts = SavedProduct::where('c_by', Auth::id())
    //             ->pluck('product_id')
    //             ->toArray();

    //         // fetch all cart product IDs for current user
    //         $cartItems = Cart::where('c_by', Auth::id())
    //             ->pluck('product_id')
    //             ->toArray();
    //     }

    //     return view('products.products1', compact('products', 'category', 'locations', 'savedProducts', 'cartItems'));
    // }

    public function products(Request $request)
    {
        // $products = Cache::remember('products_cache_full', 60, function () {
        //     return Products::with(['categoryRelation'])->withCount('reviews')
        //         ->withAvg('reviews', 'stars')
        //         ->where('status', 'active') // only active products
        //         ->orderByDesc('created_at')
        //         ->take(1000) // fetch up to 1000
        //         ->get();
        // });

        $category = Cache::remember('category_cache', 2, function () {
            return DropdownList::where('dropdown_id', 3)->get();
        });

        $locations = Cache::remember('locations_cache', 2, function () {
            return DropdownList::where('dropdown_id', 1)->get();
        });

        $savedProducts = [];
        $cartItems = [];

        if (Auth::check()) {
            $savedProducts = SavedProduct::where('c_by', Auth::id())
                ->pluck('product_id')
                ->toArray();

            // fetch all cart product IDs for current user
            $cartItems = Cart::where('c_by', Auth::id())
                ->pluck('product_id')
                ->toArray();
        }
        $location = Auth::user()->location ?? 3;

        $query = Products::with(['categoryRelation', 'locationRelation'])
            ->withExists([
                'add_to_cart as in_cart' => function ($q) {
                    $q->where('c_by', Auth::user()->id);
                },
            ])
            ->withExists([
                'wishlist as in_wishlist' => function ($q) {
                    $q->where('c_by', Auth::user()->id);
                },
            ])
            ->withCount('reviews')
            ->withAvg('reviews', 'stars')
            ->where('status', 'active')
            ->where('created_by', '!=', Auth::id())
            ->where('approvalstatus', 'approved')
            ->where('availability', 'In Stock');

        // dd($query);
        // Keyword
        // if ($request->filled('keyword')) {
        //     $keyword = $request->keyword;
        //     $query->where(function ($q) use ($keyword) {
        //         // Search in product name
        //         $q->where('name', 'like', '%' . $keyword . '%');

        //         // Also search in related category's value
        //         $q->orWhereHas('categoryRelation', function ($q2) use ($keyword) {
        //             $q2->where('value', 'like', '%' . $keyword . '%');
        //         });
        //     });
        // }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%'.$keyword.'%')
                    ->orWhere('brand_name', 'like', '%'.$keyword.'%')
                    ->orWhere('description', 'like', '%'.$keyword.'%')
                    ->orWhere('specifications', 'like', '%'.$keyword.'%')
                    ->orWhere('key_feature', 'like', '%'.$keyword.'%')
                    ->orWhereHas('categoryRelation', function ($q2) use ($keyword) {
                        $q2->where('value', 'like', '%'.$keyword.'%');
                    });
            });
        }

        // Categories (array of values)
        if ($request->filled('categories')) {
            $categories = is_array($request->categories) ? $request->categories : [$request->categories];
            $query->whereHas('categoryRelation', function ($q) use ($categories) {
                $q->whereIn('value', $categories);
            });
        }

        // Stock status
        if ($request->filled('stock')) {
            $query->whereIn('availability', $request->stock); // adjust to your column
        }

        // Location
        if ($request->filled('locations')) {
            $locations = is_array($request->locations) ? $request->locations : [$request->locations];
            $query->whereHas('locationRelation', function ($q) use ($locations) {
                $q->whereIn('value', $locations);
            });
        }

        // Price range
        if ($request->filled('minPrice')) {
            $query->where('sp', '>=', $request->minPrice);
        }
        if ($request->filled('maxPrice')) {
            $query->where('sp', '<=', $request->maxPrice);
        }

        // Highlighted
        if ($request->filled('highlight')) {
            $query->where('highlighted', $request->highlight);
        }

        // Add custom sorting at the end
        $query->orderByRaw('
            CASE
                WHEN highlighted = 1 AND location = ? THEN 1
                WHEN highlighted = 1 THEN 2
                WHEN location = ? THEN 3
                ELSE 4
            END, created_at DESC,id ASC
        ', [$location, $location]);

        $query->orderByDesc('created_at') // ✅ secondary order
            ->orderByDesc('id');

        $cursor = $request->input('cursor'); // get cursor from query string

        $products = $query->cursorPaginate(20, ['*'], 'cursor', $cursor);

        $next_page_url = $products->nextPageUrl();

        if ($request->ajax() || $request->header('Authorization')) {
            // dd($products);
            if ($request->header('Authorization')) {
                $auth_id = Auth::user()->id;
                foreach ($products as $product) {
                    if ($auth_id != $product->created_by) {
                        $totalsp = 0;
                        $totalsp = $product->base_price + $product->cashback_price + $product->margin;
                        $taxAmount = ($totalsp * $product->tax_percentage) / 100;
                        $product->sp = $totalsp + $taxAmount;
                        // $product->sp = $product->sp + $product->cashback_price;
                        $totalmrp = $product->mrp + $product->cashback_price + $product->margin;
                        $product->mrp = ($totalmrp * $taxAmount) / 100;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $products->getCollection()->isEmpty() ? [] : $products->items(), // empty array if no data
                'next_page_url' => $next_page_url,
            ]);
        }

        // If first load → return full view
        // $products = $query->cursorPaginate(5);
        return view('products.products', compact('products', 'category', 'locations', 'savedProducts', 'cartItems', 'next_page_url'));
    }

    public function wishlist(Request $request)
    {
        $category = DropdownList::where('dropdown_id', 3)->get();
        $locations = DropdownList::where('dropdown_id', 1)->get();

        $savedProductIds = SavedProduct::where('c_by', Auth::id())
            ->pluck('product_id');

        $products = products::whereIn('id', $savedProductIds)
            ->withCount('reviews')
            ->withAvg('reviews', 'stars')
            ->withExists([
                'add_to_cart as in_cart' => function ($q) {
                    $q->where('c_by', Auth::user()->id);
                },
            ])
            ->withExists([
                'wishlist as in_wishlist' => function ($q) {
                    $q->where('c_by', Auth::user()->id);
                },
            ])
            ->latest()->get();

        $savedProducts = [];
        $cartItems = [];

        if (Auth::check()) {
            $savedProducts = $savedProductIds->toArray();
            $cartItems = Cart::where('c_by', Auth::id())
                ->pluck('product_id')
                ->toArray();
        }
        if ($request->header('Authorization')) {
            if ($request->header('Authorization')) {
                $auth_id = Auth::user()->id;
                foreach ($products as $product) {
                    if ($auth_id != $product->created_by) {
                        $totalsp = 0;
                        $totalsp = $product->base_price + $product->cashback_price + $product->margin;
                        $taxAmount = ($totalsp * $product->tax_percentage) / 100;
                        $product->sp = $totalsp + $taxAmount;
                        // $product->sp = $product->sp + $product->cashback_price;
                        $totalmrp = $product->mrp + $product->cashback_price + $product->margin;
                        $product->mrp = ($totalmrp * $taxAmount) / 100;
                    }
                }
            }

            return response()->json([
                'data' => $products->isEmpty() ? [] : $products, // empty array if no data
                'category' => $category,
                'locations' => $locations,
            ]);
        }

        return view('products.wishlist', compact('products', 'category', 'locations', 'savedProducts', 'cartItems'));
    }

    public function toggleSavedProduct(Request $request)
    {
        if (! Auth::id()) {
            return response()->json(['status' => 'unauthenticated']);
        }

        $userId = Auth::id();
        $productId = $request->input('product_id');

        $saved = SavedProduct::where('product_id', $productId)
            ->where('c_by', $userId)
            ->first();

        if ($saved) {
            $saved->delete();

            return response()->json(['success' => true, 'status' => 'removed']);
        } else {
            SavedProduct::create([
                'product_id' => $productId,
                'c_by' => $userId,
                'status' => 1,
            ]);

            return response()->json(['success' => true, 'status' => 'saved']);
        }
    }

    public function individual_product(Request $req, $id = null)
    {
        // dd('here');
        if ($req->has('product_id')) {
            $id = $req->product_id;
        }
        $product = Products::with('categoryRelation:id,value', 'locationRelation:id,value', 'hubRelation:id,hub_name', 'vendor.gst')->withCount('reviews')
            ->withAvg('reviews', 'stars')->find($id);
        $product->vendor->gst_no = $product->vendor->gst->gst_number ?? null;

        $reviews = Review::where('product_id', $id)->with('user')->get();
        $recommended_products = Products::Where('category', $product->category)
            ->where('id', '!=', $id)
            ->withCount('reviews')
            ->withAvg('reviews', 'stars')
            ->with('locationRelation:id,value')
            ->latest()
            ->get();
        $recommended_products->each(function ($product) {
            $product->isSaved = SavedProduct::where('product_id', $product->id)
                // ->where('user_id', Auth::id())
                ->exists();
        });
        $savedProducts = [];
        $cartItems = [];

        if ($product->highlighted && $product->created_by != Auth::user()->id) {
            $boostId = productBoost::where('product_id', $product->id)
                ->where('type', 'click')
                ->where('status', 'active')
                ->latest()
                ->value('id');
            $flag = Click::where('category', 'Product')
                ->where('category_id', $product->id)
                ->where('boost_id', $boostId)
                ->where('created_by', Auth::id())
                ->exists();

            if (! $flag) {
                $product->decrement('click');
                Click::create([
                    'category' => 'Product',
                    'category_id' => $product->id,
                    'boost_id' => $boostId,
                    'status' => 'active',
                    'created_by' => Auth::id() ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                if ($product->click == 0) {
                    $product->decrement('highlighted');
                    $boost = productBoost::where('product_id', $product->id)
                        ->where('type', 'click')
                        ->where('status', 'active')
                        ->latest()
                        ->first();

                    if ($boost) {
                        $boost->update(['status' => 'inactive']);
                    }
                    Posts::where('category', 'products')->where('category_id', $product->id)->update(['status' => 'inactive']);
                    $this->notifyUser(
                        $product->created_by,
                        'Product Highlights Ended',
                        'Your product "'.$product->name.'" highlighting has been expired.',
                        $product->id
                    );
                    if ($product->vendor && ($product->vendor->web_token || $product->vendor->mob_token)) {
                        $data = [
                            'web_token' => $product->vendor->web_token,
                            'mob_token' => $product->vendor->mob_token ?? null,
                            'title' => 'Product Highlights Ended',
                            'body' => 'Your product "'.$product->name.'" highlighting has been expired.',
                            'id' => $product->id,
                            'link' => route('individual-product', ['id' => $product->id]),
                        ];
                        $this->notificationService->token($data);
                    }
                }
            }
        }

        if (Auth::check()) {
            $savedProducts = SavedProduct::where('c_by', Auth::id())
                ->pluck('product_id')
                ->toArray();

            // fetch all cart product IDs for current user
            $cartItem = Cart::where('c_by', Auth::id())
                ->pluck('product_id')
                ->toArray();
        }
        $images = json_decode($product->image, true);
        $specs = json_decode($product->specifications, true);
        $trans = json_decode($product->ship_charge, true);

        if ($req->header('Authorization')) {
            unset($product->image, $product->ship_charge, $product->specifications);
            $isSaved = SavedProduct::where('product_id', $id)
                ->where('c_by', Auth::id())
                ->exists();

            $iscart = Cart::where('product_id', $id)
                ->where('c_by', Auth::id())
                ->exists();
            $product->isCart = ($iscart) ? true : false;
            $product->isSaved = $isSaved;
            if (! is_array($images)) {
                $images = [];
            }

            $newImage = $product->cover_img; // your new image string
            array_unshift($images, $newImage);

            $auth_id = Auth::user()->id;

            // dd($auth_id, $product->created_by);
            if ($auth_id != $product->created_by) {
                $totalsp = 0;
                $totalsp = $product->base_price + $product->cashback_price + $product->margin;
                $taxAmount = ($totalsp * $product->tax_percentage) / 100;
                $product->sp = $totalsp + $taxAmount;
                // $product->sp = $product->sp + $product->cashback_price;
                $product->mrp = $product->mrp + $product->cashback_price;
            }

            $product_charge_list = round(Charge::where('category', 'product_highlight')->latest()->value('charge') * 1.18, 1);

            // dd($product_charge_list);
            return response()->json([
                'success' => true,
                'product' => $product,
                'images' => $images,
                'specs' => $specs,
                'trans' => $trans,
                'reviews' => $reviews,
                'recommended_products' => $recommended_products,
                'boost_charge' => $product_charge_list,
                'highlighted' => $product->highlighted,
                // 'savedProducts' => $savedProducts,
                // 'cartItem' => $cartItem,
            ]);
        }

        return view('products.individual_product', compact('product', 'images', 'specs', 'trans', 'reviews', 'recommended_products', 'savedProducts', 'cartItem'));
    }

    public function toggleProductStatus(Request $request)
    {
        $product = Products::findOrFail($request->id);
        $product->status = $product->status === 'active' ? 'inactive' : 'active';
        $product->save();

        return response()->json([
            'success' => true,
            'new_status' => $product->status,
        ]);
    }

    // function for product category list

    public function product_category_list(Request $request)
    {
        // DD(aUTH::id());
        $category = DropdownList::where('dropdown_id', 3)->get()->map(function ($com) {

            $com->commission = (float) Commission::where('category_id', $com->id)->latest()->value('commission') ?? 0;

            return $com;
        });
        $hub_list = Hub::where('vendor_id', Auth::id())->get();
        $product_charge_list = Charge::where('category', 'product_list')->latest()->value('charge') * 1.18;

        return response()->json(['success' => true, 'category' => $category, 'hub_list' => $hub_list, 'product_charge_list' => $product_charge_list]);
    }

    public function add_product()
    {
        $category = DropdownList::where('dropdown_id', 3)->get();
        $locations = DropdownList::where('dropdown_id', 1)->get();
        $list_charge = Charge::where('category', 'product_list')->latest()->value('charge') * 1.18;
        // $commision = Charge::where('category', 'commision')->value('charge');
        $hubs = Hub::where('vendor_id', Auth::id())->get();

        return view('products.add', compact('locations', 'category', 'list_charge', 'hubs'));
    }

    public function edit_product($id)
    {
        $products = Products::find($id);
        $category = DropdownList::where('dropdown_id', 3)->get();
        $locations = DropdownList::where('dropdown_id', 1)->get();
        $hubs = Hub::where('vendor_id', Auth::id())->get();

        return view('products.edit', compact('products', 'category', 'locations', 'hubs'));
    }

    public function store(Request $request, Aws $aws)
    {
        Log::info('Request Data: '.json_encode($request->all(), JSON_PRETTY_PRINT));
        Log::info([$request->allFiles()]);

        // dd('check log');

        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'brand_name' => 'required|string',
            'category' => 'required|string',
            'd_days' => 'required|numeric',
            'd_km' => 'required|numeric',
            'availability' => 'required|string',
            'hub' => 'required|numeric',
            'mrp' => 'required|numeric',
            'sp' => 'required|numeric',
            'tax_percentage' => 'required|numeric',
            'product_unit' => 'required|string',
            'moq' => 'required|numeric',
            'base_price' => 'required|numeric',
            'transport' => 'required',
            'key_feature' => 'required|string',
            'size' => 'required|string',
            'hsn' => 'required|string',
            'description' => 'nullable|string',
            'specifications' => 'required',
            'cover_img' => $request->has('product_id') ? 'nullable|image' : 'required|image',
            'image1' => 'nullable|image',
            'image2' => 'nullable|image',
            'image3' => 'nullable|image',
            'catlogue' => 'nullable|file',
            // 'location' => 'required|numeric',
            // 'cashback_price' => 'required|numeric',
            // 'cashback_percentage' => 'nullable|numeric',
            // 'ship_method' => 'nullable|string',
            // 'ship_charge' => 'nullable|numeric',
            // 'ship_tax' => 'nullable|string',
            // 'highlighted' => 'nullable|boolean',
        ], [
            'name.required' => 'Product name is required.',
            'brand_name.required' => 'Brand name is required.',
            'category.required' => 'Category is required.',
            'd_days.required' => 'Maximum Delivery Days is required.',
            'd_km.required' => 'Maximum Distance is required.',
            'availability.required' => 'Availability is required.',
            'hub.required' => 'Location is required.',
            'mrp.required' => 'MRP is required.',
            'sp.required' => 'Selling price is required.',
            'tax_percentage.required' => 'Tax percentage is required.',
            'product_unit.required' => 'Product unit is required.',
            // 'cashback_price.required' => 'Cashback price is required.',
            'moq.required' => 'MOQ is required.',
            'key_feature.required' => 'Key features are required.',
            'size.required' => 'Size is required.',
            'hsn.required' => 'HSN code is required.',
            'cover_img.required' => 'Product Cover image is required.',
        ]);

        if ($validator->fails()) {
            if ($request->header('Authorization')) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->filled('delete_img')) {
            $product_del = Products::findOrFail($request->product_id);

            // {
            //   "delete_img": ["image1", "image3"]
            // }

            if ($product_del) {
                // Decode associative image array
                $existingImages = json_decode($product_del->image, true) ?: [];

                // Keys to delete (image1, image2, etc.)
                $keysToDelete = is_array($request->delete_img) ? $request->delete_img : [$request->delete_img];

                // Loop through the keys to delete
                foreach ($keysToDelete as $key) {
                    if (isset($existingImages[$key])) {
                        $imgPath = $existingImages[$key];

                        // Remove key    from array
                        unset($existingImages[$key]);
                    }
                }

                // ✅ Save updated JSON (no reindexing)
                $product_del->image = json_encode($existingImages);
                $product_del->save();
            }
        }

        $data = $validator->validated();
        $hub = hub::where('id', $request->hub)->first();
        $data['location'] = $hub->location_id;
        $data['hub_id'] = $hub->id;

        // Image upload
        if ($request->hasFile('cover_img')) {
            $file = $request->file('cover_img');
            if (! is_array($file)) {
                $file = [$file];
            }
            $folder = 'product_images';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $cover_img = is_array($s3Result) ? $s3Result[0] : $s3Result;
            $data['cover_img'] = $cover_img;
        }

        $images = [];

        if ($request->hasFile('image1')) {
            $file = $request->file('image1');
            if (! is_array($file)) {
                $file = [$file];
            }
            $folder = 'product_images';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $img1Name = is_array($s3Result) ? $s3Result[0] : $s3Result;
            $images['image1'] = $img1Name;
        }

        if ($request->hasFile('image2')) {
            $file = $request->file('image2');
            if (! is_array($file)) {
                $file = [$file];
            }
            $folder = 'product_images';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $img2Name = is_array($s3Result) ? $s3Result[0] : $s3Result;
            $images['image2'] = $img2Name;
        }

        if ($request->hasFile('image3')) {
            $file = $request->file('image3');
            if (! is_array($file)) {
                $file = [$file];
            }
            $folder = 'product_images';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $img3Name = is_array($s3Result) ? $s3Result[0] : $s3Result;
            $images['image3'] = $img3Name;
        }

        $specifications = $request->specifications;
        $trans = $request->transport;

        if ($request->header('Authorization')) {
            $specs = [];
            $tp = [];

            // $specifications = trim($request->specifications, '[]');

            // // 2. Split by comma into array
            // $parts = explode(',', $specifications);
            // ["nane:color", " color:blue"]
            if (is_array($specifications)) {
                // Case 1: already an array like ["Color: Grey", "Texture: Soft"]
                foreach ($specifications as $spec) {
                    if (strpos($spec, ':') !== false) {
                        [$key, $value] = explode(':', $spec, 2);
                        $specs[ucfirst(trim($key))] = trim($value);
                    }
                }

                // log::info('specifications array: '.json_encode($specifications, JSON_PRETTY_PRINT));
            } elseif (is_string($specifications)) {
                // Case 2: string like "[nane:color, color:blue]"
                $specifications = trim($specifications, '[]');   // remove [ ]
                $parts = explode(',', $specifications);

                foreach ($parts as $part) {
                    if (strpos($part, ':') !== false) {
                        [$key, $value] = explode(':', $part, 2);
                        $specs[ucfirst(trim($key))] = trim($value);
                    }
                }

                // log::info('specifications string: '.json_encode($parts, JSON_PRETTY_PRINT));
            }

            $specifications = $specs;

            $trans = trim($trans, '[]');

            // dd($trans);

            // 2. Split by comma into parts
            $parts = explode(',', (string) $trans);

            // dd($parts);

            foreach ($parts as $part) {
                // split by "-"
                $segments = explode('-', $part);
                $keys = [];

                foreach ($segments as $seg) {
                    if (strpos($seg, ':') !== false) {
                        [$key, $value] = explode(':', $seg, 2);
                        $keys[trim($key)] = trim($value);
                    }
                }

                $tp[] = $keys;
            }

            $trans = $tp;
        }
        // $data['specifications'] = json_encode($specifications);
        $data['specifications'] = json_encode($specifications);
        $data['ship_charge'] = json_encode($trans);

        if ($request->has('product_id')) {
            $existingProduct = Products::find($request->product_id);
            $existingImages = json_decode($existingProduct->image, true) ?: [];
            $images = array_merge($existingImages, $images);
        }
        $data['image'] = json_encode($images);

        if ($request->hasFile('catlogue')) {
            $file = $request->file('catlogue');
            if (! is_array($file)) {
                $file = [$file];
            }
            $folder = 'product_catalogue';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $catalogue = is_array($s3Result) ? $s3Result[0] : $s3Result;
            $data['catlogue'] = $catalogue;
        }

        $data['highlighted'] = $request->has('highlighted') ? 1 : 0;
        $data['created_by'] = Auth::id();
        $data['status'] = 'active';
        $data['approvalstatus'] = 'pending';

        if ($request->has('product_id')) {
            try {
                $product = Products::find($request->product_id);
                $product->update($data);
                // log::info('Final data to store: '.json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            } catch (\Exception $e) {
                // Log::error('Error finding product: '.$e->getMessage());
            }

            // if (!$product) {
            //     return response()->json(['error' => 'Product not found'], 404);
            // }

        } else {
            $product = Products::create($data);
            // Add listing charge only for new products
            $list_charge = Charge::where('category', 'product_list')->latest()->value('charge') * 1.18;
            UserDetail::where('id', Auth::id())
                ->where('balance', '>=', $list_charge)
                ->decrement('balance', $list_charge);

            ProductBoost::create([
                'product_id' => $product->id,
                'type' => 'list',
                'amount' => $list_charge,
                'click' => 0,
                'status' => 'active',
            ]);
        }

        // $list_charge = Charge::where('category', 'product_list')->value('charge') * 1.18;
        // UserDetail::where('id', Auth::id())
        //     ->where('balance', '>=', $list_charge)
        //     ->decrement('balance', $list_charge);

        // $product = Products::create($data);
        // ProductBoost::create([
        //     'product_id' => $product->id,
        //     'type' => 'list',
        //     'amount' => $list_charge,
        //     'click' => 0,
        //     'status' => 'active',
        // ]);

        // $spec = json_decode($model->specifications, true); // true = assoc array
        // $img = json_decode($model->image, true); // true = assoc array

        if ($request->header('Authorization')) {

            return response()->json(['success' => true, 'message' => 'Product added or updated successfully', 'data' => $img ?? 'check']);
        }

        return redirect()->route('profile')->with('success', 'Product added successfully');
    }

    public function update_product(Request $request, $id, Aws $aws)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'brand_name' => 'required|string',
            // 'category' => 'required|string',
            'd_days' => 'required|numeric',
            'd_km' => 'required|numeric',
            'availability' => 'required|string',
            'hub_id' => 'required|numeric',
            'mrp' => 'required|numeric',
            'sp' => 'required|numeric',
            'tax_percentage' => 'required|numeric',
            'product_unit' => 'required|string',
            'moq' => 'required|numeric',
            'base_price' => 'required|numeric',
            'transport' => 'required|array',
            'key_feature' => 'required|string',
            'size' => 'required|string',
            'hsn' => 'required|string',
            'description' => 'nullable|string',
            'highlighted' => 'nullable|boolean',
            'specifications' => 'nullable|array',
            'cover_img' => 'nullable|image',
            'image1' => 'nullable|image',
            'image2' => 'nullable|image',
            'image3' => 'nullable|image',
            'catlogue' => 'nullable|file',
            // 'image4' => 'nullable|image',
            // 'location' => 'required|string',
            // 'cashback_price' => 'required|numeric',
            // 'cashback_percentage' => 'nullable|numeric',
            // 'ship_method' => 'nullable|string',
            // 'ship_charge' => 'nullable|numeric',
            // 'ship_tax' => 'nullable|string',
        ], [
            'name.required' => 'Product name is required.',
            'brand_name.required' => 'Brand name is required.',
            // 'category.required' => 'Category is required.',
            'd_days.required' => 'Maximum Delivery Days is required.',
            'd_km.required' => 'Maximum Distance is required.',
            'availability.required' => 'Availability is required.',
            'hub_id.required' => 'Hub is required.',
            'mrp.required' => 'MRP is required.',
            'sp.required' => 'Selling price is required.',
            'tax_percentage.required' => 'Tax percentage is required.',
            'product_unit.required' => 'Product unit is required.',
            // 'cashback_price.required' => 'Cashback price is required.',
            'moq.required' => 'MOQ is required.',
            'key_feature.required' => 'Key features are required.',
            'size.required' => 'Size is required.',
            'hsn.required' => 'HSN code is required.',
            'cover_image.required' => 'Product Cover image is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        unset($data['image1'], $data['image2'], $data['image3'], $data['cover_img'], $data['transport']);
        $product = Products::where('id', $id)->firstOrFail();
        $cover_image = $product->cover_img;

        if ($request->hasFile('cover_img')) {
            if (! empty($cover_image)) {
                Storage::disk('s3')->delete($cover_image);
            }
            $file = $request->file('cover_img');
            if (! is_array($file)) {
                $file = [$file];
            }
            $folder = 'product_images';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $cover_img = is_array($s3Result) ? $s3Result[0] : $s3Result;
            $data['cover_img'] = $cover_img;
        }

        $images = [];

        $existingImages = json_decode($product->image, true) ?? [];

        if ($request->hasFile('image1')) {
            if (! empty($existingImages['image1'])) {
                Storage::disk('s3')->delete($existingImages['image1']);
            }

            $file = $request->file('image1');
            if (! is_array($file)) {
                $file = [$file];
            }
            $folder = 'product_images';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $img1Name = is_array($s3Result) ? $s3Result[0] : $s3Result;
            $existingImages['image1'] = $img1Name;
        }

        if ($request->hasFile('image2')) {
            if (! empty($existingImages['image2'])) {
                Storage::disk('s3')->delete($existingImages['image2']);
            }
            $file = [$request->file('image2')];
            $folder = 'product_images';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $existingImages['image2'] = is_array($s3Result) ? $s3Result[0] : $s3Result;
        }

        // Optional: Handle deletions
        if ($request->has('delete_image3')) {
            unset($existingImages['image3']);
        }

        if ($request->hasFile('image3')) {

            if (! empty($existingImages['image3'])) {
                Storage::disk('s3')->delete($existingImages['image3']);
            }
            $file = $request->file('image3');
            if (! is_array($file)) {
                $file = [$file];
            }
            $folder = 'product_images';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $img3Name = is_array($s3Result) ? $s3Result[0] : $s3Result;
            $existingImages['image3'] = $img3Name; // Replace or add image3
        }

        $specifications = $request->specifications;
        $trans = $request->transport;
        $data['ship_charge'] = $request->transport;
        $data['specifications'] = json_encode($specifications);
        $data['ship_charge'] = json_encode($trans);
        $data['image'] = json_encode($existingImages);
        // $data['image'] = json_encode($image);

        $catlogue = $product->catlogue;
        if ($request->hasFile('catlogue')) {

            if (! empty($catlogue)) {
                Storage::disk('s3')->delete($catlogue);
            }

            $file = $request->file('catlogue');
            if (! is_array($file)) {
                $file = [$file];
            }
            $folder = 'product_catalogue';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $catalogue = is_array($s3Result) ? $s3Result[0] : $s3Result;
            $data['catlogue'] = $catalogue;
        }
        $hub = hub::where('id', $request->hub_id)->first();
        $data['location'] = $hub->location_id;
        $data['highlighted'] = $request->has('highlighted') ? 1 : 0;
        $data['created_by'] = Auth::id();
        $data['approvalstatus'] = 'pending';
        Products::where('id', $id)->update($data);

        return redirect()->route('profile')->with('success', 'Product Updated Successfully');
    }

    public function checkout()
    {
        // dd(session('deliverableProducts'));
        if (session('deliverableProducts') == null) {
            return redirect()->route('cart')->with('success', 'Please enter a valid deliverable Pincode to get your order');
        }
        $userId = Auth::id();
        $address = Address::where('c_by', $userId)
            ->latest()
            ->take(3)
            ->get();

        return view('payment.checkout', compact('address'));
    }

    public function payment_process()
    {
        $userId = Auth::id();
        $cartItems = Cart::with([
            'product:id,id,name,sp,mrp,tax_percentage,d_to,cover_img,ship_charge,ship_method',
            'vendor:id,id,name',
        ])
            ->where('c_by', $userId)
            ->where('status', 'cart')
            ->get()
            ->groupBy('vendor_id');

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }
        $addresses = Address::where('c_by', Auth::id())->first();

        $available_cashback = Cashback::where('user_id', $userId)
            ->get()
            ->keyBy('vendor_id');

        return view('payment.payment_process', compact('cartItems', 'available_cashback', 'addresses'));
    }

    // public function payment_success()
    // {
    //     $userId = Auth::id();
    //     $cartItems = Cart::with('product', 'vendor')
    //         ->where('c_by', $userId)
    //         ->where('status', 'cart')
    //         ->get();

    //     if ($cartItems->isEmpty()) {
    //         return redirect()->route('cart')->with('error', 'Your cart is empty.');
    //     }

    //     $totalAmount = 0;
    //     $totalTransport = 0;
    //     $totalCashback = 0;
    //     $cashbackDetails = [];
    //     $vendorsInCart = [];

    //     foreach ($cartItems as $item) {
    //         $product = $item->product;

    //         if ($item->vendor_id) {
    //             $vendorsInCart[] = $item->vendor_id;
    //         }

    //         $totalAmount += $product->sp * $item->quantity;

    //         // if ($product->ship_method === 'Fixed') {
    //         //     $totalTransport += $product->ship_charge;
    //         // } elseif ($product->ship_method === 'Per_unit') {
    //         //     $totalTransport += $product->ship_charge * $item->quantity;
    //         // }

    //         $cashbackEarned = $product->cashback_price * $item->quantity;
    //         $cashbackDetails[$item->vendor_id] = $cashbackDetails[$item->vendor_id] ?? 0;
    //         $cashbackDetails[$item->vendor_id] += $cashbackEarned;

    //         $totalCashback += $cashbackEarned;
    //     }

    //     $vendorsInCart = array_unique($vendorsInCart);

    //     $cashbacks = Cashback::where('user_id', $userId)
    //         ->whereIn('vendor_id', $vendorsInCart)
    //         ->get();

    //     foreach ($cashbacks as $cb) {
    //         $vendorId = $cb->vendor_id;

    //         if (isset($cashbackDetails[$vendorId])) {
    //             $cb->avail_cb += $cashbackDetails[$vendorId];
    //             $cb->save();
    //         }

    //         $cb->applied_cb = 0;
    //         $cb->save();
    //     }

    //     $grandTotal = $totalAmount + $totalTransport - $totalCashback;

    //     $order = Orders::create([
    //         'order_id' => 'ORD-' . strtoupper(Str::random(8)),
    //         'user_id' => $userId,
    //         'address_id' => session('address_id'),
    //         'cashback' => json_encode($cashbackDetails),
    //         'total' => $grandTotal,
    //         'transaction_status' => 'success',
    //         'status' => 'pending',
    //     ]);

    //     foreach ($cartItems as $item) {
    //         OrderProducts::create([
    //             'order_id' => $order->order_id,
    //             'product_id' => $item->product_id,
    //             'quantity' => $item->quantity,
    //             'status' => 'pending',
    //         ]);
    //     }

    //     Cart::where('c_by', $userId)
    //         ->where('status', 'cart')
    //         ->delete();

    //     foreach ($vendorsInCart as $vendorId) {
    //         $this->notifyUser(
    //             $vendorId,
    //             'New Order Received',
    //             'You have received a new order #' . $order->order_id . ' from user ' . Auth::user()->name,
    //             $order->id
    //         );
    //     }

    //     return view('payment.payment_success', [
    //         'order' => $order,
    //         'totalAmount' => $totalAmount,
    //         'totalTransport' => $totalTransport,
    //         'totalCashback' => $totalCashback,
    //         'grandTotal' => $grandTotal,
    //         'order_details' => $order,
    //     ]);
    // }

    // public function cart()
    // {
    //     $userId = Auth::id();

    //     $cartItems = Cart::with([
    //         'product.hub',
    //         'vendor:id,id,name'
    //     ])
    //         ->where('c_by', $userId)
    //         ->where('status', 'cart')
    //         // ->orwhere('status', 'saved_for_later')
    //         ->get()
    //         ->groupBy('vendor_id');

    //     $savedItems = Cart::with([
    //         'product:id,id,name,sp,mrp,tax_percentage,d_days,cover_img,ship_charge,ship_method',
    //         'vendor:id,id,name'
    //     ])
    //         ->where('c_by', $userId)
    //         ->where('status', 'saved_for_later')
    //         ->get()
    //         ->groupBy('vendor_id');

    //     $available_cashback = Cashback::where('user_id', $userId)
    //         ->get()
    //         ->keyBy('vendor_id');

    //     $savedProducts = SavedProduct::where('c_by', $userId)
    //         ->pluck('product_id')
    //         ->toArray();
    //     return view('payment.cart', compact('cartItems', 'available_cashback', 'savedItems', 'savedProducts'));
    // }

    public function cart_store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required',
            'vendor_id' => 'required',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $cartItem = Cart::firstOrCreate(
            [
                'product_id' => $validated['product_id'],
                'c_by' => Auth::id() ?? 0,
            ],
            [
                'vendor_id' => $validated['vendor_id'],
                'quantity' => $validated['quantity'] ?? 1,
                'status' => 'cart',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'cart_id' => $cartItem->id,
        ], 200);
    }

    // public function getCartSummary(Request $request)
    // {
    //     $user = Auth::user();
    //     if (!$user) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unauthorized'
    //         ], 401);
    //     }

    //     $cartItems = Cart::with(['product', 'vendor'])
    //         ->where('c_by', $user->id)
    //         ->get()
    //         ->groupBy(function ($item) {
    //             return optional($item->vendor)->id;
    //         });

    //     $available_cashback = Cashback::where('user_id', $user->id)
    //         ->get()
    //         ->keyBy('vendor_id');

    //     $html = view('payment.cart-right', [
    //         'cartItems' => $cartItems,
    //         'available_cashback' => $available_cashback,
    //     ])->render();

    //     return response()->json([
    //         'success' => true,
    //         'html' => $html,
    //     ]);
    // }

    // public function rm_cart($id)
    // {
    //     $cartItem = Cart::find($id);

    //     if (!$cartItem || $cartItem->c_by != Auth::id()) {
    //         return response()->json(['success' => false], 403);
    //     }

    //     $cartItem->delete();

    //     return response()->json(['success' => true]);
    // }

    // public function updateQty(Request $request)
    // {
    //     $cart = Cart::with('product')->find($request->id); // load product relation too

    //     if (!$cart) {
    //         return response()->json(['success' => false, 'msg' => 'Cart item not found']);
    //     }

    //     if ($request->type === 'plus') {
    //         $cart->quantity += 1;
    //     } elseif ($request->type === 'minus') {
    //         $moq = $cart->product->moq ?? 1; // default moq is 1 if not set

    //         if ($cart->quantity > $moq) {
    //             $cart->quantity -= 1;
    //         } else {
    //             return response()->json([
    //                 'success' => false,
    //                 'msg' => "Minimum order quantity is {$moq}"
    //             ]);
    //         }
    //     }

    //     $cart->save();

    //     return response()->json([
    //         'success' => true,
    //         'quantity' => $cart->quantity,
    //         'subtotal' => $cart->quantity * $cart->product->sp,
    //         'tax_percentage' => $cart->product->tax_percentage,
    //     ]);
    // }

    public function applyCashback(Request $request)
    {
        $vendorId = $request->vendor_id;
        $appliedCb = (float) $request->cb_amount;
        $available = (float) $request->available_cb;
        $subtotal = (float) $request->subtotal;
        $shipping = (float) $request->shipping;
        // dd($vendorId, $appliedCb, $available, $subtotal, $shipping);
        // Max 25% rule
        $maxCb = 0.25 * ($subtotal + $shipping);

        // Validate applied cashback
        $appliedCb = min($appliedCb, $maxCb, $available);

        // Store in Laravel session
        $appliedCashbacks = session()->get('appliedCashbacks', []);
        $appliedCashbacks[$vendorId] = $appliedCb;
        session()->put('appliedCashbacks', $appliedCashbacks);
        // dd(session('appliedCashbacks'));

        // ---------- Calculate totals ----------
        // You may already have cart items in session or DB
        $cartItems = session()->get('cartItems', []); // or fetch from DB
        $totalSubtotal = 0;
        $totalShipping = 0;
        $totalCashback = 0;

        foreach ($cartItems as $vendor => $items) {
            $vendorSubtotal = 0;
            $vendorShipping = 0;

            foreach ($items as $item) {
                $qty = $item['quantity'] ?? 1;
                $priceWithTax = $item['price']; // price already includes tax if applicable
                $vendorSubtotal += $priceWithTax * $qty;

                $vendorShipping += $item['shipping'] ?? 0;
            }

            $totalSubtotal += $vendorSubtotal;
            $totalShipping += $vendorShipping;
            $totalCashback += $appliedCashbacks[$vendor] ?? 0;
        }

        $grandTotal = $totalSubtotal + $totalShipping - $totalCashback;

        return response()->json([
            'success' => true,
            'appliedCb' => $appliedCb,
            'totalCashback' => $totalCashback,
            'totalShipping' => $totalShipping,
            'grandTotal' => $grandTotal,
        ]);
    }

    // public function applyCashback(Request $request)
    // {
    //     $vendorId   = $request->vendor_id;
    //     $appliedCb  = (float) $request->cb_amount;
    //     $available  = (float) $request->available_cb;
    //     $subtotal   = (float) $request->subtotal;
    //     $shipping   = (float) $request->shipping;

    //     // Max 25% rule
    //     $maxCb = 0.25 * ($subtotal + $shipping);

    //     // Validate
    //     $appliedCb = min($appliedCb, $maxCb, $available);

    //     // Store in Laravel session
    //     $appliedCashbacks = session()->get('appliedCashbacks', []);
    //     $appliedCashbacks[$vendorId] = $appliedCb;
    //     session()->put('appliedCashbacks', $appliedCashbacks);

    //     return response()->json([
    //         'success' => true,
    //         'appliedCb' => $appliedCb
    //     ]);
    // }

    // public function applyCashback(Request $request)
    // {
    //     $request->validate([
    //         'vendor_id' => 'required|integer',
    //         'amount' => 'required|numeric|min:0'
    //     ]);

    //     $user = Auth::user();
    //     $vendorId = $request->vendor_id;
    //     $amount = $request->amount;
    //     $vendorTotal = $this->calculateVendorTotal($user->id, $vendorId);
    //     $maxCashback = $vendorTotal * 0.25;

    //     if ($amount > $maxCashback) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => "Cashback exceeds limit. Applied: ₹{$amount}, Max allowed1: ₹{$vendorTotal}"
    //         ]);
    //     }

    //     $cashback = Cashback::firstOrNew([
    //         'user_id' => $user->id,
    //         'vendor_id' => $vendorId
    //     ]);

    //     if ($amount > $cashback->avail_cb) {
    //         return response()->json(['success' => false, 'message' => 'Insufficient cashback balance.']);
    //     }

    //     $cashback->applied_cb = $amount;
    //     $cashback->save();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Cashback applied successfully.',
    //         'applied_cb' => $amount,
    //         'vendor_id' => $vendorId,
    //     ]);
    // }

    // private function calculateVendorTotal($userId, $vendorId)
    // {
    //     $items = Cart::where('vendor_id', $vendorId)
    //         ->whereHas('product', function ($q) use ($vendorId) {
    //             $q->where('created_by', $vendorId);
    //         })->get();

    //     return $items->sum(function ($item) {
    //         return $item->product->sp * $item->quantity;
    //     });
    // }

    // public function removeItem($id)
    // {
    //     $cartItem = Cart::findOrFail($id);
    //     if ($cartItem->c_by !== Auth::id()) {
    //         abort(403);
    //     }
    //     $cartItem->delete();

    //     return response()->json(['message' => 'Item removed']);
    // }

    public function address_store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'primary_phone' => 'required|string|max:15',
            'secondary_phone' => 'nullable|string|max:15',
            'gst_billing' => 'nullable|in:yes,no',
            // 'pre_booking' => 'nullable|in:yes,no',
            'billing_address' => 'required|string',
            'billing_pincode' => 'required|string',
            'billing_city' => 'required|string',
            'billing_state' => 'required|string',
            'billing_gst' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'shipping_pincode' => 'nullable|string',
            'shipping_city' => 'nullable|string',
            'shipping_state' => 'nullable|string',
            'shipping_gst' => 'nullable|string',
            'same_as_billing' => 'nullable|boolean',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        // If same_as_billing is checked, copy billing to shipping
        if ($request->has('same_as_billing')) {
            $validated['shipping_address'] = $validated['billing_address'];
            $validated['shipping_pincode'] = $validated['billing_pincode'];
            $validated['shipping_city'] = $validated['billing_city'];
            $validated['shipping_state'] = $validated['billing_state'];
        }
        // dd('here');
        $validated['c_by'] = Auth::id() ?? 0;

        $address = Address::create($validated);
        $addressId = $address->id;

        $validated['latitude'] = $validated['latitude'];
        $validated['longitude'] = $validated['longitude'];
        $validated['c_by'] = Auth::id() ?? 0;
        session(['address_data' => $validated]);
        // $address = Address::create($validated);
        // $addressId = $address->id;

        // if ($request->header('Authorization')) {
        //     return response()->json(['success' => true, 'message' => 'Address added successfully'], 201);
        // }
        // session(['address_id' => $addressId]);

        return redirect()
            ->route('order-summary')
            ->with('success', 'Address saved successfully!');
    }

    public function getAddress($id)
    {
        $address = Address::where('id', $id)
            ->where('c_by', Auth::id())
            ->firstOrFail();

        return response()->json($address);
    }

    // public function updateStatus(Request $request, $id)
    // {
    //     $status = $request->input('status');

    //     $cartItem = Cart::find($id);
    //     if (!$cartItem) {
    //         return response()->json(['success' => false, 'message' => 'Item not found']);
    //     }

    //     $cartItem->status = $status;
    //     $cartItem->save();

    //     return response()->json(['success' => true]);
    // }

    // public function saveForLater(Request $request)
    // {
    //     $cart = Cart::findOrFail($request->id);
    //     $cart->status = 'save_for_later'; // New column in cart table to mark saved items
    //     $cart->save();

    //     return response()->json(['success' => true]);
    // }

    // public function moveToCart(Request $request)
    // {
    //     $cart = Cart::findOrFail($request->id);
    //     $cart->status = 'cart'; // Back to cart
    //     $cart->save();

    //     return response()->json(['success' => true]);
    // }

    public function review(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'review' => 'required|string|max:500',
            'rating' => 'required|integer|min:1|max:5',
        ]);
        Review::create([
            'product_id' => $request->product_id,
            'c_by' => Auth::id(),
            'review' => $request->review,
            'stars' => $request->rating,
        ]);

        return response()->json(['success' => true, 'message' => 'Review posted successfully!']);
    }

    public function boost_product(Request $request, Aws $aws)
    {
        $request->validate([
            'product_id' => 'required',
            'video' => 'nullable',
            'click' => 'required',
        ]);
        $video = null;
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            if (! is_array($file)) {
                $file = [$file];
            }
            $folder = 'product_video';
            $videoResult = $aws->common_upload_to_s3($file, $folder);
            $video = is_array($videoResult) ? $videoResult : [$videoResult];
        }

        $product_day_charge = Charge::where('category', 'product_highlight')->latest()->value('charge') * 1.18;
        $total_amount = $product_day_charge * $request->click;

        UserDetail::where('id', Auth::id())
            ->where('balance', '>=', $total_amount)
            ->decrement('balance', $total_amount);

        productBoost::create([
            'product_id' => $request->product_id,
            'type' => 'click',
            'amount' => $total_amount,
            'click' => $request->click,
            'status' => 'active',
        ]);

        $product = Products::where('id', $request->product_id)->first();

        Posts::create([
            'file_type' => 'image',
            'category' => 'products',
            'category_id' => $request->product_id,
            'file' => [$product->cover_img],
            'caption' => $product->description,
            'location' => $product->location,
            'created_by' => Auth::id(),
            'status' => 'active',
        ]);

        if ($video != null) {
            Posts::create([
                'file_type' => 'video',
                'category' => 'products',
                'category_id' => $request->product_id,
                'file' => $video,
                'caption' => $product->description,
                'location' => $product->location,
                'created_by' => Auth::id(),
                'status' => 'active',
            ]);
        }

        if ($video != null) {
            $product->update([
                'video' => $video,
            ]);
        }

        Products::where('id', $request->product_id)->update(['highlighted' => 1, 'click' => $request->click]);

        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'message' => 'Product Highlighted successfully!'], 200);
        }

        return redirect()->back()->with('success', 'Product Highlighted successfully!');
    }

    public function hubStore(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'hubname' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_id' => 'required|string|max:255',
            // 'place_id'           => 'nullable|string|max:255',
            // 'address_components' => 'nullable|json',
        ]);

        Hub::create([
            'vendor_id' => Auth::id(),
            'hub_name' => $request->hubname,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_id' => $request->location_id,
            // 'place_id'          => $request->place_id,
            // 'address_components' => $request->address_components,
        ]);

        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'message' => 'Hub added successfully.'], 200);
        }

        return redirect()->back()->with('success', 'Hub added successfully.');
    }

    public function hubUpdate(Request $request)
    {
        $validated = $request->validate([
            'hubs' => 'required|integer|exists:hubs,id',
            'hubname' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'place_id' => 'nullable|string|max:255',
            'address_components' => 'nullable|json',
        ]);

        Hub::findOrFail($validated['hubs'])->update([
            'hub_name' => $validated['hubname'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'pincode' => $validated['pincode'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'place_id' => $validated['place_id'],
            'address_components' => $validated['address_components'],
        ]);

        return back()->with('success', 'Hub updated successfully.');
    }

    public function getCommissionByCategory($categoryId)
    {
        // Fetch the commission based on the category ID
        $commission = Commission::where('category_id', $categoryId)
            ->where('status', 'active')
            ->first();

        if ($commission) {
            return response()->json([
                'success' => true,
                'commission' => $commission->commission, // Assuming 'percentage' is the column name for commission percentage
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No commission found for this category.',
        ]);
    }

    private function getCartData($userId)
    {
        $cartItems = Cart::with([
            'product.hub',
            'vendor:id,id,name',
        ])
            ->where('c_by', $userId)
            ->where('status', 'cart')
            ->get()
            ->groupBy('vendor_id');

        $savedItems = Cart::with([
            'product.hub',
            'vendor:id,id,name',
        ])
            ->where('c_by', $userId)
            ->where('status', 'saved_for_later')
            ->get()
            ->groupBy('vendor_id');

        $available_cashback = Cashback::where('user_id', $userId)
            ->get()
            ->keyBy('vendor_id');

        $savedProducts = SavedProduct::where('c_by', $userId)
            ->pluck('product_id')
            ->toArray();

        return compact('cartItems', 'savedItems', 'available_cashback', 'savedProducts');
    }

    public function cart()
    {
        $userId = Auth::id();
        $data = $this->getCartData($userId);

        return view('payment.cart', $data);
    }

    public function updateQuantity(Request $request)
    {
        $itemId = $request->input('item_id');
        $action = $request->input('action');

        $cartItem = Cart::with('product')->find($itemId);

        if (! $cartItem) {
            return response()->json(['success' => false, 'msg' => 'Cart item not found']);
        }

        // Adjust quantity
        if ($action === 'plus') {
            $cartItem->quantity += 1;
        } elseif ($action === 'minus') {
            $moq = $cartItem->product->moq ?? 1;
            if ($cartItem->quantity > $moq) {
                $cartItem->quantity -= 1;
            } else {
                return response()->json([
                    'success' => false,
                    'msg' => "Minimum order quantity is {$moq}",
                ]);
            }
        }
        $vendorId = $cartItem->vendor_id;
        $cartItem->save();
        $appliedCashbacks = session()->get('appliedCashbacks', []);
        $appliedCashbacks[$vendorId] = 0;
        session()->put('appliedCashbacks', $appliedCashbacks);
        // $this->pruneAppliedCashbacks(Auth::id());
        $userId = Auth::id();
        $data = $this->getCartData($userId);

        return response()->json([
            'cart_left' => view('payment.cart-left', $data)->render(),
            'cart_right' => view('payment.cart-right', $data)->render(),
        ]);
    }

    public function removeFromCart(Request $request)
    {

        $itemId = $request->input('item_id');

        $cartItem = Cart::find($itemId);
        if (! $cartItem) {
            return response()->json(['success' => false, 'msg' => 'Cart item not found']);
        }
        $vendorId = $cartItem->vendor_id;
        $cartItem->delete();

        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'msg' => 'Item removed from cart'], 200);
        }
        $appliedCashbacks = session()->get('appliedCashbacks', []);
        $appliedCashbacks[$vendorId] = 0;
        session()->put('appliedCashbacks', $appliedCashbacks);
        $userId = Auth::id();
        $data = $this->getCartData($userId);

        return response()->json([
            'success' => true,
            'msg' => 'Item removed from cart',
            'cart_left' => view('payment.cart-left', $data)->render(),
            'cart_right' => view('payment.cart-right', $data)->render(),
        ]);
    }

    public function saveForLater(Request $request)
    {
        $itemId = $request->input('item_id');

        $cartItem = Cart::find($itemId);
        if (! $cartItem) {
            return response()->json(['success' => false, 'msg' => 'Cart item not found']);
        }
        // $vendorId = $cartItem->vendor_id;
        $cartItem->status = 'saved_for_later';
        $cartItem->save();
        session()->forget('appliedCashbacks');
        session()->save();
        // $appliedCashbacks = session()->get('appliedCashbacks', []);
        // $appliedCashbacks[$vendorId] = 0;
        // session()->put('appliedCashbacks', $appliedCashbacks);

        $userId = Auth::id();
        $data = $this->getCartData($userId);

        return response()->json([
            'success' => true,
            'msg' => 'Item moved to Saved for Later',
            'cart_left' => view('payment.cart-left', $data)->render(),
            'cart_right' => view('payment.cart-right', $data)->render(),
        ]);
    }

    public function moveToCart(Request $request)
    {
        $itemId = $request->input('item_id');

        $cartItem = Cart::find($itemId);
        if (! $cartItem) {
            return response()->json(['success' => false, 'msg' => 'Cart item not found']);
        }

        $cartItem->status = 'cart';
        $cartItem->save();

        $userId = Auth::id();
        $data = $this->getCartData($userId);

        return response()->json([
            'success' => true,
            'msg' => 'Item moved to Cart',
            'cart_left' => view('payment.cart-left', $data)->render(),
            'cart_right' => view('payment.cart-right', $data)->render(),
        ]);
    }

    public function storeDeliverables(Request $request)
    {
        $deliverables = $request->input('deliverables', []);

        // Save to session
        session(['pincode' => $request->pincode]);
        session(['deliverableProducts' => $deliverables]);

        return response()->json(['success' => true]);
    }

    public function getDrivingDistance(Request $request)
    {

        if ($request->header('Authorization')) {

            log::info('API Request Received', $request->all());

            $product = $request->product_id;

            // Replace with the pincode you want to look up
            // $pincode = '600001'; // Example: Chennai, India Pincode
            $pincode = $request->pincode;

            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $pincode,
                'key' => 'AIzaSyAmDotOMSahWwA7WAjmtremPvNNRN1Nye0', // Ensure you have your key in .env
            ]);

            $data = $response->json();

            if ($data['status'] === 'OK' && ! empty($data['results'])) {
                $location = $data['results'][0]['geometry']['location'];

                $destLat = $location['lat'];
                $destLng = $location['lng'];

                // log::info('Latitude: '.$destLat.', Longitude: '.$destLng);

                // Output:
                // echo "Latitude: $latitude, Longitude: $longitude";

            }

            // $destLat = $request->buyer_lat;
            // $destLng = $request->buyer_lng;

            $distanceKm = 0;

            foreach (($product) as $key => $value) {
                $hub = Products::where('id', $value)->first();

                $hubDetails = Hub::where('id', $hub->hub_id)->first();

                $originLat = $hubDetails->latitude;
                $originLng = $hubDetails->longitude;

                $apiKey = 'AIzaSyAmDotOMSahWwA7WAjmtremPvNNRN1Nye0';
                $origin = "{$originLat},{$originLng}";
                $destination = "{$destLat},{$destLng}";

                $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                    'origins' => $origin,
                    'destinations' => $destination,
                    'mode' => 'driving',
                    'key' => $apiKey,
                ]);

                $data = $response->json();

                if (
                    isset($data['rows'][0]['elements'][0]['status']) &&
                    $data['rows'][0]['elements'][0]['status'] === 'OK'
                ) {
                    $distanceMeters = $data['rows'][0]['elements'][0]['distance']['value'];
                    $distanceKm = round($distanceMeters / 1000, 0);

                    // return response()->json([
                    //     'success' => true,
                    //     'distance_km' => $distanceKm,
                    // ]);

                    $responseData[] = [
                        'product_id' => $value,
                        'distance_km' => $distanceKm,
                        'delivery' => ($distanceKm <= $hub->d_km) ? 'deliverable' : 'not deliverable',
                        'shipping' => $hub->ship_charge,
                        'tax' => $hub->tax_percentage,
                    ];
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unable to retrieve driving distance.',
                        'raw' => $data,
                    ], 422);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $responseData,
            ]);
        }

        // web
        $originLat = $request->input('hubLat');
        $originLng = $request->input('hubLng');
        $destLat = $request->input('buyerLat');
        $destLng = $request->input('buyerLng');

        if (! $originLat || ! $originLng || ! $destLat || ! $destLng) {
            return response()->json([
                'success' => false,
                'message' => 'Missing coordinates.',
            ], 400);
        }

        $apiKey = 'AIzaSyAmDotOMSahWwA7WAjmtremPvNNRN1Nye0';
        $origin = "{$originLat},{$originLng}";
        $destination = "{$destLat},{$destLng}";

        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins' => $origin,
            'destinations' => $destination,
            'mode' => 'driving',
            'key' => $apiKey,
        ]);

        $data = $response->json();

        if (
            isset($data['rows'][0]['elements'][0]['status']) &&
            $data['rows'][0]['elements'][0]['status'] === 'OK'
        ) {
            $distanceMeters = $data['rows'][0]['elements'][0]['distance']['value'];
            $distanceKm = round($distanceMeters / 1000, 0);

            return response()->json([
                'success' => true,
                'distance_km' => $distanceKm,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve driving distance.',
                'raw' => $data,
            ], 422);
        }
    }

    public function gst_verify(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'gst_no' => 'required|string|max:15',
        ]);

        $gst_number = strtoupper(trim($request->gst_no));

        $group_id = 'ec33ab9a-6ebb-46a7-b87d-3966673f1214';
        $task_id = '8abc6431-fc08-4594-bc8d-090df206f15c';
        $api_key = 'b4bf8650-cd17-43a4-9139-1eb7a6c5ca83';
        $account_id = '6f29b17f07fd/d2aecc09-60b4-452b-ba48-debf18233d3e';

        $client = new Client;

        try {
            // 1️⃣ Initiate async task
            $payload = [
                'group_id' => $group_id,
                'task_id' => $task_id,
                'data' => [
                    'gstnumber' => $gst_number,
                    'isdetails' => true,
                ],
            ];

            $response = $client->post(
                'https://eve.idfy.com/v3/tasks/async/retrieve/gst_info',
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'api-key' => $api_key,
                        'account-id' => $account_id,
                    ],
                    'json' => $payload,
                ]
            );

            $data = json_decode($response->getBody()->getContents(), true);
            $requestId = $data['request_id'] ?? null;

            if (! $requestId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get request ID.',
                ]);
            }

            // 2️⃣ Wait for async processing
            sleep(7); // Allow Idfy to complete the verification task

            // 3️⃣ Fetch GST verification result
            $response2 = $client->get(
                'https://eve.idfy.com/v3/tasks',
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'api-key' => $api_key,
                        'account-id' => $account_id,
                    ],
                    'query' => ['request_id' => $requestId],
                ]
            );

            $result = json_decode($response2->getBody()->getContents(), true);

            if (! empty($result) && isset($result[0]['result'])) {
                if ($result[0]['result']['status'] == 'id_found') {
                    return response()->json([
                        'success' => true,
                        'data' => $result[0]['result'],
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'GST verification pending or failed.',
                'data' => $result ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // polling method
    // public function startVerification(Request $request)
    // {
    //     $request->validate(['gst_no' => 'required|string']);

    //     $payload = [
    //         'group_id' => 'ec33ab9a-6ebb-46a7-b87d-3966673f1214',
    //         'task_id' => '8abc6431-fc08-4594-bc8d-090df206f15c',
    //         'data' => [
    //             'gstnumber' => $request->gst_no,
    //             'isdetails' => true,
    //         ],
    //     ];

    //     try {
    //         $client = new Client();
    //         $response = $client->post('https://eve.idfy.com/v3/tasks/async/retrieve/gst_info', [
    //             'headers' => [
    //                 'Accept' => 'application/json',
    //                 'Content-Type' => 'application/json',
    //                 'api-key' => 'b4bf8650-cd17-43a4-9139-1eb7a6c5ca83',
    //                 'account-id' => '6f29b17f07fd/d2aecc09-60b4-452b-ba48-debf18233d3e',
    //             ],
    //             'json' => $payload,
    //         ]);

    //         $data = json_decode($response->getBody(), true);
    //         return response()->json([
    //             'success' => true,
    //             'request_id' => $data['request_id'] ?? null,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    // public function checkStatus(Request $request)
    // {
    //     $request->validate(['request_id' => 'required|string']);

    //     try {
    //         $client = new Client();
    //         $response = $client->get('https://eve.idfy.com/v3/tasks', [
    //             'headers' => [
    //                 'Accept' => 'application/json',
    //                 'Content-Type' => 'application/json',
    //                 'api-key' => 'b4bf8650-cd17-43a4-9139-1eb7a6c5ca83',
    //                 'account-id' => '6f29b17f07fd/d2aecc09-60b4-452b-ba48-debf18233d3e',
    //             ],
    //             'query' => [
    //                 'request_id' => $request->request_id,
    //             ],
    //         ]);

    //         $data = json_decode($response->getBody(), true);
    //         $task = $data['tasks'][0] ?? null;

    //         if ($task && isset($task['status']) && $task['status'] === 'completed') {
    //             return response()->json([
    //                 'success' => true,
    //                 'status' => 'completed',
    //                 'data' => $task['result']['result']['gst_info'] ?? [],
    //             ]);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'status' => $task['status'] ?? 'pending',
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'status' => 'failed',
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }

    // function for product created list

    public function product_created_by()
    {
        $products = Products::with('categoryRelation:id,value')->where('created_by', Auth::id())->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    // public function updatePrice(Request $request)
    // {
    //     $request->validate([
    //         'productId' => 'required|numeric|exists:products,id',
    //         'mrp' => 'required|numeric',
    //         'sp' => 'required|numeric',
    //         'baseprice' => 'required|numeric',
    //     ]);

    //     $product = Products::find($request->productId);
    //     if ($product) {
    //         $product->mrp = $request->mrp;
    //         $product->sp = $request->sp;
    //         $product->base_price = $request->baseprice;
    //         $product->save();

    //         if ($request->header('Authorization')) {

    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Product Price updated successfully',
    //             ], 200);
    //         }

    //         return redirect()->back()->with('success', 'Product prices updated successfully');
    //     }
    //     return redirect()->back()->with('error', 'Product not found');
    // }

    public function updatePrice(Request $request)
    {
        $request->validate([
            'productId' => 'required|numeric|exists:products,id',
            'mrp' => 'required|numeric',
            'sp' => 'required|numeric',
            'baseprice' => 'required|numeric',
        ]);

        $product = Products::find($request->productId);

        if (! $product) {
            return redirect()->back()->with('error', 'Product not found');
        }

        // --- Capture old values ---
        $oldMrp = intval($product->mrp);
        $oldBase = intval($product->base_price);

        // --- Capture new values ---
        $newMrp = intval($request->mrp);
        $newBase = intval($request->baseprice);
        $newSp = intval($request->sp);

        // --- Calculate allowed 20% range (integer) ---
        $mrpMin = intval(round($oldMrp * 0.8));
        $mrpMax = intval(round($oldMrp * 1.2));
        $baseMin = intval(round($oldBase * 0.8));
        $baseMax = intval(round($oldBase * 1.2));

        // --- Determine if approval is needed ---
        $requiresApproval = false;

        if ($newMrp < $mrpMin || $newMrp > $mrpMax) {
            $requiresApproval = true;
        }

        if ($newBase < $baseMin || $newBase > $baseMax) {
            $requiresApproval = true;
        }

        // --- Update product ---
        $product->mrp = $newMrp;
        $product->sp = $newSp;
        $product->base_price = $newBase;

        // --- Update status based on approval need ---
        if ($product->approvalstatus == 'approved') {
            $product->approvalstatus = $requiresApproval ? 'pending' : 'approved';
        }

        $product->save();

        // --- API or web response ---
        if ($request->header('Authorization')) {
            return response()->json([
                'success' => true,
                'message' => $requiresApproval
                    ? 'Price updated successfully. Awaiting admin approval.'
                    : 'Product price updated successfully.',
                'requires_approval' => $requiresApproval,
            ], 200);
        }

        return redirect()->back()->with(
            'success',
            $requiresApproval
                ? 'Product price updated successfully but requires admin approval.'
                : 'Product price updated successfully.'
        );
    }
}
