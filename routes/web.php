<?php

use App\Http\Controllers\Auth\CustomAuthController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceInterfaceController;
use App\Http\Controllers\MetricsController;
use App\Http\Controllers\PoolController;
use App\Http\Controllers\SumMetricsController;
use App\Http\Controllers\WanStatTotalController;
use Illuminate\Support\Facades\Route;


use Illuminate\Support\Facades\Auth;
//
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard') // or whatever route you want
        : redirect()->route('login');
});


//// Auth system
//Route::get('/', [AzureAuthController::class, 'redirectToProvider'])->name('login.azure');
//Route::get('/login/azure/callback', [AzureAuthController::class, 'handleProviderCallback']);

Route::get('login', [CustomAuthController::class, 'index'])->name('login');
Route::post('custom-login', [CustomAuthController::class, 'customLogin'])->name('login.custom');
Route::get('registration', [CustomAuthController::class, 'registration'])->name('register-user');
Route::post('custom-registration', [CustomAuthController::class, 'customRegistration'])->name('register.custom');
Route::get('logout', [CustomAuthController::class, 'signOut'])->name('logout');



//Clients
Route::middleware('auth')->group(function () {
    Route::get('dashboard', [CustomAuthController::class, 'dashboard'])->name('dashboard');

    Route::resource('devices', DeviceController::class);
    Route::resource('device_interfaces', DeviceInterfaceController::class);
    Route::resource('pools', PoolController::class);
    Route::resource('wan_stats', WanStatTotalController::class);


    Route::get('/wan-stats/influx', [WanStatTotalController::class, 'exportToInflux']);


    Route::get('/settings', function () {
        return view('dashboard');
    })->name('settings');

});

Route::get('/grafana/totals', [WanStatTotalController::class, 'exportTotalsCsv'])
    ->name('wan_stats.totals');

Route::get('/wan-stats/export', [WanStatTotalController::class, 'exportCsv'])
    ->name('wan_stats.export');

Route::get('/wan-stats/add-pfr', [WanStatTotalController::class, 'addPfr'])
    ->name('wan_stats.add-pfr');

Route::get('/wan-stats/add-ibo', [WanStatTotalController::class, 'addIBO'])
    ->name('wan_stats.add-ibo');


Route::get('/wan-stats/metadata/get', [WanStatTotalController::class, 'addMetaData']);
Route::get('/wan-stats/metadata', [WanStatTotalController::class, 'metaData']);



//Route::get('/export', [MetricsController::class, 'getMetrics']);
//Route::get('/export-test', [MetricsController::class, 'getMetrics_good']);

Route::get('/export-sum-test', [SumMetricsController::class, 'getSumMetricsOneDeviceInterface']);
//Route::get('/export-sum', [SumMetricsController::class, 'getSumMetrics']);
Route::get('/export-sum', [SumMetricsController::class, 'getSumMetrics']);
Route::get('/export-sum-norm', [SumMetricsController::class, 'getSumMetrics_norm']);
Route::get('/export-sum-may', [SumMetricsController::class, 'getSumMetricsMay']);
Route::get('/export-inactive-ports', [SumMetricsController::class, 'getInactivePorts']);
