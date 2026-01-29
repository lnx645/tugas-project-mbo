<?php

use App\Http\Controllers\Guru\BankSoalController;
use App\Http\Controllers\Guru\JadwalQuizController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Guru\QuizController;

Route::get('', [QuizController::class, 'index'])->name('mainGuru');
Route::get('jadwal', [JadwalQuizController::class, 'index'])->name('jadwalQuizGuru');
Route::get('tambah-jadwal', [JadwalQuizController::class, 'tambah'])->name('formTambahJadwal');
Route::post('tambah-jadwal', [JadwalQuizController::class, 'simpanJadwal'])->name('simpanJadwal');
Route::get('edit-jadwal-{id}', [JadwalQuizController::class, 'editJadwal'])->name('editJadwal');
Route::put('edit-jadwal-{id}', [JadwalQuizController::class, 'simpanEditJadwal'])->name('simpanEditJadwal');




Route::get('bank-soal', [BankSoalController::class, 'index'])->name('bankSoalGuru');
Route::post('bank-soal/add-new-category', [BankSoalController::class, 'addNewCategory'])->name('addNewCategory');
Route::put('bank-soal/edit-category/{id}', [BankSoalController::class, 'editQuizCategory'])->name('editQuizCategory');
Route::get('category-{id}-bank-soal', [BankSoalController::class, 'bankSoal'])->name('showBankSoal');
Route::get('category-{id}-bank-soal-add-new', [BankSoalController::class, 'tambahSoalBaru'])->name('tambahSoalBaru');
Route::post('category-{id}-bank-soal-add-new', [BankSoalController::class, 'simpanBankSoal'])->name('simpanBankSoal');
Route::get('bank-soal-{soal_id}-edit', [BankSoalController::class, 'editBankSoal'])->name('editBankSoal');
Route::put('bank-soal-{soal_id}-edit', [BankSoalController::class, 'simpanEditBankSoal'])->name('simpanEditBankSoal');
Route::delete('bank-soal-{soal_id}-delete', [BankSoalController::class, 'deleteSoal'])->name('deleteSoal');
