<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SubmissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [SubmissionController::class, 'index']);


Route::get('/questions', [QuestionController::class, 'getAllQuestions']);
Route::post('/questions', [QuestionController::class, 'store']);
Route::get('/languages', [SubmissionController::class, 'getSupportedLanguages']);

