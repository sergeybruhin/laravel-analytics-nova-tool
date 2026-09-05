<?php

namespace SergeyBruhin\NovaAnalytics\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use SergeyBruhin\Analytics\Drivers\Postgres\Models\Event as EventModel;

/**
 * Read-only by design — this data is written exclusively through the
 * collect endpoint's queue job; Nova is a reporting surface on top of it,
 * not an editor for it.
 */
class Event extends Resource
{
    public static $model = EventModel::class;

    public static $group = 'Аналитика';

    public static $title = 'name';

    public static $search = ['name', 'site', 'dedup_key'];

    /**
     * @return array<int, mixed>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Visitor', 'visitor', Visitor::class),
            // Nullable: a server-side goal may only have a visitor, no live session.
            BelongsTo::make('Session', 'session', Session::class)->nullable(),
            Text::make('Site')->sortable(),
            Text::make('Name')->sortable(),
            Number::make('Value')->sortable(),
            Code::make('Properties')->json()->onlyOnDetail(),
            Text::make('Dedup Key', 'dedup_key')->onlyOnDetail(),
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
