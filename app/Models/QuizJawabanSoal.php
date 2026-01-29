<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizJawabanSoal extends Model
{
    use HasFactory;

    /**
     * Properti fillable agar bisa melakukan mass assignment.
     */
    protected $fillable = [
        'quiz_bank_soal_id',
        'teks_jawaban',
        'apakah_benar',
    ];

    /**
     * Relasi ke soal (Kebalikan dari hasMany).
     */
    public function soal(): BelongsTo
    {
        return $this->belongsTo(QuizBankSoal::class, 'quiz_bank_soal_id');
    }
}
