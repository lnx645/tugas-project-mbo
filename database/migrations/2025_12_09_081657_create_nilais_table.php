<?php

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
        Schema::create('nilais', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tugas_id')
                ->constrained('tugas', 'tugasID')
                ->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'siswa_id')
                ->constrained('users', 'id')
                ->cascadeOnDelete();
            $table->foreignUuid('jawaban_id')
                ->nullable()
                ->constrained('jawaban_tugas', 'jawabanID')
                ->nullOnDelete();

            $table->integer('angka');
            $table->text('komentar')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilais');
    }
};
