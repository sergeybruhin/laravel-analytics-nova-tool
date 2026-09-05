<?php

namespace SergeyBruhin\NovaAnalytics\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SergeyBruhin\NovaAnalytics\Support\AnalyticsFilters;
use SergeyBruhin\NovaAnalytics\Support\Queries\DeviceBreakdownQuery;

class DeviceBreakdownController extends Controller
{
    public function __invoke(Request $request, DeviceBreakdownQuery $query): JsonResponse
    {
        return response()->json($query->handle(AnalyticsFilters::fromRequest($request)));
    }
}
