<?php

namespace SergeyBruhin\NovaAnalytics\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Http\Middleware\Authenticate;
use Laravel\Nova\Nova;
use SergeyBruhin\NovaAnalytics\Http\Middleware\Authorize;
use SergeyBruhin\NovaAnalytics\NovaAnalyticsTool;

class NovaAnalyticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/nova-analytics-tool.php', 'nova-analytics-tool');

        $this->app->singleton(NovaAnalyticsTool::class);
    }

    public function boot(): void
    {
        $this->app->booted(function (): void {
            $this->routes();
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/nova-analytics-tool.php' => config_path('nova-analytics-tool.php'),
            ], 'config');
        }
    }

    /**
     * Nova's own auth middleware plus the tool's gate. Registered on `booted` so the Nova
     * config is in place; skipped entirely once routes are cached, as Laravel expects.
     */
    private function routes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        // The page itself, inside Nova's own prefix and domain.
        Nova::router(['nova', Authenticate::class, Authorize::class], 'nova-analytics')
            ->group(__DIR__.'/../../routes/inertia.php');

        // The JSON the page talks to, under the convention Nova's front end expects.
        Route::middleware(['nova', Authorize::class])
            ->domain(config('nova.domain'))
            ->prefix('nova-vendor/nova-analytics')
            ->group(__DIR__.'/../../routes/api.php');
    }
}
