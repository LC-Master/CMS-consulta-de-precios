<?php

namespace App\Providers;

use App\Models\Campaign;
use App\Models\Media;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;

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
        User::observe(\App\Observers\User\UserObserver::class);
        Campaign::observe(\App\Observers\CampaignObserver::class);
        PersonalAccessToken::observe(\App\Observers\Token\SanctumTokenObserver::class);
        Media::observe(\App\Observers\Media\MediaObserver::class);
    }
}
