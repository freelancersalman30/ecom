<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Productimage;
use App\Models\Productcolor;
use App\Models\Productsize;
use App\Models\ProductVariantPrice;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Childcategory;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Size;
use Toastr;
use File;
use Illuminate\Support\Str;
use DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
        $this->middleware('permission:product-create', ['only' => ['create','store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    // ================================
    // AJAX: SUBCATEGORY
    // ================================
    public function getSubcategory(Request $request)
    {
        $sub = DB::table("subcategories")
            ->where("category_id", $request->category_id)
            ->pluck('subcategoryName', 'id');

        return response()->json($sub);
    }

    // ================================
    // AJAX: CHILDCATEGORY
    // ================================
    public function getChildcategory(Request $request)
    {
        $child = DB::table("childcategories")
            ->where("subcategory_id", $request->subcategory_id)
            ->pluck('childcategoryName', 'id');

        return response()->json($child);
    }

    // ================================
    // INDEX
    // ================================
    public function index(Request $request)
    {
        $query = Product::orderBy('id','DESC')->with('image','category');

        if ($request->keyword) {
            $query->where('name', 'LIKE', '%' . $request->keyword . "%");
        }

        $data = $query->paginate(50);
        return view('backEnd.product.index', compact('data'));
    }

    // ================================
    // CREATE
    // ================================
    public function create()
    {
        return view('backEnd.product.create', [
            'categories' => Category::where('parent_id', 0)->where('status', 1)->select('id', 'name')->with('childrenCategories')->get(),
            'brands'     => Brand::where('status', 1)->select('id', 'name')->get(),
            'colors'     => Color::where('status', 1)->get(),
            'sizes'      => Size::where('status', 1)->get(),
        ]);
    }

    // ================================
    // STORE
    // ================================
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'           => 'required',
            'category_id'    => 'required',
            'new_price'      => 'required',
            'purchase_price' => 'required',
            'stock'          => 'required',
            'description'    => 'required',
            'advance_amount' => 'nullable|numeric|min:0',

            'product_type'        => 'required|in:physical,digital',
            'digital_file'        => 'nullable|file|max:51200', // 50MB
            'download_limit'      => 'nullable|integer|min:1',
            'download_expire_days'=> 'nullable|integer|min:1',
        ]);

        $last_id = Product::max('id') + 1;

        // proSize, proColor, image, meta_image, variant_price, digital_file বাদ
        $input = $request->except([
            'image',
            'meta_image',
            'variant_price',
            'digital_file',
            'proSize',
            'proColor',
        ]);

        foreach ($input as $key => $val) {
            if (is_array($val)) {
                $input[$key] = implode(',', $val);
            }
        }

        // PRODUCT TYPE
        $isDigital = $request->product_type === 'digital';
        $input['is_digital'] = $isDigital ? 1 : 0;

        if ($isDigital) {
            $input['advance_amount'] = 0; // ডিজিটাল হলে advance লাগবে না
        } else {
            $input['advance_amount'] = $request->advance_amount ?? 0;
        }

        // Slug & video
        $input['slug']      = strtolower(preg_replace('/[\/\s]+/', '-', $request->name.'-'.$last_id));
        $input['pro_video'] = $this->getYouTubeVideoId($request->pro_video);

        // Status flags
        $input['status']          = $request->status ? 1 : 0;
        $input['topsale']         = $request->topsale ? 1 : 0;
        $input['feature_product'] = $request->feature_product ? 1 : 0;
        $input['product_code']    = 'P' . str_pad($last_id, 4, '0', STR_PAD_LEFT);

        // SEO
        $input['meta_title']       = $request->meta_title ?? $request->name;
        $input['meta_description'] = $request->meta_description ?? Str::limit(strip_tags($request->description), 160);
        $input['meta_keywords']    = $request->meta_keywords ?? '';

        // META IMAGE UPLOAD
        if ($request->hasFile('meta_image')) {
            $metaImg  = $request->file('meta_image');
            $metaName = time().'-meta-'.$metaImg->getClientOriginalName();
            $metaPath = 'public/uploads/product/meta/';
            $metaImg->move($metaPath, $metaName);
            $input['meta_image'] = $metaPath.$metaName;
        }

        // DIGITAL FILE UPLOAD
        if ($isDigital) {
            $input['download_limit']       = $request->download_limit ?? 5;
            $input['download_expire_days'] = $request->download_expire_days ?? 7;

            if ($request->hasFile('digital_file')) {
                $file = $request->file('digital_file');
                // storage/app/private/digital-products/...
                $path = $file->store('digital-products', 'private');
                $input['digital_file'] = $path;
            } else {
                $input['digital_file'] = null;
            }
        } else {
            $input['digital_file']        = null;
            $input['download_limit']      = null;
            $input['download_expire_days']= null;
        }

        // CREATE PRODUCT
        $product = Product::create($input);

        // Colors & Sizes (nullable হলে attach হবে না)
        if ($request->proSize) {
            $product->sizes()->attach($request->proSize);
        }
        if ($request->proColor) {
            $product->colors()->attach($request->proColor);
        }

        // PRODUCT IMAGES
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $img) {
                $name = time().'-'.$img->getClientOriginalName();
                $name = strtolower(preg_replace('/\s+/', '-', $name));
                $path = 'public/uploads/product/';
                $img->move($path, $name);

                Productimage::create([
                    'product_id' => $product->id,
                    'image'      => $path.$name,
                ]);
            }

            // যদি meta_image সেট করা না থাকে, প্রথম ইমেজকে meta_image করো
            if (empty($product->meta_image) && $product->images()->first()) {
                $product->update(['meta_image' => $product->images()->first()->image]);
            }
        }

        // VARIANT PRICES
        if ($request->variant_price && is_array($request->variant_price)) {
            foreach ($request->variant_price as $variant) {
                ProductVariantPrice::create([
                    'product_id' => $product->id,
                    'color_id'   => $variant['color_id'] ?? null,
                    'size_id'    => $variant['size_id'] ?? null,
                    'price'      => $variant['price'] ?? 0,
                    'stock'      => $variant['stock'] ?? 0,
                ]);
            }
        }

        Toastr::success('Product created successfully!');
        return redirect()->route('products.index');
    }

    // ================================
    // EDIT
    // ================================
    public function edit($id)
    {
        $edit = Product::with(['images','variantPrices'])->findOrFail($id);

        return view('backEnd.product.edit', [
            'edit_data'     => $edit,
            'categories'    => Category::where('parent_id', 0)->where('status', 1)->with('childrenCategories')->get(),
            'subcategory'   => Subcategory::where('category_id', $edit->category_id)->get(),
            'childcategory' => Childcategory::where('subcategory_id', $edit->subcategory_id)->get(),
            'brands'        => Brand::where('status', 1)->get(),
            'totalsizes'    => Size::where('status', 1)->get(),
            'totalcolors'   => Color::where('status', 1)->get(),
            'selectcolors'  => Productcolor::where('product_id', $id)->get(),
            'selectsizes'   => Productsize::where('product_id', $id)->get(),
        ]);
    }

    // ================================
    // UPDATE
    // ================================
    public function update(Request $request)
    {
        $this->validate($request, [
            'name'           => 'required',
            'category_id'    => 'required',
            'new_price'      => 'required',
            'purchase_price' => 'required',
            'stock'          => 'required',
            'description'    => 'required',

            'product_type'        => 'required|in:physical,digital',
            'digital_file'        => 'nullable|file|max:51200',
            'download_limit'      => 'nullable|integer|min:1',
            'download_expire_days'=> 'nullable|integer|min:1',
        ]);

        $product = Product::findOrFail($request->id);

        $input = $request->except([
            'image',
            'meta_image',
            'variant_price',
            'digital_file',
            'proSize',
            'proColor',
        ]);

        foreach ($input as $key => $val) {
            if (is_array($val)) {
                $input[$key] = implode(',', $val);
            }
        }

        // PRODUCT TYPE
        $isDigital = $request->product_type === 'digital';
        $input['is_digital'] = $isDigital ? 1 : 0;

        if ($isDigital) {
            $input['advance_amount'] = 0;
        } else {
            $input['advance_amount'] = $request->advance_amount ?? 0;
        }

        // Slug & flags
        $input['slug']            = strtolower(preg_replace('/[\/\s]+/', '-', $request->name.'-'.$product->id));
        $input['status']          = $request->status ? 1 : 0;
        $input['topsale']         = $request->topsale ? 1 : 0;
        $input['feature_product'] = $request->feature_product ? 1 : 0;
        $input['pro_video']       = $this->getYouTubeVideoId($request->pro_video);

        // SEO
        $input['meta_title']       = $request->meta_title ?? $request->name;
        $input['meta_description'] = $request->meta_description ?? $request->description;
        $input['meta_keywords']    = $request->meta_keywords ?? '';

        // META IMAGE UPDATE
        if ($request->hasFile('meta_image')) {
            if ($product->meta_image && file_exists($product->meta_image)) {
                @unlink($product->meta_image);
            }
            $metaImg  = $request->file('meta_image');
            $metaName = time().'-meta-'.$metaImg->getClientOriginalName();
            $metaPath = 'public/uploads/product/meta/';
            $metaImg->move($metaPath, $metaName);
            $input['meta_image'] = $metaPath.$metaName;
        }

        // DIGITAL FILE UPDATE
        if ($isDigital) {
            $input['download_limit']       = $request->download_limit ?? $product->download_limit ?? 5;
            $input['download_expire_days'] = $request->download_expire_days ?? $product->download_expire_days ?? 7;

            if ($request->hasFile('digital_file')) {
                // পুরনো ফাইল ডিলিট
                if ($product->digital_file && Storage::disk('private')->exists($product->digital_file)) {
                    Storage::disk('private')->delete($product->digital_file);
                }

                $file = $request->file('digital_file');
                $path = $file->store('digital-products', 'private');
                $input['digital_file'] = $path;
            } // নতুন ফাইল না দিলে digital_file আগেরটাই থাকবে (update-এ key না পাঠালে unchanged)
        } else {
            // এখন যদি physical করে দাও, তাহলে digital ইনফো ডিলিট
            if ($product->digital_file && Storage::disk('private')->exists($product->digital_file)) {
                Storage::disk('private')->delete($product->digital_file);
            }
            $input['digital_file']        = null;
            $input['download_limit']      = null;
            $input['download_expire_days']= null;
        }

        // PRODUCT UPDATE
        $product->update($input);

        // SIZE & COLOR
        $product->sizes()->sync($request->proSize ?? []);
        $product->colors()->sync($request->proColor ?? []);

        // NEW IMAGES
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $img) {
                $name = time().'-'.$img->getClientOriginalName();
                $name = strtolower(preg_replace('/\s+/', '-', $name));
                $path = 'public/uploads/product/';
                $img->move($path, $name);

                Productimage::create([
                    'product_id' => $product->id,
                    'image'      => $path.$name,
                ]);
            }
        }

        // VARIANTS UPDATE
        ProductVariantPrice::where('product_id', $product->id)->delete();

        if ($request->variant_price && is_array($request->variant_price)) {
            foreach ($request->variant_price as $variant) {
                if (empty($variant['color_id']) && empty($variant['size_id'])) continue;

                ProductVariantPrice::create([
                    'product_id' => $product->id,
                    'color_id'   => $variant['color_id'] ?? null,
                    'size_id'    => $variant['size_id'] ?? null,
                    'price'      => $variant['price'] ?? 0,
                    'stock'      => $variant['stock'] ?? 0,
                ]);
            }
        }

        Toastr::success('Product updated successfully!');
        return redirect()->route('products.index');
    }

    // ================================
    // DELETE / IMAGE DELETE
    // ================================
    public function destroy(Request $request)
    {
        $product = Product::findOrFail($request->hidden_id);

        // digital ফাইল থাকলে ডিলিট
        if ($product->digital_file && Storage::disk('private')->exists($product->digital_file)) {
            Storage::disk('private')->delete($product->digital_file);
        }

        $product->delete();
        Toastr::success('Product deleted successfully');
        return redirect()->back();
    }

    public function imgdestroy(Request $request)
    {
        $img = Productimage::findOrFail($request->id);
        File::delete($img->image);
        $img->delete();

        Toastr::success('Image deleted successfully!');
        return redirect()->back();
    }

    // ================================
    // YOUTUBE VIDEO HELPER
    // ================================
    private function getYouTubeVideoId($input)
    {
        if (!$input) return null;

        // শুধু ১১ ক্যারেক্টারের ID হলে
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) {
            return $input;
        }

        // পূর্ণ URL হলে
        preg_match(
            '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
            $input,
            $matches
        );

        return $matches[1] ?? null;
    }
}
