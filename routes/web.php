<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

// Routes for authenticated and verified users
Auth::routes(['verify' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/dashboard',  [App\Http\Controllers\PagesController::class, 'index']);
Route::get('/deposit',  [App\Http\Controllers\PagesController::class, 'deposit']);
Route::get('/withdrawal',  [App\Http\Controllers\PagesController::class, 'withdrawal']);
Route::get('/purchase-package',  [App\Http\Controllers\PagesController::class, 'purchase']);

Route::get('/announcement',  [App\Http\Controllers\PagesController::class, 'announcement']);
Route::get('/referral',  [App\Http\Controllers\PagesController::class, 'referral']);
Route::get('/daily-history',  [App\Http\Controllers\PagesController::class, 'dailyHistory']);
Route::get('/referral-history',  [App\Http\Controllers\PagesController::class, 'referralHistory']);


// Routes for User settings
Route::get('/settings/profile',  [App\Http\Controllers\PagesController::class, 'profile']);
Route::get('/settings/password',  [App\Http\Controllers\PagesController::class, 'changePassword']);
Route::get('/settings/pin',  [App\Http\Controllers\PagesController::class, 'changePin']);

