<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Childcategory;
use App\Models\Product;
use App\Models\CreatePage;

class SitemapController extends Controller
{
    public function index()
    {
        return view('backEnd.sitemap.index');
    }

    public function generate()
    {
        $sitemap = Sitemap::create();

        // 1. Homepage
        $sitemap->add(Url::create('/')
            ->setPriority(1.0)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        // 2. Categories
        Category::where('status', 1)->get()->each(function (Category $category) use ($sitemap) {
            $sitemap->add(Url::create("/category/{$category->slug}")
                ->setPriority(0.8)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
        });

        // 3. Subcategories
        Subcategory::where('status', 1)->get()->each(function (Subcategory $sub) use ($sitemap) {
            $sitemap->add(Url::create("/subcategory/{$sub->slug}")
                ->setPriority(0.7)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
        });

        // 4. Childcategories
        Childcategory::where('status', 1)->get()->each(function (Childcategory $child) use ($sitemap) {
            $sitemap->add(Url::create("/products/{$child->slug}")
                ->setPriority(0.6)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
        });

        // 5. Products
        Product::where('status', 1)->get()->each(function (Product $product) use ($sitemap) {
            $sitemap->add(Url::create("/product/{$product->slug}")
                ->setPriority(0.9)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
        });

        // 6. Custom Pages
        CreatePage::where('status', 1)->get()->each(function (CreatePage $page) use ($sitemap) {
            $sitemap->add(Url::create("/page/{$page->slug}")
                ->setPriority(0.5)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));
        });

        // 7. Fixed Pages
        $sitemap->add(Url::create('/hot-deals')->setPriority(0.7));
        $sitemap->add(Url::create('/flash-sales')->setPriority(0.7));
        $sitemap->add(Url::create('/shop')->setPriority(0.7));
        $sitemap->add(Url::create('/site/contact-us')->setPriority(0.4));

        $sitemap->writeToFile(public_path('sitemap.xml'));

        return redirect()->back()->with('success', '✅ Sitemap generated successfully in the public folder!');
    }
}
