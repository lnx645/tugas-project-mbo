<?php

use App\Models\JadwalQuiz;
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
        Schema::create('quiz_siswa_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(JadwalQuiz::class)->constrained('jadwal_quizzes');
            $table->dateTime('start_date')->nullable();
            $table->integer('remaining_time')->nullable();
            $table->foreignIdFor(User::class, 'siswa_id')->constrained('users');
            $table->dateTime('end_date')->nullable();
            $table->double('score_result')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_siswa_histories');
    }
};
