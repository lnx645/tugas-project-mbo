<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Guru extends Model
{
    /** @use HasFactory<\Database\Factories\GuruFactory> */
    use HasFactory;
    protected $primaryKey = 'nip';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'nip',
        'jenis_kelamin',
        'foto',
        'status',
        'user_id',
        'matpel_kode',
        'gelar_depan',
        'gelar_belakang',
    ];
    protected $appends = ['foto_url'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function spesialisMatpel(): BelongsTo
    {
        return $this->belongsTo(Matpel::class, 'matpel_kode', 'kode');
    }
    public function getFotoUrlAttribute()
    {
        if ($this->foto && Storage::disk('public')->exists($this->foto)) {
            return asset('storage/' . $this->foto);
        }

        return asset('images/default-avatar.png');
    }
    public function matpels()
    {
        return $this->belongsToMany(
            Matpel::class,
            'pengajarans',
            'guru_nip',
            'matpel_kode',
            'nip',
            'kode'
        )->withPivot('kelas_id');
    }
    public function pengajarans()
    {
        return $this->hasMany(Pengajaran::class, 'guru_nip', 'nip');
    }
}
