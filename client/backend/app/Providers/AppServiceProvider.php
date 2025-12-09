<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\TourReview;
use App\Observers\OrderObserver;
use App\Observers\TourReviewObserver;
use Illuminate\Support\ServiceProvider;

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
        // Register Order observer to send emails when booking is completed
        Order::observe(OrderObserver::class);
        
        // Register TourReview observer to update tour rating
        TourReview::observe(TourReviewObserver::class);
    }
}
