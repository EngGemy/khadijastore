<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\Setting;
use App\Models\ShippingRule;
use App\Models\Theme;
use App\Models\User;
use App\Notifications\LowStockNotification;
use App\Observers\OrderObserver;
use App\Observers\SettingObserver;
use App\Observers\ShippingRuleObserver;
use App\Policies\BrandResourcePolicy;
use App\Policies\OrderPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\ShippingRulePolicy;
use App\Policies\SuperAdminPolicy;
use App\Services\PublicStoragePublisher;
use App\Services\SettingsService;
use App\Services\ThemeResolver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use OwenIt\Auditing\Models\Audit;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThemeResolver::class);
        $this->app->singleton(SettingsService::class);

        require_once app_path('helpers.php');
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Product::class, BrandResourcePolicy::class);
        Gate::policy(Category::class, BrandResourcePolicy::class);
        Gate::policy(Review::class, ReviewPolicy::class);
        Gate::policy(ShippingRule::class, ShippingRulePolicy::class);
        Gate::policy(Brand::class, SuperAdminPolicy::class);
        Gate::policy(Theme::class, SuperAdminPolicy::class);
        Gate::policy(User::class, SuperAdminPolicy::class);
        Gate::policy(Audit::class, SuperAdminPolicy::class);

        Gate::before(function (User $user, string $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        $clear = fn () => app(ThemeResolver::class)->clearCache();
        Theme::saved($clear);
        Theme::deleted($clear);

        // Homepage caches brand cards + products for 1h — flush on any brand/product change.
        $clearHome = fn () => forget_home_blocks_cache();
        Brand::saved($clearHome);
        Brand::deleted($clearHome);
        Product::saved($clearHome);
        Product::deleted($clearHome);

        Order::observe(OrderObserver::class);
        Setting::observe(SettingObserver::class);
        ShippingRule::observe(ShippingRuleObserver::class);

        Event::listen(MediaHasBeenAddedEvent::class, function (MediaHasBeenAddedEvent $event): void {
            PublicStoragePublisher::publishPath($event->media->getPathRelativeToRoot());

            $model = $event->media->model;
            if ($model instanceof Brand || $model instanceof Product) {
                forget_home_blocks_cache();
            }
        });

        // إشعار المخزون المنخفض → إشعار لأدمن البراند والسوبر أدمن
        Event::listen('stock.low', function (ProductVariant|Product $stockHolder, int $brandId) {
            $name = $stockHolder instanceof ProductVariant
                ? ($stockHolder->product->name ?? '').' — '.$stockHolder->name
                : $stockHolder->name;

            $recipients = User::where(function ($q) use ($brandId) {
                $q->where('brand_id', $brandId)
                    ->whereHas('roles', fn ($r) => $r->whereIn('name', ['brand_admin', 'brand_staff']));
            })->orWhereHas('roles', fn ($r) => $r->where('name', 'super_admin'))->get();

            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new LowStockNotification($name, $stockHolder->stock));
            }
        });
    }
}
