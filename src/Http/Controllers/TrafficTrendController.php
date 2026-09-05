<?php

namespace SergeyBruhin\NovaAnalytics\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SergeyBruhin\NovaAnalytics\Support\AnalyticsFilters;
use SergeyBruhin\NovaAnalytics\Support\Queries\TrafficTrendQuery;

class TrafficTrendController extends Controller
{
    public function __invoke(Request $request, TrafficTrendQuery $query): JsonResponse
    {
        $filters = AnalyticsFilters::fromRequest($request);

        return response()->json([
            'period' => $filters->period,
            'series' => $query->handle($filters),
        ]);
    }
}
