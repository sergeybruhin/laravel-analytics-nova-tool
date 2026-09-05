<?php

namespace SergeyBruhin\NovaAnalytics\Support\Queries;

use SergeyBruhin\Analytics\Drivers\Postgres\Models\Session;
use SergeyBruhin\NovaAnalytics\Support\AnalyticsFilters;

/**
 * Distinct values for the filter bar's dropdowns, scoped only by the current date range —
 * not full cross-dimension faceting (picking utm_medium=cpc won't yet narrow the utm_source
 * list to sources actually paired with cpc). Simpler v1; worth revisiting if staff find the
 * option lists too broad.
 */
final class FilterOptionsQuery
{
    /**
     * @return array{
     *     periods: array<string, string>,
     *     utm_sources: array<int, string>,
     *     utm_mediums: array<int, string>,
     *     utm_campaigns: array<int, string>,
     *     device_types: array<int, string>,
     *     browsers: array<int, string>,
     *     os: array<int, string>,
     * }
     */
    public function handle(AnalyticsFilters $filters, int $limit): array
    {
        return [
            'periods' => AnalyticsFilters::periodOptions(),
            'utm_sources' => $this->distinct($filters, 'utm_source', $limit),
            'utm_mediums' => $this->distinct($filters, 'utm_medium', $limit),
            'utm_campaigns' => $this->distinct($filters, 'utm_campaign', $limit),
            'device_types' => $this->distinct($filters, 'device_type', $limit),
            'browsers' => $this->distinct($filters, 'browser', $limit),
            'os' => $this->distinct($filters, 'os', $limit),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function distinct(AnalyticsFilters $filters, string $column, int $limit): array
    {
        return Session::query()
            ->tap(fn ($query) => $filters->applyDateRange($query, 'started_at'))
            ->when(! $filters->includeBots, fn ($query) => $query->where('is_bot', false))
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->distinct()
            ->orderBy($column)
            ->limit($limit)
            ->pluck($column)
            ->all();
    }
}
