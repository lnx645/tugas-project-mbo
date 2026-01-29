<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizBankSoal extends Model
{
    protected $table = 'quiz_bank_soals';

    protected $fillable = [
        'quiz_category_id',
        'user_id',
        'pertanyaan',
        'user_id',
        'tipe',
        'bobot',
        'lampiran_gambar',
    ];
    public function category(): BelongsTo
    {
        return $this->belongsTo(QuizCategory::class, 'quiz_category_id');
    }
    public function jawaban()
    {
        return $this->hasMany(QuizJawabanSoal::class, 'quiz_bank_soal_id');
    }
    public function kunci_jawaban()
    {
        return $this->hasOne(QuizJawabanSoal::class, 'quiz_bank_soal_id')->where('apakah_benar', true);
    }
}
