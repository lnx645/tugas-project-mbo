<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalQuiz extends Model
{
    protected $fillable = [
        'quiz_category_id',
        'kelas_id',
        'user_id',
        'judul',
        'total_soal',
        'max_attempts',
        'kkm',
        'mulai',
        'selesai',
        'durasi',

    ];
    protected $casts = [
        'mulai' => 'datetime',
        'selesai' => 'datetime',
    ];
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
    public function bankSoalGroup()
    {
        return $this->belongsTo(QuizCategory::class, 'quiz_category_id');
    }
}
