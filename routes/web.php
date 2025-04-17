<?php

use App\Http\Controllers\Backend\Dashboard\HomeController;
use App\Http\Controllers\Backend\Setting\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('dashboard')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard.index');

    Route::get('/setting/website', [SettingController::class, 'index'])->name('dashboard.setting.index');
    Route::post('/setting/website', [SettingController::class, 'update'])->name('dashboard.setting.update');
    Route::post('/setting/website/logo', [SettingController::class, 'logoChange'])->name('dashboard.setting.logo');
    Route::post('/setting/website/name', [SettingController::class, 'nameChange'])->name('dashboard.setting.name');
});

// Setting Website



