<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizSiswaHistory extends Model
{
    protected $fillable = [
        'jadwal_quiz_id',
        'siswa_id',
        'start_date',
        'end_date',
        'percobaan_ke',
        'remaining_time',
        'score_result',
    ];
    protected $casts = [
        'start_date' => 'datetime:Y-m-d H:i:s',
        'end_date' => 'datetime:Y-m-d H:i:s',
        'score_result' => 'double',
        'remaining_time' => 'integer',
    ];
    public function jadwalQuiz()
    {
        return $this->belongsTo(JadwalQuiz::class);
    }

    // Relasi ke Siswa (User)
    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }
    public function jawaban_tersimpan()
    {
        return $this->hasMany(QuizSiswaJawaban::class, 'quiz_siswa_history_id');
    }
}
