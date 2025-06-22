<?php

use App\Http\Controllers\Api\CalendarAvailabilityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/calendars/{id}/availabilities', [CalendarAvailabilityController::class, 'index']);
Route::get('/public/calendars/{id}/availabilities', [CalendarAvailabilityController::class, 'public']);