<?php

namespace SergeyBruhin\NovaAnalytics\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Defers to the host app's `viewNovaAnalytics` gate. Defaults to allowed when the host
 * hasn't defined that gate at all, so the package stays usable standalone — same convention
 * as nova-postgres-tools's own Authorization::canView().
 */
final class Authorization
{
    public const VIEW = 'viewNovaAnalytics';

    public static function canView(Request $request): bool
    {
        if (! Gate::has(self::VIEW)) {
            return true;
        }

        return Gate::forUser($request->user())->check(self::VIEW);
    }
}
