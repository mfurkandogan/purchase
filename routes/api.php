<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SubController;
use App\Http\Middleware\Auth;
use Illuminate\Support\Facades\Route;


Route::post('register', [DeviceController::class, 'register']);
Route::post('platform/{os}', [OrderController::class, 'checkOrder']);
Route::post('checkSubs', [SubController::class, 'checkSubs']);


Route::middleware([Auth::class])->group(function(){
    Route::post('purchase', [OrderController::class, 'createOrder']);
    Route::post('checkSubscription', [DeviceController::class, 'checkSubscription']);
});
