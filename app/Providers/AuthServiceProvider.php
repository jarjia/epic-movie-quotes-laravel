<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Movie;
use App\Models\Quote;
use App\Policies\MoviePolicy;
use App\Policies\QuotePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Movie::class => MoviePolicy::class,
        Quote::class => QuotePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
