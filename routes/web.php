<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FolderController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



Route::resource('documents', DocumentController::class);
Route::get('/download/{document}', [DocumentController::class, 'download'])->name('documents.download');
Route::resource('folders', FolderController::class);
