<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'file_path', 'dosen_id', 'tipe_kelas_id'];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function tipeKelas()
    {
        return $this->belongsTo(TipeKelas::class);
    }
}
