<?php

use Illuminate\Support\Facades\Route;

/*
 * The tool's page inside Nova. Nova::router() has already applied the Nova path prefix and
 * domain, so this is the /nova-analytics the menu section points at.
 */
Route::get('/', static fn () => inertia('NovaAnalyticsTool'));
