<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = ['quiz_id', 'user_id', 'time_finish_record', 'nilai', 'how_much_use_power_up', 'how_much_win_streak'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

