<?php

namespace SergeyBruhin\NovaAnalytics\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use SergeyBruhin\Analytics\Drivers\Postgres\Models\Session as SessionModel;

/**
 * Read-only by design — this data is written exclusively through the
 * collect endpoint's queue job; Nova is a reporting surface on top of it,
 * not an editor for it.
 */
class Session extends Resource
{
    public static $model = SessionModel::class;

    public static $group = 'Аналитика';

    public static $search = ['id', 'site', 'utm_source', 'utm_campaign'];

    /**
     * @return array<int, mixed>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Visitor', 'visitor', Visitor::class),
            Text::make('Site')->sortable(),
            DateTime::make('Started At', 'started_at')->sortable(),
            DateTime::make('Ended At', 'ended_at')->sortable(),
            Boolean::make('Bot', 'is_bot'),
            Text::make('Device Type', 'device_type'),
            Text::make('Browser'),
            Text::make('OS', 'os'),
            Text::make('UTM Source', 'utm_source')->sortable(),
            Text::make('UTM Medium', 'utm_medium'),
            Text::make('UTM Campaign', 'utm_campaign')->sortable(),
            Text::make('UTM Content', 'utm_content')->onlyOnDetail(),
            Text::make('UTM Term', 'utm_term')->onlyOnDetail(),
            Code::make('Click IDs', 'click_ids')->json()->onlyOnDetail(),
            Text::make('Landing URL', 'landing_url')->onlyOnDetail(),
            Text::make('Exit URL', 'exit_url')->onlyOnDetail(),
            Text::make('Referrer')->onlyOnDetail(),
            Text::make('IP (truncated)', 'ip_truncated')->onlyOnDetail(),

            HasMany::make('Pageviews', 'pageviews', Pageview::class),
            HasMany::make('Events', 'events', Event::class),
        ];
    }

    public static function authorizedToCreate(Request $request): bool
    {
        return false;
    }

    public function authorizedToUpdate(Request $request): bool
    {
        return false;
    }

    public function authorizedToDelete(Request $request): bool
    {
        return false;
    }

    public function authorizedToReplicate(Request $request): bool
    {
        return false;
    }
}
