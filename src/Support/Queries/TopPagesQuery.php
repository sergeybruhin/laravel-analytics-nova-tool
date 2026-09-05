<?php

namespace SergeyBruhin\NovaAnalytics\Support\Queries;

use SergeyBruhin\Analytics\Drivers\Postgres\Models\Pageview;
use SergeyBruhin\NovaAnalytics\Support\AnalyticsFilters;

/** Which paths get the most views under the current filters — nothing else surfaces this today. */
final class TopPagesQuery
{
    /**
     * @return array<int, array{path: string, views: int, visitors: int}>
     */
    public function handle(AnalyticsFilters $filters, int $limit): array
    {
        return Pageview::query()
            ->whereHas('session', fn ($query) => $filters->applySessionAttributes($query))
            ->tap(fn ($query) => $filters->applyDateRange($query, 'created_at'))
            ->selectRaw('path, count(*) as views, count(distinct visitor_id) as visitors')
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'path' => $row->path,
                'views' => (int) $row->views,
                'visitors' => (int) $row->visitors,
            ])
            ->all();
    }
}
