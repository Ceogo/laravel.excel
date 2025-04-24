<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ScheduleController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/upload', function () {
    return view('upload');
});
Route::post('/upload', [DocumentController::class, 'index'])->name('upload');
Route::get('/edit-data', [DocumentController::class, 'editData'])->name('edit_data');
Route::post('/save-data', [DocumentController::class, 'saveData'])->name('save_data');
Route::get('/document', [DocumentController::class, 'showDocument'])->name('document');
Route::get('/schedule', [ScheduleController::class, 'show'])->name('schedule');
Route::get('/schedule/edit/{scheduleId}', [ScheduleController::class, 'editSchedule'])->name('schedule.edit');
Route::post('/schedule/edit/{scheduleId}', [ScheduleController::class, 'editSchedule']);    
