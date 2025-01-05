<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\TipeController;
use App\Http\Controllers\PowerUpController;
use App\Http\Controllers\MaterialController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->group(function () {
    
    //auth
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // quiz
    Route::get('/quizzes', [QuizController::class, 'getQuizzes']);
    Route::get('/class-types', [TipeController::class, 'tipeKelas']);
    Route::get('/question-types', [TipeController::class, 'tipeJawaban']);
    Route::post('/store/quizzes', [QuizController::class, 'storeQuiz']);
    Route::get('/quiz-detail/{quiz_id}', [QuizController::class, 'getQuizDetail']);
    Route::get('/get/answer/detail/{question_id}', [QuizController::class, 'getAnswerDetail']);
    Route::post('/validate/answer', [QuizController::class, 'validateAnswer']);
    Route::post('/save/answer', [QuizController::class, 'saveAnswer']);
    Route::get('/resume/answers/{quiz_id}/{user_id}', [QuizController::class, 'getResumeAnswers']);
    Route::post('/quiz/attempt', [QuizController::class, 'storeQuizResult']);
    Route::get('/leaderboard', [QuizController::class, 'getLeaderboard']);
    Route::get('/history/{user_id}', [QuizController::class, 'getHistory']);
    
    Route::get('/power-ups', [PowerUpController::class, 'getAllPowerUps']);
    Route::post('/power-up-usage', [PowerUpController::class, 'storePowerUpUsage']);

    //material
    Route::post('/materials/upload', [MaterialController::class, 'uploadMaterial']);
    Route::get('/materials/{tipeKelasId}', [MaterialController::class, 'getMaterialsByKelas']);
});
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
