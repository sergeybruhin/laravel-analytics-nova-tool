<?php

namespace SergeyBruhin\NovaAnalytics\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * No boot logic needed: Resources, Metrics and the Dashboard are plain Nova
 * classes the host app registers itself (AdminAreaRegistry, NovaServiceProvider)
 * rather than auto-discovered — this provider only exists so Composer's
 * package-discovery has something to register.
 */
class NovaAnalyticsServiceProvider extends ServiceProvider {}
