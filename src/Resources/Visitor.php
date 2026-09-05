<?php

namespace SergeyBruhin\NovaAnalytics\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use SergeyBruhin\Analytics\Drivers\Postgres\Models\Visitor as VisitorModel;

/**
 * Read-only by design — this data is written exclusively through the
 * collect endpoint's queue job; Nova is a reporting surface on top of it,
 * not an editor for it.
 */
class Visitor extends Resource
{
    public static $model = VisitorModel::class;

    public static $group = 'Аналитика';

    public static $search = ['id', 'site', 'external_id', 'first_utm_source', 'first_utm_campaign'];

    /**
     * @return array<int, mixed>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Site')->sortable(),
            DateTime::make('First Seen At', 'first_seen_at')->sortable(),
            DateTime::make('Last Seen At', 'last_seen_at')->sortable(),
            Text::make('External ID', 'external_id')->sortable(),
            Text::make('First Landing URL', 'first_landing_url')->onlyOnDetail(),
            Text::make('First Referrer', 'first_referrer')->onlyOnDetail(),
            Text::make('First UTM Source', 'first_utm_source')->sortable(),
            Text::make('First UTM Medium', 'first_utm_medium'),
            Text::make('First UTM Campaign', 'first_utm_campaign')->sortable(),
            Text::make('First UTM Content', 'first_utm_content')->onlyOnDetail(),
            Text::make('First UTM Term', 'first_utm_term')->onlyOnDetail(),
            Code::make('First Click IDs', 'first_click_ids')->json()->onlyOnDetail(),
            Code::make('Traits', 'traits')->json()->onlyOnDetail(),

            HasMany::make('Sessions', 'sessions', Session::class),
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
