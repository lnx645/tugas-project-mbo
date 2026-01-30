<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizSiswaJawaban extends Model
{
    protected $fillable = [
        'quiz_siswa_history_id',
        'siswa_id',
        'quiz_bank_soal_id',
        'quiz_jawaban_soal_id',
        'is_correct',
        'jawaban_texts'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];
    public function soal()
    {
        return $this->belongsTo(QuizBankSoal::class, 'quiz_bank_soal_id');
    }
    public function jawaban()
    {
        return $this->belongsTo(QuizJawabanSoal::class, 'quiz_jawaban_soal_id');
    }
    public function history()
    {
        return $this->belongsTo(QuizSiswaHistory::class, 'quiz_siswa_history_id');
    }
}
