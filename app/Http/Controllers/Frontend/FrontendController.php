<?php

namespace App\Http\Controllers\Frontend;

use shurjopayv2\ShurjopayLaravelPackage8\Http\Controllers\ShurjopayController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Childcategory;
use App\Models\Product;
use App\Models\ProductVariantPrice;
use App\Models\District;
use App\Models\CreatePage;
use App\Models\Campaign;
use App\Models\Banner;
use App\Models\ShippingCharge;
use App\Models\Productcolor;
use App\Models\Productsize;
use App\Models\Customer;
use App\Models\OrderDetails;
use App\Models\Payment;
use App\Models\Order;
use App\Models\Review;
use App\Models\Contact;
use App\Models\GeneralSetting;
use App\Models\IncompleteOrder;
use Session;
use Cart;
use Auth;
use Illuminate\Support\Facades\DB;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Helpers\OrderHelper;

class FrontendController extends Controller
{
    public function index()
    {
        // General setting
        $generalsetting = GeneralSetting::where('status', 1)->limit(1)->first();

        // SEO setting
        $seo = DB::table('seo_settings')->first();

        // Main menu categories (for header/sidebar)
        $menucategories = Category::where('status', 1)
            ->where('parent_id', 0)
            ->select('id', 'name', 'slug', 'icon', 'image')
            ->with(['subcategories.childcategories'])
            ->orderBy('id', 'ASC')
            ->get();

        // Front categories (যদি অন্য কোথাও ব্যবহার হয়)
        $frontcategory = Category::where(['status' => 1])
            ->select('id', 'name', 'image', 'icon', 'slug', 'status')
            ->get();

        // Banners
        $sliders = Banner::where(['status' => 1, 'category_id' => 1])
            ->select('id', 'image', 'link')
            ->get();

        $campaognads = Banner::where(['status' => 1, 'category_id' => 7])
            ->select('id', 'image', 'link')
            ->limit(1)
            ->get();

        $sliderbottomads = Banner::where(['status' => 1, 'category_id' => 5])
            ->select('id', 'image', 'link')
            ->limit(3)
            ->get();

        $footertopads = Banner::where(['status' => 1, 'category_id' => 6])
            ->select('id', 'image', 'link')
            ->limit(3)
            ->get();

        $homepageads = Banner::where(['status' => 1, 'category_id' => 10])
            ->select('id', 'image', 'link')
            ->limit(1)
            ->get();

        $homepageads2 = Banner::where(['status' => 1, 'category_id' => 11])
            ->select('id', 'image', 'link')
            ->limit(1)
            ->get();

        $hitdealsbaner = Banner::where(['status' => 1, 'category_id' => 9])
            ->select('id', 'image', 'link')
            ->limit(1)
            ->get();

        // Flash sale – image + reviews eager load
        $flas_sales = Product::where(['status' => 1, 'flashsale' => 1])
            ->orderBy('id', 'DESC')
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'sold', 'stock')
            ->with(['prosizes', 'procolors', 'image', 'reviews'])
            ->limit(12)
            ->get();

        // Hot deal top – image + reviews eager load
        $hotdeal_top = Product::where(['status' => 1, 'topsale' => 1])
            ->orderBy('id', 'DESC')
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'stock')
            ->with(['prosizes', 'procolors', 'image', 'reviews'])
            ->limit(12)
            ->get();

        $hotdeal_bottom = Product::where(['status' => 1, 'topsale' => 1])
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'stock')
            ->with('image')
            ->skip(12)
            ->limit(12)
            ->get();

        // Category wise home products – products এর image + reviews eager load
        if ($generalsetting && $generalsetting->show_category_wise_products) {
            $homeproducts = Category::where(['front_view' => 1, 'status' => 1])
                ->orderBy('id', 'ASC')
                ->with([
                    'products' => function ($q) {
                        $q->select('id', 'name', 'slug', 'new_price', 'old_price', 'category_id')
                            ->with(['image', 'prosizes', 'procolors', 'reviews']);
                    }
                ])
                ->get()
                ->map(function ($query) {
                    // প্রতি ক্যাটাগরিতে ১২টা প্রোডাক্ট দেখাবো
                    $query->setRelation('products', $query->products->take(12));
                    return $query;
                });
        } else {
            $homeproducts = null;
        }

        $reviews = Banner::where(['status' => 1, 'category_id' => 8])
            ->select('id', 'image', 'link')
            ->limit(3)
            ->get();

        // All products – image + reviews eager load (যদি হোমে দরকার হয়)
        if ($generalsetting && $generalsetting->show_all_products) {
            $all_products = Product::where(['status' => 1])
                ->inRandomOrder()
                ->select('id', 'name', 'slug', 'new_price', 'old_price', 'sold', 'stock')
                ->with(['prosizes', 'procolors', 'image', 'reviews'])
                ->limit(30)
                ->get();
        } else {
            $all_products = null;
        }

        return view('frontEnd.layouts.pages.index', compact(
            'seo',
            'generalsetting',
            'menucategories',
            'sliders',
            'frontcategory',
            'hotdeal_top',
            'hotdeal_bottom',
            'homeproducts',
            'sliderbottomads',
            'footertopads',
            'homepageads2',
            'hitdealsbaner',
            'homepageads',
            'flas_sales',
            'campaognads',
            'reviews',
            'all_products'
        ));
    }

    // ===========================
    // Add to cart with variant + stock check
    // ===========================
    public function cartStore(Request $request)
    {
        $request->validate([
            'id'            => 'required|integer',
            'qty'           => 'nullable|integer|min:1',
            'product_color' => 'nullable|integer',
            'product_size'  => 'nullable|integer',
        ]);

        $product = Product::with('image')->findOrFail($request->id);

        // 1) প্রোডাক্টের স্টক বের করি
        $availableStock = $this->getAvailableStock($product);
        $requestedQty   = max(1, (int)($request->qty ?? 1));

        // যদি স্টকের কোন কলামই না থাকে (stock/qty/quantity নেই), তখন স্টক চেক স্কিপ করবে
        if ($availableStock !== null) {

            // স্টক ০ বা কম হলে সরাসরি ব্লক
            if ($availableStock <= 0) {
                Toastr::error('এই পণ্যটি বর্তমানে স্টক আউট, অর্ডার করা যাবে না।', 'স্টক আউট!');
                return redirect()->back()->withInput();
            }

            // কার্টে আগে থেকে একই প্রোডাক্ট (একই ভ্যারিয়েন্ট) কত qty আছে, সেটা বের করি
            $alreadyInCart = Cart::instance('shopping')
                ->search(function ($cartItem, $rowId) use ($product, $request) {
                    if ($cartItem->id != $product->id) {
                        return false;
                    }

                    $colorId = $request->product_color ?? null;
                    $sizeId  = $request->product_size ?? null;

                    return ($cartItem->options->color_id ?? null) == $colorId
                        && ($cartItem->options->size_id ?? null) == $sizeId;
                })
                ->sum('qty');

            $totalRequested = $alreadyInCart + $requestedQty;

            // স্টকের চেয়ে বেশি চাইলে error
            if ($totalRequested > $availableStock) {
                Toastr::error(
                    'স্টকে যত আছে তার বেশি অর্ডার করা যাবে না। সর্বোচ্চ ' . $availableStock . ' টি নিতে পারবেন।',
                    'স্টক সীমা!'
                );
                return redirect()->back()->withInput();
            }
        }

        // Variant price খুঁজে বের করো
        $variantPrice = ProductVariantPrice::where('product_id', $product->id)
            ->when($request->product_color, function ($q) use ($request) {
                $q->where('color_id', $request->product_color);
            })
            ->when($request->product_size, function ($q) use ($request) {
                $q->where('size_id', $request->product_size);
            })
            ->first();

        // এখন price ঠিকভাবে fallback করো
        if ($variantPrice && $variantPrice->price > 0) {
            $finalPrice = $variantPrice->price;
        } elseif (!empty($product->new_price) && $product->new_price > 0) {
            $finalPrice = $product->new_price;
        } elseif (!empty($product->old_price) && $product->old_price > 0) {
            $finalPrice = $product->old_price;
        } else {
            $finalPrice = 1; // fallback price
        }

        // সব ঠিক থাকলে এখন কার্টে Add করো
        Cart::instance('shopping')->add([
            'id'    => $product->id,
            'name'  => $product->name,
            'qty'   => $requestedQty,
            'price' => $finalPrice,
            'options' => [
                'color_id'         => $request->product_color ?? null,
                'size_id'          => $request->product_size ?? null,
                'variant_price_id' => $variantPrice->id ?? null,
                'image'            => $product->image->image ?? null,
                'slug'             => $product->slug,
                'purchase_price'   => $product->purchase_price ?? null,
            ],
        ]);

        Toastr::success('Product added to cart successfully', 'Success!');

        // order_now থাকলে checkout এ পাঠাও
        if ($request->has('order_now')) {
            return redirect()->route('customer.checkout');
        }

        return redirect()->back();
    }

    // ===========================
    // Rest of original controller methods
    // ===========================
    public function storeIncompleteOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'          => 'nullable|string|max:255',
                'phone'         => 'nullable|string|max:55',
                'address'       => 'nullable|string|max:500',
                'items'         => 'nullable|array',
                'product_image' => 'nullable|string',
                'product_link'  => 'nullable|string',
                'total_amount'  => 'nullable|numeric',
            ]);

            $total = isset($validated['total_amount']) ? floatval($validated['total_amount']) : 0;

            $incomplete = IncompleteOrder::updateOrCreate(
                [
                    'phone'   => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                ],
                [
                    'name'          => $validated['name'] ?? null,
                    'items'         => $validated['items'] ?? [],
                    'product_image' => $validated['product_image'] ?? null,
                    'product_link'  => $validated['product_link'] ?? null,
                    'total_amount'  => $total,
                ]
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Incomplete order saved successfully.',
                'data'    => $incomplete
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Incomplete order save failed: '.$e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to save incomplete order: '.$e->getMessage()
            ], 500);
        }
    }

    public function hotdeals(Request $request)
    {
        $products = Product::where(['status' => 1, 'topsale' => 1])
            ->select('id', 'name', 'slug', 'new_price', 'old_price','stock');

        if ($request->sort == 1) {
            $products = $products->orderBy('created_at', 'desc');
        } elseif ($request->sort == 2) {
            $products = $products->orderBy('created_at', 'asc');
        } elseif ($request->sort == 3) {
            $products = $products->orderBy('new_price', 'desc');
        } elseif ($request->sort == 4) {
            $products = $products->orderBy('new_price', 'asc');
        } elseif ($request->sort == 5) {
            $products = $products->orderBy('name', 'asc');
        } elseif ($request->sort == 6) {
            $products = $products->orderBy('name', 'desc');
        } else {
            $products = $products->latest();
        }

        $min_price = $products->min('new_price');
        $max_price = $products->max('new_price');
        if($request->min_price && $request->max_price){
            $products = $products->where('new_price','>=',$request->min_price);
            $products = $products->where('new_price','<=',$request->max_price);
        }
        $products = $products->paginate(36);
        return view('frontEnd.layouts.pages.hotdeals', compact('products'));
    }

    public function shop(Request $request)
    {
        $products = Product::where(['status' => 1])
            ->select('id', 'name', 'slug', 'new_price', 'old_price','stock');

        if ($request->sort == 1) {
            $products = $products->orderBy('created_at', 'desc');
        } elseif ($request->sort == 2) {
            $products = $products->orderBy('created_at', 'asc');
        } elseif ($request->sort == 3) {
            $products = $products->orderBy('new_price', 'desc');
        } elseif ($request->sort == 4) {
            $products = $products->orderBy('new_price', 'asc');
        } elseif ($request->sort == 5) {
            $products = $products->orderBy('name', 'asc');
        } elseif ($request->sort == 6) {
            $products = $products->orderBy('name', 'desc');
        } else {
            $products = $products->latest();
        }

        $min_price = $products->min('new_price');
        $max_price = $products->max('new_price');
        if($request->min_price && $request->max_price){
            $products = $products->where('new_price','>=',$request->min_price);
            $products = $products->where('new_price','<=',$request->max_price);
        }
        $products = $products->paginate(36);
        return view('frontEnd.layouts.pages.shop', compact('products'));
    }

    public function flashsales(Request $request)
    {
        $products = Product::where(['status' => 1, 'flashsale' => 1])
            ->select('id', 'name', 'slug', 'new_price', 'old_price','stock');

        if ($request->sort == 1) {
            $products = $products->orderBy('created_at', 'desc');
        } elseif ($request->sort == 2) {
            $products = $products->orderBy('created_at', 'asc');
        } elseif ($request->sort == 3) {
            $products = $products->orderBy('new_price', 'desc');
        } elseif ($request->sort == 4) {
            $products = $products->orderBy('new_price', 'asc');
        } elseif ($request->sort == 5) {
            $products = $products->orderBy('name', 'asc');
        } elseif ($request->sort == 6) {
            $products = $products->orderBy('name', 'desc');
        } else {
            $products = $products->latest();
        }

        $min_price = $products->min('new_price');
        $max_price = $products->max('new_price');
        if($request->min_price && $request->max_price){
            $products = $products->where('new_price','>=',$request->min_price);
            $products = $products->where('new_price','<=',$request->max_price);
        }
        $products = $products->paginate(36);
        return view('frontEnd.layouts.pages.flashsales', compact('products'));
    }

    public function category($slug, Request $request)
    {
        $soldShow = $request->sold=='show'?true:false;
        $category = Category::where(['slug' => $slug, 'status' => 1])->first();

        $products = Product::where(['status' => 1, 'category_id' => $category->id])
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'category_id','sold','stock');
        $subcategories = Subcategory::where('category_id', $category->id)->get();

        if ($request->sort == 1) {
            $products = $products->orderBy('created_at', 'desc');
        } elseif ($request->sort == 2) {
            $products = $products->orderBy('created_at', 'asc');
        } elseif ($request->sort == 3) {
            $products = $products->orderBy('new_price', 'desc');
        } elseif ($request->sort == 4) {
            $products = $products->orderBy('new_price', 'asc');
        } elseif ($request->sort == 5) {
            $products = $products->orderBy('name', 'asc');
        } elseif ($request->sort == 6) {
            $products = $products->orderBy('name', 'desc');
        } else {
            $products = $products->latest();
        }

        $min_price = $products->min('new_price');
        $max_price = $products->max('new_price');
        if($request->min_price && $request->max_price){
            $products = $products->where('new_price','>=',$request->min_price);
            $products = $products->where('new_price','<=',$request->max_price);
        }

        $selectedSubcategories = $request->input('subcategory', []);
        $products = $products->when($selectedSubcategories, function ($query) use ($selectedSubcategories) {
            return $query->whereHas('subcategory', function ($subQuery) use ($selectedSubcategories) {
                $subQuery->whereIn('id', $selectedSubcategories);
            });
        });

        $products = $products->paginate(24);
        return view('frontEnd.layouts.pages.category', compact('category', 'products', 'subcategories', 'min_price', 'max_price','soldShow'));
    }

    public function subcategory($slug, Request $request)
    {
        $soldShow = $request->sold=='show'?true:false;
        $subcategory = Subcategory::where(['slug' => $slug, 'status' => 1])->first();
        $products = Product::where(['status' => 1, 'subcategory_id' => $subcategory->id])
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'category_id', 'subcategory_id','sold','stock');
        $childcategories = Childcategory::where('subcategory_id', $subcategory->id)->get();

        if ($request->sort == 1) {
            $products = $products->orderBy('created_at', 'desc');
        } elseif ($request->sort == 2) {
            $products = $products->orderBy('created_at', 'asc');
        } elseif ($request->sort == 3) {
            $products = $products->orderBy('new_price', 'desc');
        } elseif ($request->sort == 4) {
            $products = $products->orderBy('new_price', 'asc');
        } elseif ($request->sort == 5) {
            $products = $products->orderBy('name', 'asc');
        } elseif ($request->sort == 6) {
            $products = $products->orderBy('name', 'desc');
        } else {
            $products = $products->latest();
        }

        $min_price = $products->min('new_price');
        $max_price = $products->max('new_price');
        if($request->min_price && $request->max_price){
            $products = $products->where('new_price','>=',$request->min_price);
            $products = $products->where('new_price','<=',$request->max_price);
        }

        $selectedChildcategories = $request->input('childcategory', []);
        $products = $products->when($selectedChildcategories, function ($query) use ($selectedChildcategories) {
            return $query->whereHas('childcategory', function ($subQuery) use ($selectedChildcategories) {
                $subQuery->whereIn('id', $selectedChildcategories);
            });
        });

        $products = $products->paginate(24);
        $impproducts = Product::where(['status' => 1, 'topsale' => 1])
            ->with('image')
            ->limit(6)
            ->select('id', 'name', 'slug')
            ->get();

        return view('frontEnd.layouts.pages.subcategory', compact('subcategory', 'products', 'impproducts', 'childcategories', 'max_price', 'min_price','soldShow'));
    }

    public function products($slug, Request $request)
    {
        $soldShow = $request->sold=='show'?true:false;
        $childcategory = Childcategory::where(['slug' => $slug, 'status' => 1])->first();
        $childcategories = Childcategory::where('subcategory_id', $childcategory->subcategory_id)->get();
        $products = Product::where(['status' => 1, 'childcategory_id' => $childcategory->id])->with('category')
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'category_id', 'subcategory_id', 'childcategory_id','sold','stock');

        if ($request->sort == 1) {
            $products = $products->orderBy('created_at', 'desc');
        } elseif ($request->sort == 2) {
            $products = $products->orderBy('created_at', 'asc');
        } elseif ($request->sort == 3) {
            $products = $products->orderBy('new_price', 'desc');
        } elseif ($request->sort == 4) {
            $products = $products->orderBy('new_price', 'asc');
        } elseif ($request->sort == 5) {
            $products = $products->orderBy('name', 'asc');
        } elseif ($request->sort == 6) {
            $products = $products->orderBy('name', 'desc');
        } else {
            $products = $products->latest();
        }

        $min_price = $products->min('new_price');
        $max_price = $products->max('new_price');
        if($request->min_price && $request->max_price){
            $products = $products->where('new_price','>=',$request->min_price);
            $products = $products->where('new_price','<=',$request->max_price);
        }

        $products = $products->paginate(24);
        $impproducts = Product::where(['status' => 1, 'topsale' => 1])
            ->with('image')
            ->limit(6)
            ->select('id', 'name', 'slug','stock')
            ->get();

        return view('frontEnd.layouts.pages.childcategory', compact('childcategory', 'products', 'impproducts', 'min_price', 'max_price', 'childcategories','soldShow'));
    }

    public function details($slug)
    {
        $details = Product::where(['slug' => $slug, 'status' => 1])
            ->with([
                'image',
                'images',
                'category',
                'subcategory',
                'childcategory',
                'variantPrices.color',
                'variantPrices.size'
            ])
            ->firstOrFail();

        $products = Product::where(['category_id' => $details->category_id, 'status' => 1])
            ->with('image')
            ->select('id', 'name', 'slug', 'new_price', 'old_price','stock')
            ->get();

        $shippingcharge = ShippingCharge::where('status', 1)->get();
        $reviews = Review::where('product_id', $details->id)
            ->where('status', 'active')
            ->latest()
            ->get();

        $productcolors = Productcolor::where('product_id', $details->id)->with('color')->get();
        $productsizes  = Productsize::where('product_id', $details->id)->with('size')->get();

        return view('frontEnd.layouts.pages.details', compact(
            'details',
            'products',
            'shippingcharge',
            'productcolors',
            'productsizes',
            'reviews'
        ));
    }

    public function quickview(Request $request)
    {
        $data['data'] = Product::where(['id' => $request->id, 'status' => 1])
            ->with('images')
            ->withCount('reviews')
            ->first();

        $data = view('frontEnd.layouts.ajax.quickview', $data)->render();
        if ($data != '') {
            echo $data;
        }
    }

    public function livesearch(Request $request)
    {
        $products = Product::select('id', 'name', 'slug', 'new_price', 'old_price','stock')
            ->where('status', 1)
            ->with('image');
        if ($request->keyword) {
            $products = $products->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->category) {
            $products = $products->where('category_id', $request->category);
        }
        $products = $products->get();

        if (empty($request->category) && empty($request->keyword)) {
            $products = [];
        }
        return view('frontEnd.layouts.ajax.search', compact('products'));
    }

    public function search(Request $request)
    {
        $products = Product::select('id', 'name', 'slug', 'new_price', 'old_price','stock')
            ->where('status', 1)
            ->with('image');
        if ($request->keyword) {
            $products = $products->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->category) {
            $products = $products->where('category_id', $request->category);
        }
        $products = $products->paginate(36);
        $keyword = $request->keyword;
        return view('frontEnd.layouts.pages.search', compact('products', 'keyword'));
    }

    public function shipping_charge(Request $request)
    {
        $shipping = ShippingCharge::find($request->id);
        if (!$shipping) {
            return response()->json(['error' => 'Shipping charge not found'], 404);
        }
        Session::put('shipping', $shipping->amount);
        return view('frontEnd.layouts.ajax.cart');
    }

    public function contact()
    {
        $contact = Contact::where('status', 1)->first();
        $cmnmenu = CreatePage::where('status', 1)->get();

        return view('frontEnd.layouts.pages.contact', compact('contact', 'cmnmenu'));
    }

    public function contactStore(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|numeric',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        \App\Models\Contact::create([
            'name'    => $request->name,
            'mobile'  => $request->phone,
            'email'   => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        $adminEmail = 'admin@example.com';
        try {
            \Mail::to($adminEmail)->send(new \App\Mail\ContactMail($request->all()));
        } catch (\Exception $e) {
            \Log::error('Email send failed: ' . $e->getMessage());
        }

        Toastr::success('✅ আপনার বার্তাটি সফলভাবে পাঠানো হয়েছে!', 'Success');
        return back();
    }

    public function page($slug)
    {
        $page = CreatePage::where('slug', $slug)->firstOrFail();
        return view('frontEnd.layouts.pages.page', compact('page'));
    }

    public function districts(Request $request)
    {
        $areas = District::where(['district' => $request->id])->pluck('area_name', 'id');
        return response()->json($areas);
    }

    public function campaign($slug)
    {
        $campaign_data = Campaign::where('slug', $slug)->with('images')->first();

        $products = Product::whereIn('id', function($query) use ($campaign_data) {
            $query->select('product_id')
                  ->from('campaign_product')
                  ->where('campaign_id', $campaign_data->id);
        })->orWhere('id', $campaign_data->product_id)
          ->where('status', 1)
          ->with('image')
          ->get();

        Cart::instance('shopping')->destroy();
        $cart_count = Cart::instance('shopping')->count();
        $product = $products->first();
        if ($cart_count == 0 && $product) {
            Cart::instance('shopping')->add([
                'id'   => $product->id,
                'name' => $product->name,
                'qty'  => 1,
                'price'=> $product->new_price,
                'options' => [
                    'slug'           => $product->slug,
                    'image'          => $product->image->image,
                    'old_price'      => $product->old_price,
                    'purchase_price' => $product->purchase_price,
                ],
            ]);
        }

        $shippingcharge = ShippingCharge::where('status', 1)->get();
        $select_charge  = ShippingCharge::where('status', 1)->first();
        if ($select_charge) {
            Session::put('shipping', $select_charge->amount);
        }

        return view('frontEnd.layouts.pages.campaign.campaign', compact('campaign_data', 'products', 'shippingcharge'));
    }

    public function payment_success(Request $request)
    {
        $order_id = $request->order_id;
        $shurjopay_service = new ShurjopayController();
        $json = $shurjopay_service->verify($order_id);
        $data = json_decode($json);

        if ($data[0]->sp_code != 1000) {
            Toastr::error('Your payment failed, try again', 'Oops!');
            return redirect()->route('home');
        }

        if ($data[0]->value1 == 'customer_payment') {
            $customer = Customer::find(Auth::guard('customer')->user()->id);

            $order = new Order();
            $order->invoice_id   = $data[0]->id;
            $order->amount       = $data[0]->amount;
            $order->customer_id  = Auth::guard('customer')->user()->id;
            $order->order_status = $data[0]->bank_status;
            $order->save();

            $payment = new Payment();
            $payment->order_id       = $order->id;
            $payment->customer_id    = Auth::guard('customer')->user()->id;
            $payment->payment_method = 'shurjopay';
            $payment->amount         = $order->amount;
            $payment->trx_id         = $data[0]->bank_trx_id;
            $payment->sender_number  = $data[0]->phone_no;
            $payment->payment_status = 'paid';
            $payment->save();

            // Order details + stock update helper
            OrderHelper::saveOrderDetails($order);

            Cart::instance('shopping')->destroy();
            Toastr::success('Thanks, Your payment send successfully', 'Success!');
            return redirect()->route('home');
        }

        Toastr::error('Something wrong, please try again', 'Error!');
        return redirect()->route('home');
    }

    public function payment_cancel(Request $request)
    {
        $order_id = $request->order_id;
        $shurjopay_service = new ShurjopayController();
        $json = $shurjopay_service->verify($order_id);
        $data = json_decode($json);

        Toastr::error('Your payment cancelled', 'Cancelled!');
        return redirect()->route('home');
    }

    public function offers()
    {
        return view('frontEnd.layouts.pages.offers');
    }

    /**
     * Helper: প্রোডাক্টের stock কলাম থেকে available স্টক বের করবে
     * products টেবিলে stock / qty / quantity – যেটা আছে সেটাই ব্যবহার করবে
     */
    protected function getAvailableStock(Product $product)
    {
        if (isset($product->stock)) {
            return (int) $product->stock;
        }

        if (isset($product->qty)) {
            return (int) $product->qty;
        }

        if (isset($product->quantity)) {
            return (int) $product->quantity;
        }

        // কোনো stock-সংক্রান্ত কলাম না থাকলে null রিটার্ন করবে
        return null;
    }
}
