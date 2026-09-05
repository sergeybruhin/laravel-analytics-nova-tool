<?php

namespace SergeyBruhin\NovaAnalytics\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;
use SergeyBruhin\Analytics\Drivers\Postgres\Models\Visitor;

class TopTrafficSources extends Partition
{
    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Visitor::query(), 'first_utm_source')
            ->label(fn (?string $value) => $value ?: 'Direct / unknown');
    }

    public function uriKey(): string
    {
        return 'analytics-top-traffic-sources';
    }
}
