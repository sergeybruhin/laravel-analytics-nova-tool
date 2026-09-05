<?php

namespace SergeyBruhin\NovaAnalytics\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use SergeyBruhin\Analytics\Drivers\Postgres\Models\Pageview as PageviewModel;

/**
 * Read-only by design — this data is written exclusively through the
 * collect endpoint's queue job; Nova is a reporting surface on top of it,
 * not an editor for it.
 */
class Pageview extends Resource
{
    public static $model = PageviewModel::class;

    public static $group = 'Аналитика';

    public static $title = 'path';

    public static $search = ['path', 'site'];

    /**
     * @return array<int, mixed>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Visitor', 'visitor', Visitor::class),
            BelongsTo::make('Session', 'session', Session::class),
            Text::make('Site')->sortable(),
            Text::make('Path')->sortable(),
            Text::make('Title'),
            Text::make('URL', 'url')->onlyOnDetail(),
            Text::make('Referrer')->onlyOnDetail(),
            Number::make('Duration (ms)', 'duration_ms'),
            DateTime::make('Created At', 'created_at')->sortable(),
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
