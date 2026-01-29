<?php

use App\Models\QuizBankSoal;
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
        Schema::create('quiz_jawaban_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(QuizBankSoal::class)->constrained('quiz_bank_soals')->onDelete('cascade');
            $table->text('teks_jawaban');
            $table->boolean('apakah_benar')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_jawaban_soals');
    }
};
