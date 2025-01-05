<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerUpUsage extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'power_up_id', 'quiz_id', 'used_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function powerUp()
    {
        return $this->belongsTo(PowerUp::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}

