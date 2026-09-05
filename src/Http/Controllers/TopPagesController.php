<?php

namespace SergeyBruhin\NovaAnalytics\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SergeyBruhin\NovaAnalytics\Support\AnalyticsFilters;
use SergeyBruhin\NovaAnalytics\Support\Queries\TopPagesQuery;

class TopPagesController extends Controller
{
    public function __invoke(Request $request, TopPagesQuery $query): JsonResponse
    {
        $filters = AnalyticsFilters::fromRequest($request);
        $limit = (int) $request->query('limit', config('nova-analytics-tool.top_pages_limit', 20));

        return response()->json([
            'rows' => $query->handle($filters, $limit),
        ]);
    }
}
