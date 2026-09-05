<?php

namespace SergeyBruhin\NovaAnalytics\Support\Queries;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use SergeyBruhin\Analytics\Drivers\Postgres\Models\Pageview;
use SergeyBruhin\NovaAnalytics\Support\AnalyticsFilters;

/**
 * Daily pageviews + unique visitors over the selected range, zero-filled so a day with no
 * traffic still appears on the chart instead of leaving a silent gap.
 */
final class TrafficTrendQuery
{
    /**
     * @return array<int, array{date: string, pageviews: int, visitors: int}>
     */
    public function handle(AnalyticsFilters $filters): array
    {
        $tz = AnalyticsFilters::timezone();

        $rows = Pageview::query()
            ->whereHas('session', fn ($query) => $filters->applySessionAttributes($query))
            ->tap(fn ($query) => $filters->applyDateRange($query, 'created_at'))
            ->selectRaw(
                "date_trunc('day', created_at AT TIME ZONE ?) as day, count(*) as pageviews, count(distinct visitor_id) as visitors",
                [$tz]
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy(fn ($row) => CarbonImmutable::parse($row->day)->format('Y-m-d'));

        $lastDay = $filters->until !== null
            ? $filters->until->subDay()
            : CarbonImmutable::now($tz)->startOfDay();

        $series = [];

        foreach (CarbonPeriod::create($filters->from, $lastDay, '1 day') as $day) {
            $key = $day->format('Y-m-d');
            $row = $rows->get($key);

            $series[] = [
                'date' => $key,
                'pageviews' => (int) ($row->pageviews ?? 0),
                'visitors' => (int) ($row->visitors ?? 0),
            ];
        }

        return $series;
    }
}
