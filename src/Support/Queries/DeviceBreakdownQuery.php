<?php

namespace SergeyBruhin\NovaAnalytics\Support\Queries;

use SergeyBruhin\NovaAnalytics\Support\AnalyticsFilters;

/** Groups sessions by device_type/browser/os — nothing surfaces these columns today. */
final class DeviceBreakdownQuery extends SessionDimensionQuery
{
    private const NULL_LABEL = 'Не определено';

    /**
     * @return array{
     *     by_device_type: array<int, array{value: ?string, label: string, sessions: int}>,
     *     by_browser: array<int, array{value: ?string, label: string, sessions: int}>,
     *     by_os: array<int, array{value: ?string, label: string, sessions: int}>,
     * }
     */
    public function handle(AnalyticsFilters $filters): array
    {
        return [
            'by_device_type' => $this->groupBy($filters, 'device_type', self::NULL_LABEL),
            'by_browser' => $this->groupBy($filters, 'browser', self::NULL_LABEL),
            'by_os' => $this->groupBy($filters, 'os', self::NULL_LABEL),
        ];
    }
}
