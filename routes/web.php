<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/upload', function() {
    return view('upload');
});
Route::post('/document', [DocumentController::class, 'index'])->name('document');
