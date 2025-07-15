<?php

namespace App\Providers;

use App\Models\Spend;
use App\Policies\SpendPolicy;
use App\Models\Income;
use App\Policies\IncomePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

#[\Illuminate\Foundation\Providers\AutoDiscover]
class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Spend::class => SpendPolicy::class,
        Income::class => IncomePolicy::class,
    ];

    public function boot(): void
    {
        \Log::info('AuthServiceProvider booted');
    }
}
