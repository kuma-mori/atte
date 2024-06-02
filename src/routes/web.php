<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BreakTimeController;
use App\Http\Controllers\AttendanceController;

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

Route::middleware('auth')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('attendance');
    Route::get('/attendance', [AttendanceController::class, 'list'])->name('attendance.list');
});
Route::post('/attendance/start', [AttendanceController::class, 'store'])->name('attendance.store');
Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
Route::post('/attendance/end', [AttendanceController::class, 'workEnd'])->name('attendance.end');
Route::post('/breaktime/start', [BreakTimeController::class, 'store'])->name('breaktime.start');
Route::post('/breaktime/store', [BreakTimeController::class, 'store'])->name('breaktime.store');
Route::post('/breaktime/end', [BreakTimeController::class, 'breakEnd'])->name('breaktime.end');