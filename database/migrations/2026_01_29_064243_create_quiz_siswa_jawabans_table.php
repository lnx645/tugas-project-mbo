<?php

use App\Models\JadwalQuiz;
use App\Models\QuizBankSoal;
use App\Models\QuizJawabanSoal;
use App\Models\QuizSiswaHistory;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quiz_siswa_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(QuizSiswaHistory::class)->constrained('quiz_siswa_histories')->cascadeOnUpdate()->cascadeOnUpdate();
            $table->foreignIdFor(User::class, 'siswa_id')->constrained('users');
            $table->foreignIdFor(QuizBankSoal::class)->constrained('quiz_bank_soals');
            $table->foreignIdFor(QuizJawabanSoal::class)->nullable()->constrained('quiz_jawaban_soals')->nullOnDelete()->nullOnUpdate();
            $table->boolean('is_correct')->default(false);
            $table->text('jawaban_texts')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_siswa_jawabans');
    }
};
