<?php

use App\Models\QuizCategory;
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
        Schema::create('quiz_bank_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(QuizCategory::class)->constrained('quiz_categories')->onDelete('cascade');
            $table->text('pertanyaan');
            $table->enum('tipe', ['pilihan_ganda', 'isian_singkat', 'essay', 'benar_salah']);
            $table->string('lampiran_gambar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_bank_soals');
    }
};
