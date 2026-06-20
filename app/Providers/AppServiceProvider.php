<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

use App\Models\GeneralSetting;
use App\Models\Category;
use App\Models\Brand;
use App\Models\SocialMedia;
use App\Models\Contact;
use App\Models\CreatePage;
use App\Models\OrderStatus;
use App\Models\EcomPixel;
use App\Models\GoogleTagManager;
use App\Models\Order;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Review;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * 🟢 Super Admin Override (যাতে Admin সব পারমিশন পায়)
         */
        Gate::before(function (User $user, $ability) {
            return $user->hasRole('Admin') ? true : null;
        });

        /**
         * 🧩 Shurjopay Dynamic Config
         */
        $shurjopay = PaymentGateway::where(['status' => 1, 'type' => 'shurjopay'])->first();
        if ($shurjopay) {
            Config::set([
                'shurjopay.apiCredentials.username'   => $shurjopay->username,
                'shurjopay.apiCredentials.password'   => $shurjopay->password,
                'shurjopay.apiCredentials.prefix'     => $shurjopay->prefix,
                'shurjopay.apiCredentials.return_url' => $shurjopay->success_url,
                'shurjopay.apiCredentials.cancel_url' => $shurjopay->return_url,
                'shurjopay.apiCredentials.base_url'   => $shurjopay->base_url,
            ]);
        }

        /**
         * 🧠 Global View Share (header, footer, sidebar)
         */
		 
		  $pending_reviews = Review::where('status', 'pending')->count();
    view()->share('pending_reviews', $pending_reviews); 
        $generalsetting = GeneralSetting::where('status', 1)->first();
        view()->share('generalsetting', $generalsetting);

        $sidecategories = Category::where('parent_id', 0)->where('status', 1)
            ->select('id', 'name', 'slug', 'status', 'image')->get();
        view()->share('sidecategories', $sidecategories);

        $menucategories = Category::where('status', 1)
            ->select('id', 'name', 'slug', 'status', 'image')->get();
        view()->share('menucategories', $menucategories);

        $contact = Contact::where('status', 1)->first();
        view()->share('contact', $contact);

        $socialicons = SocialMedia::where('status', 1)->get();
        view()->share('socialicons', $socialicons);

        $pages = CreatePage::where('status', 1)->limit(3)->get();
        view()->share('pages', $pages);

        $pagesright = CreatePage::where('status', 1)->skip(1)->limit(5)->get();
        view()->share('pagesright', $pagesright);

        $cmnmenu = CreatePage::where('status', 1)->get();
        view()->share('cmnmenu', $cmnmenu);

        $brands = Brand::where('status', 1)->get();
        view()->share('brands', $brands);

        $neworder = Order::where('order_status', 1)->count();
        view()->share('neworder', $neworder);

        $pendingorder = Order::where('order_status', 1)->latest()->limit(9)->get();
        view()->share('pendingorder', $pendingorder);

        $orderstatus = OrderStatus::get();
        view()->share('orderstatus', $orderstatus);

        $pixels = EcomPixel::where('status', 1)->get();
        view()->share('pixels', $pixels);

        $gtm_code = GoogleTagManager::where('status', 1)->get();
        view()->share('gtm_code', $gtm_code);
		
		Paginator::useBootstrapFive();
    }
}
