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


Route::patch('/folders/{folder}/move', [FolderController::class, 'move'])->name('folders.move');
Route::patch('/documents/{document}/move', [DocumentController::class, 'move'])->name('documents.move');
Route::resource('documents', DocumentController::class);
Route::get('/download/{document}', [DocumentController::class, 'download'])->name('documents.download');
Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');
Route::post('/documents/{document}/update-content', [DocumentController::class, 'updateContent'])->name('documents.update-content');
Route::resource('folders', FolderController::class);
