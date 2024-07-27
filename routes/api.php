<?php

use App\Http\Controllers\AudioProcessController;
use App\Http\Controllers\AudioSummariseController;
use App\Http\Controllers\AudioTranscribeController;
use App\Http\Controllers\FitnessCoachController;
use App\Http\Controllers\TravelAssistantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('travel-assistant', TravelAssistantController::class);

Route::get('fitness-coach', FitnessCoachController::class);

Route::post('audio-transcribe', AudioTranscribeController::class);

Route::post('audio-process', AudioProcessController::class);
