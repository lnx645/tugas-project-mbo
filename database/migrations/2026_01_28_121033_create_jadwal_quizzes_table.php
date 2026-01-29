<?php

use App\Models\Kelas;
use App\Models\QuizBankSoal;
use App\Models\QuizCategory;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('jadwal_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(QuizCategory::class)->constrained('quiz_categories')->onDelete('cascade');
            $table->foreignIdFor(Kelas::class)->constrained('kelas')->onDelete('cascade');
            $table->foreignIdFor(User::class)->constrained('users');
            $table->string('judul');
            $table->integer('total_soal');
            $table->integer('kkm')->default(75);
            $table->dateTime('mulai');
            $table->dateTime('selesai');
            $table->integer('durasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_quizzes');
    }
};
