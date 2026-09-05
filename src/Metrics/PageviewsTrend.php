<?php

namespace SergeyBruhin\NovaAnalytics\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;
use SergeyBruhin\Analytics\Drivers\Postgres\Models\Pageview;

class PageviewsTrend extends Trend
{
    public function calculate(NovaRequest $request): TrendResult
    {
        return $this->countByDays($request, Pageview::query()->whereHas(
            'session',
            fn ($query) => $query->where('is_bot', false)
        ));
    }

    public function ranges(): array
    {
        return [
            7 => '7 Days',
            30 => '30 Days',
            60 => '60 Days',
            90 => '90 Days',
        ];
    }

    public function uriKey(): string
    {
        return 'analytics-pageviews-trend';
    }
}
