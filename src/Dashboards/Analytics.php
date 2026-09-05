<?php

namespace SergeyBruhin\NovaAnalytics\Dashboards;

use Laravel\Nova\Dashboards\Dashboard;
use SergeyBruhin\NovaAnalytics\Metrics\PageviewsTrend;
use SergeyBruhin\NovaAnalytics\Metrics\TopTrafficSources;

class Analytics extends Dashboard
{
    public function label(): string
    {
        return 'Аналитика';
    }

    /**
     * @return array<int, mixed>
     */
    public function cards(): array
    {
        return [
            (new PageviewsTrend)->width('1/2'),
            (new TopTrafficSources)->width('1/2'),
        ];
    }
}
