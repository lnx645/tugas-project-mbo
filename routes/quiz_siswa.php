<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Siswa\QuizController;

Route::get('', [QuizController::class, 'index'])->name('siwaQuiz');
Route::get('{history_id}/detail', [QuizController::class, 'hasilDetail'])->name('hasilDetail');
Route::post('{id}/start', [QuizController::class, 'start'])->name('siwaQuizStart');
Route::get('{id}', [QuizController::class, 'detail'])->name('siwaQuizDetail');
Route::get('kerjakan/quiz-{id}-history-{history_id}', [QuizController::class, 'kerjakan'])->name('kerjakanQuiz');
Route::post('kerjakan/quiz-{id}-history-{history_id}/save', [QuizController::class, 'autoSaveJawaban'])->name('autoSaveJawaban');
Route::post('kerjakan/quiz-{id}-history-{history_id}', [QuizController::class, 'selesai'])->name('selesaikanUjian');
