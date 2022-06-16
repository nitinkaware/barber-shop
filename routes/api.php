<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\EventBookingController;
use App\Http\Controllers\EventsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/events/{event}/bookings', [EventBookingController::class, 'store']);
Route::get('/events', [EventsController::class, 'index']);