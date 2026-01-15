<?php

use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/register',[AuthController::class,'register']);
Route::post('/auth/login',[AuthController::class,'login']);
Route::post('/auth/mail-verify',[AuthController::class,'mailVerify']);

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
Route::middleware('auth:sanctum')->get('/notifications', function (Request $request) {
    return apiSuccess(
        'User notifications',
        $request->user()->notifications
    );
});


Route::middleware(['auth:sanctum'])->group(function(){

    //Check if logged in user has user role.
    Route::apiResource('/products',ProductController::class);
    Route::middleware('auth:sanctum')->post('/products/{id}/buy', [ProductController::class, 'buy']);
    Route::post('/offers/send',[OfferController::class,'send']);
    
});
