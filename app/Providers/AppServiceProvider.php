<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\PaymentProof;
use App\Observers\PaymentProofObserver;
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
         PaymentProof::observe(PaymentProofObserver::class);
    }
}
