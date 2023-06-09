<?php

use App\Http\Controllers\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/upload', [ContactController::class, 'store']);
Route::get('/upload/{id}', [ContactController::class, 'getUploadDetails']);

Route::get('/upload-summary/{id}', [ContactController::class, 'getUploadSummary']);

Route::delete('/upload/{id}', [ContactController::class, 'deleteJob']);
