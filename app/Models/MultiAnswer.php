<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultiAnswer extends Model
{
    use HasFactory;

    protected $table = 'multi_answers';

    protected $fillable = ['question_id', 'text', 'is_true_answer'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}

