<?php

use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::post('/api/track-complaint', [LandingController::class, 'track'])->name('complaint.track');
