<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

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
