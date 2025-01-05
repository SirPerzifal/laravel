<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipeQuiz extends Model
{
    use HasFactory;

    protected $table = 'tipe_quiz';

    protected $fillable = ['nama'];
}

