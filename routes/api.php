<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ChatController;

Route::get('/test', function () {
    return response()->json(['message' => 'API working']);
});
Route::post('/slot/add', [BookingController::class, 'addSlot']);
Route::get('/slots', [BookingController::class, 'getSlots']);
Route::post('/book', [BookingController::class, 'book']);
// Route::post('/chat', [ChatController::class, 'chat']);
