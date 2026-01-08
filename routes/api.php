<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Spears\UserController;
use App\Http\Controllers\API\SMTS\DocumentRegistryEntryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// replace old spears/account user routes with resource-style routes (store + update)
// Keep parameter mapping so update uses {employee_no}
Route::apiResource('spears/account/users', UserController::class)
    ->parameters(['users' => 'employee_no'])
    ->only(['store', 'update']);

// Document registry entries as resource API (index is the important migrated method)
Route::apiResource('smts/document-registry-entry', DocumentRegistryEntryController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);
