<?php

use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\ContactController;
use App\Http\Controllers\v1\FollowupController;
use App\Http\Controllers\v1\FormSerialController;
use App\Http\Controllers\v1\IndustryController;
use App\Http\Controllers\v1\InquerySourceController;
use App\Http\Controllers\v1\LeadController;
use App\Http\Controllers\v1\LeadStatusController;
use App\Http\Controllers\v1\ProductController;
use App\Http\Controllers\v1\RoleController;
use App\Http\Controllers\v1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register'])->middleware('guest:api')->name('api.register');
Route::post('login', [AuthController::class, 'login'])->middleware('guest:api');
Route::get('refresh-token', [AuthController::class, 'refresh'])->middleware('auth:api');

Route::middleware(['auth:api'])->group(function (){
    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('followups', FollowupController::class);
    Route::apiResource('form-serial-numbers', FormSerialController::class);
    Route::apiResource('industries', IndustryController::class);
    Route::apiResource('inquery-sources', InquerySourceController::class);
    Route::apiResource('lead-statuses', LeadStatusController::class);
    Route::apiResource('leads', LeadController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('roles', RoleController::class);
    Route::get('/permissions', [RoleController::class, 'getAllPermissions']);
    Route::apiResource('users', UserController::class);
    Route::put('users/{user_id}/is-active', [UserController::class, 'updateIsActive']);
    Route::post('users/changePassword', [UserController::class, 'changePassword']);
});
