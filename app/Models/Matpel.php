<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matpel extends Model
{
    /** @use HasFactory<\Database\Factories\MatpelFactory> */
    use HasFactory;
    protected $fillable = [
        'kode',
        'nama',
        'kelompok',
        'kategori'
    ];
    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'kode';

    public function guru()
    {
        return $this->belongsToMany(Guru::class, 'pengajarans', 'guru_nip');
    }
    public function kelas()
    {
        return $this->belongsTo(
            Kelas::class,           // Model Tujuan
            'pengajarans',      // Nama Tabel Pivot (tabel di migrasi kamu)
            'matpel_kode',              // Foreign Key di tabel pivot untuk model ini (Guru)
            'kelas_id',           // Foreign Key di tabel pivot untuk model lawan (Matpel)
            'kode',                   // Primary Key lokal (Guru)
            'id'                   // Primary Key lawan (Matpel)
        );    // Opsional: Jika ingin mengambil data kelas_id juga
    }
}
