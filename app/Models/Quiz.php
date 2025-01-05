<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $table = 'quizzes';

    protected $fillable = ['id', 'judul', 'deskripsi', 'dosen_id', 'tipe_kelas_id', 'tipe_quiz_id', 'time_end'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id', 'id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function tipeKelas()
    {
        return $this->belongsTo(TipeKelas::class);
    }

    public function tipeQuiz()
    {
        return $this->belongsTo(TipeQuiz::class);
    }
}

