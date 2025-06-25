<?php

namespace App\Providers;

use App\Models\Spend;
use App\Policies\SpendPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

#[\Illuminate\Foundation\Providers\AutoDiscover]
class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Spend::class => SpendPolicy::class,
    ];

    public function boot(): void
    {
        \Log::info('AuthServiceProvider booted');
    }
}
