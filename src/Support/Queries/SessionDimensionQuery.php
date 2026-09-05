<?php

namespace SergeyBruhin\NovaAnalytics\Support\Queries;

use SergeyBruhin\Analytics\Drivers\Postgres\Models\Session;
use SergeyBruhin\NovaAnalytics\Support\AnalyticsFilters;

/**
 * "Top N sessions grouped by one of their own columns" is the same query shape for both
 * UTM breakdowns (source/medium/campaign) and device breakdowns (device_type/browser/os) —
 * this is the one method both TrafficSourcesQuery and DeviceBreakdownQuery call, rather than
 * four/six near-identical copies of the same groupBy.
 *
 * $column is always one of this class's own hardcoded call sites, never request input, so
 * interpolating it into selectRaw/groupBy is safe — Eloquent has no way to bind a column
 * name as a parameter.
 */
abstract class SessionDimensionQuery
{
    /**
     * @return array<int, array{value: ?string, label: string, sessions: int}>
     */
    protected function groupBy(AnalyticsFilters $filters, string $column, string $nullLabel, int $limit = 20): array
    {
        return Session::query()
            ->tap(fn ($query) => $filters->applySessionAttributes($query))
            ->tap(fn ($query) => $filters->applyDateRange($query, 'started_at'))
            ->selectRaw("{$column} as value, count(*) as sessions")
            ->groupBy($column)
            ->orderByDesc('sessions')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'value' => $row->value,
                'label' => $row->value !== null && $row->value !== '' ? $row->value : $nullLabel,
                'sessions' => (int) $row->sessions,
            ])
            ->all();
    }
}
