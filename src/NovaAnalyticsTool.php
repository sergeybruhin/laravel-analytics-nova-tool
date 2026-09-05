<?php

namespace SergeyBruhin\NovaAnalytics;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use SergeyBruhin\NovaAnalytics\Support\Authorization;

class NovaAnalyticsTool extends Tool
{
    public function boot(): void
    {
        Nova::script('nova-analytics', __DIR__.'/../dist/js/tool.js');
        Nova::style('nova-analytics', __DIR__.'/../dist/css/tool.css');
    }

    /**
     * Label matches the existing Dashboards\Analytics::label() on purpose. They render in
     * different sidebar locations (that dashboard is a child link inside Nova's own
     * "Dashboards" dropdown; this Tool gets its own top-level section), and reusing the
     * string means the host's NovaServiceProvider::$sectionIcons entry for 'Аналитика'
     * applies here automatically — no separate icon wiring needed.
     */
    public function menu(Request $request): mixed
    {
        return MenuSection::make('Аналитика')
            ->path('/nova-analytics')
            ->icon('ph-chart-line');
    }

    public function authorize(Request $request): bool
    {
        return Authorization::canView($request);
    }
}
