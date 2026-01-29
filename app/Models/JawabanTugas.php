<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanTugas extends Model
{
    /** @use HasFactory<\Database\Factories\JawabanTugasFactory> */
    protected $primaryKey = 'jawabanID';
    use HasFactory, HasUuids;
    protected $fillable = [
        'tugas_id',
        'answered_by_id',
        'jawaban',
        'file',
        'file_url',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'answered_by_id');
    }
    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }
    public function nilai()
    {
        return $this->hasOne(Nilai::class, 'jawaban_id', 'jawabanID');
    }
}
