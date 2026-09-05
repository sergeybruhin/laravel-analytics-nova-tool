<?php

use Illuminate\Support\Facades\Route;
use SergeyBruhin\NovaAnalytics\Http\Controllers\DeviceBreakdownController;
use SergeyBruhin\NovaAnalytics\Http\Controllers\FilterOptionsController;
use SergeyBruhin\NovaAnalytics\Http\Controllers\TopPagesController;
use SergeyBruhin\NovaAnalytics\Http\Controllers\TrafficSourcesController;
use SergeyBruhin\NovaAnalytics\Http\Controllers\TrafficTrendController;

Route::get('/filter-options', FilterOptionsController::class);
Route::get('/trend', TrafficTrendController::class);
Route::get('/top-pages', TopPagesController::class);
Route::get('/traffic-sources', TrafficSourcesController::class);
Route::get('/devices', DeviceBreakdownController::class);
