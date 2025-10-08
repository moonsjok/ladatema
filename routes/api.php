<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SouscriptionCallbackController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/souscription-callback', [SouscriptionCallbackController::class, 'handleCallback'])->name("souscription-payment-callback");
