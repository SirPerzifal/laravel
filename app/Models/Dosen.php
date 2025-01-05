<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen';

    protected $fillable = ['user_id', 'nidn', 'nama', 'prodi', 'jurusan', 'mata_kuliah'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke tabel quizzes
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}

