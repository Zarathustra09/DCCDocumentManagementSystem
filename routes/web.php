<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\RoleController;
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



// Fix the routes order to prioritize named routes before parameter routes
Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
Route::post('/profile/upload-image', [App\Http\Controllers\ProfileController::class, 'uploadImage'])->name('profile.uploadImage');
Route::delete('/profile/reset-image', [App\Http\Controllers\ProfileController::class, 'resetImage'])->name('profile.resetImage')->middleware('auth');
Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');


Route::middleware(['auth', 'permission:manage users|manage roles'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/{user}', [RoleController::class, 'show'])->name('roles.show');
    Route::put('/users/{user}/roles', [RoleController::class, 'updateUserRoles'])->name('users.roles.update');
    Route::put('/users/{user}/permissions', [RoleController::class, 'updateUserPermissions'])->name('users.permissions.update');

});
