<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LongAnswer extends Model
{
    use HasFactory;

    protected $table = 'long_answers';
    
    protected $fillable = ['question_id', 'true_answer'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}

