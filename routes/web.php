<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentRegistrationEntryController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PermissionController;
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
// In routes/web.php
Route::get('/document-registration-entries/search', [DocumentController::class, 'search'])
    ->name('document-registration-entries.search');

Route::resource('folders', FolderController::class);



// Fix the routes order to prioritize named routes before parameter routes
Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
Route::post('/profile/upload-image', [ProfileController::class, 'uploadImage'])->name('profile.uploadImage');
Route::delete('/profile/reset-image', [ProfileController::class, 'resetImage'])->name('profile.resetImage')->middleware('auth');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

Route::middleware(['auth', 'permission:manage users|manage roles'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [PermissionController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [PermissionController::class, 'show'])->name('users.show');
    Route::put('/users/{user}/roles', [PermissionController::class, 'updateUserRoles'])->name('users.roles.update');
    Route::put('/users/{user}/permissions', [PermissionController::class, 'updateUserPermissions'])->name('users.permissions.update');
});

Route::resource('roles', RoleController::class)->only(['index', 'show']);
Route::post('roles/{role}/update-permissions', [RoleController::class, 'updatePermissions'])->name('roles.update-permissions');



Route::get('/document-registry/{documentRegistrationEntry}/download', [DocumentRegistrationEntryController::class, 'downloadFile'])
    ->name('document-registry.download');


Route::get('/document-registry/{documentRegistrationEntry}/preview', [DocumentRegistrationEntryController::class, 'previewFile'])
    ->name('document-registry.preview');


// Document Registration Entry Routes
Route::middleware(['auth'])->prefix('document-registry')->name('document-registry.')->group(function () {
    // Basic CRUD routes
    Route::get('/', [App\Http\Controllers\DocumentRegistrationEntryController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\DocumentRegistrationEntryController::class, 'create'])
        ->middleware('permission:submit document for approval')->name('create');
    Route::post('/', [App\Http\Controllers\DocumentRegistrationEntryController::class, 'store'])
        ->middleware('permission:submit document for approval')->name('store');
    Route::get('/{documentRegistrationEntry}', [App\Http\Controllers\DocumentRegistrationEntryController::class, 'show'])->name('show');
    Route::get('/{documentRegistrationEntry}/edit', [App\Http\Controllers\DocumentRegistrationEntryController::class, 'edit'])
        ->middleware('permission:edit document registration details')->name('edit');
    Route::put('/{documentRegistrationEntry}', [App\Http\Controllers\DocumentRegistrationEntryController::class, 'update'])
        ->middleware('permission:edit document registration details')->name('update');

    // Approval workflow routes
    Route::post('/{documentRegistrationEntry}/approve', [App\Http\Controllers\DocumentRegistrationEntryController::class, 'approve'])
        ->middleware('permission:approve document registration')->name('approve');
    Route::post('/{documentRegistrationEntry}/reject', [App\Http\Controllers\DocumentRegistrationEntryController::class, 'reject'])
        ->middleware('permission:reject document registration')->name('reject');
    Route::post('/{documentRegistrationEntry}/require-revision', [App\Http\Controllers\DocumentRegistrationEntryController::class, 'requireRevision'])
        ->middleware('permission:require revision for document')->name('require-revision');
    Route::delete('/{documentRegistrationEntry}/withdraw', [App\Http\Controllers\DocumentRegistrationEntryController::class, 'withdraw'])
        ->middleware('permission:withdraw document submission')->name('withdraw');
});

