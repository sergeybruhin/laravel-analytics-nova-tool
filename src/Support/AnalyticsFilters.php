<?php

namespace SergeyBruhin\NovaAnalytics\Support;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Parsed once per request and threaded through every Query class, so the trend, top pages,
 * traffic sources and device breakdown all answer the same question for the same slice of
 * traffic — that shared-filter behaviour is the entire reason this tool exists instead of
 * four more Nova metric cards with their own independent range dropdowns.
 *
 * This package cannot depend on the host app's App\Nova\Filters\PeriodFilter (that class
 * lives outside this package's own composer graph, same reason the Resources extend
 * Laravel\Nova\Resource directly rather than the host's App\Nova\Resource), so the six
 * preset keys are duplicated here rather than shared. The one deliberate difference: an
 * unmatched/missing period defaults to `last_30_days`, not "no filter" — PeriodFilter's
 * "no filter" default is fine for an opt-in resource filter, but would mean an unbounded
 * full-table scan on every page load here.
 */
final class AnalyticsFilters
{
    private function __construct(
        public readonly CarbonImmutable $from,
        public readonly ?CarbonImmutable $until,
        public readonly string $period,
        public readonly ?string $utmSource,
        public readonly ?string $utmMedium,
        public readonly ?string $utmCampaign,
        public readonly ?string $deviceType,
        public readonly ?string $browser,
        public readonly ?string $os,
        public readonly bool $includeBots,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $period = (string) $request->query('period', 'last_30_days');
        [$from, $until] = self::resolvePeriod($period);

        return new self(
            from: $from,
            until: $until,
            period: $period,
            utmSource: self::nullableString($request, 'utm_source'),
            utmMedium: self::nullableString($request, 'utm_medium'),
            utmCampaign: self::nullableString($request, 'utm_campaign'),
            deviceType: self::nullableString($request, 'device_type'),
            browser: self::nullableString($request, 'browser'),
            os: self::nullableString($request, 'os'),
            includeBots: $request->boolean('include_bots', false),
        );
    }

    /**
     * @return array<string, string>
     */
    public static function periodOptions(): array
    {
        return [
            'Сегодня' => 'today',
            'Вчера' => 'yesterday',
            'Последние 7 дней' => 'last_7_days',
            'Последние 30 дней' => 'last_30_days',
            'Этот месяц' => 'this_month',
            'Прошлый месяц' => 'last_month',
        ];
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function applyDateRange(Builder $query, string $column)
    {
        $qualified = $query->qualifyColumn($column);
        $query->where($qualified, '>=', $this->from->utc());

        // Half-open on purpose, same as PeriodFilter: `< until` rather than `<= end of
        // day`, so a row written in the last second of a day cannot fall through the gap.
        return $this->until === null ? $query : $query->where($qualified, '<', $this->until->utc());
    }

    /**
     * Applies every dimension filter that lives on the `sessions` table. Used both directly
     * on a Session query and inside a `whereHas('session', ...)` closure for pageview-based
     * queries — the same pattern the package's existing PageviewsTrend metric already uses
     * for its `is_bot` exclusion, just generalised to the rest of the session columns.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function applySessionAttributes(Builder $query)
    {
        return $query
            ->when(! $this->includeBots, fn ($q) => $q->where($q->qualifyColumn('is_bot'), false))
            ->when($this->utmSource, fn ($q, $value) => $q->where($q->qualifyColumn('utm_source'), $value))
            ->when($this->utmMedium, fn ($q, $value) => $q->where($q->qualifyColumn('utm_medium'), $value))
            ->when($this->utmCampaign, fn ($q, $value) => $q->where($q->qualifyColumn('utm_campaign'), $value))
            ->when($this->deviceType, fn ($q, $value) => $q->where($q->qualifyColumn('device_type'), $value))
            ->when($this->browser, fn ($q, $value) => $q->where($q->qualifyColumn('browser'), $value))
            ->when($this->os, fn ($q, $value) => $q->where($q->qualifyColumn('os'), $value));
    }

    /**
     * @return array{0: CarbonImmutable, 1: ?CarbonImmutable}
     */
    private static function resolvePeriod(string $value): array
    {
        $today = CarbonImmutable::now(self::timezone())->startOfDay();

        return match ($value) {
            'today' => [$today, null],
            'yesterday' => [$today->subDay(), $today],
            'last_7_days' => [$today->subDays(6), null],
            'this_month' => [$today->startOfMonth(), null],
            'last_month' => [$today->subMonthNoOverflow()->startOfMonth(), $today->startOfMonth()],
            default => [$today->subDays(29), null], // last_30_days, and the page-load default
        };
    }

    public static function timezone(): string
    {
        return (string) config('app.display_timezone', 'UTC');
    }

    private static function nullableString(Request $request, string $key): ?string
    {
        $value = $request->query($key);

        return is_string($value) && $value !== '' ? $value : null;
    }
}
