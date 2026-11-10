<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

Route::get('/events', [EventController::class, 'index']);
Route::get('/events/search', [EventController::class, 'search']);