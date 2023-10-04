<?php

use App\Http\Controllers\ExecutiveReportController;
use App\Http\Controllers\PlaybackContoller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('playback/live_track', [PlaybackContoller::class, 'live_track'])->name('live_track');
Route::resource('playback',PlaybackContoller::class);
Route::resource('executive_report',ExecutiveReportController::class);

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
