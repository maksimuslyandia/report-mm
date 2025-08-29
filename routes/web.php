<?php

use App\Http\Controllers\Auth\CustomAuthController;
use App\Http\Controllers\MetricsController;
use App\Http\Controllers\SumMetricsController;
use Illuminate\Support\Facades\Route;


use Illuminate\Support\Facades\Auth;
//
//Route::get('/', function () {
//    return Auth::check()
//        ? redirect()->route('dashboard') // or whatever route you want
//        : redirect()->route('login');
//});


// Auth system
//Route::get('/', [AzureAuthController::class, 'redirectToProvider'])->name('login.azure');
//Route::get('/login/azure/callback', [AzureAuthController::class, 'handleProviderCallback']);

//Route::get('login', [CustomAuthController::class, 'index'])->name('login');
//Route::post('custom-login', [CustomAuthController::class, 'customLogin'])->name('login.custom');
//Route::get('registration', [CustomAuthController::class, 'registration'])->name('register-user');
//Route::post('custom-registration', [CustomAuthController::class, 'customRegistration'])->name('register.custom');
//Route::get('logout', [CustomAuthController::class, 'signOut'])->name('logout');



//Clients
//Route::middleware('auth')->group(function () {
//    Route::get('dashboard', [CustomAuthController::class, 'dashboard'])->name('dashboard');
//
//    Route::get('/users', function () {
//        return view('dashboard');
//    })->name('users');
//
//    Route::get('/settings', function () {
//        return view('dashboard');
//    })->name('settings');

//});



Route::get('/export', [MetricsController::class, 'getMetrics']);
Route::get('/export-test', [MetricsController::class, 'getMetrics_good']);

Route::get('/export-sum-test', [SumMetricsController::class, 'getSumMetricsOneDeviceInterface']);
Route::get('/export-sum', [SumMetricsController::class, 'getSumMetrics']);
Route::get('/export-sum-may', [SumMetricsController::class, 'getSumMetricsMay']);
