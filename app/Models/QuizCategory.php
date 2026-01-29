<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizCategory extends Model
{
    protected $fillable = ['nama', 'user_id'];
    public function bankSoals(): HasMany
    {
        return $this->hasMany(QuizBankSoal::class, 'quiz_category_id');
    }
    public function jadwal()
    {
        return $this->hasMany(JadwalQuiz::class);
    }
}
