<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';

    protected $fillable = ['quiz_id', 'question_text', 'tipe_jawaban_id'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function tipeJawaban()
    {
        return $this->belongsTo(TipeJawaban::class);
    }

    public function long_answer()
    {
        return $this->hasOne(LongAnswer::class);
    }

    public function multi_answer()
    {
        return $this->hasMany(MultiAnswer::class);
    }
}

