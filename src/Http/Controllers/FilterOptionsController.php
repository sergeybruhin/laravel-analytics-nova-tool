<?php

namespace SergeyBruhin\NovaAnalytics\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SergeyBruhin\NovaAnalytics\Support\AnalyticsFilters;
use SergeyBruhin\NovaAnalytics\Support\Queries\FilterOptionsQuery;

/** Populates the filter bar's dropdowns from real distinct values rather than hardcoding them. */
class FilterOptionsController extends Controller
{
    public function __invoke(Request $request, FilterOptionsQuery $query): JsonResponse
    {
        $filters = AnalyticsFilters::fromRequest($request);
        $limit = (int) config('nova-analytics-tool.filter_options_limit', 200);

        return response()->json($query->handle($filters, $limit));
    }
}
