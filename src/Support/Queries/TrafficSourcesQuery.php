<?php

namespace SergeyBruhin\NovaAnalytics\Support\Queries;

use SergeyBruhin\NovaAnalytics\Support\AnalyticsFilters;

/**
 * Groups by the session's own utm_source/medium/campaign, not Visitor::first_utm_source
 * (unlike the package's existing TopTrafficSources metric) — first-touch attribution can
 * predate the selected date range, which would desync the breakdown from the date filter.
 */
final class TrafficSourcesQuery extends SessionDimensionQuery
{
    private const NULL_LABEL = 'Прямой / неизвестный';

    /**
     * @return array{
     *     by_source: array<int, array{value: ?string, label: string, sessions: int}>,
     *     by_medium: array<int, array{value: ?string, label: string, sessions: int}>,
     *     by_campaign: array<int, array{value: ?string, label: string, sessions: int}>,
     * }
     */
    public function handle(AnalyticsFilters $filters): array
    {
        return [
            'by_source' => $this->groupBy($filters, 'utm_source', self::NULL_LABEL),
            'by_medium' => $this->groupBy($filters, 'utm_medium', self::NULL_LABEL),
            'by_campaign' => $this->groupBy($filters, 'utm_campaign', self::NULL_LABEL),
        ];
    }
}
