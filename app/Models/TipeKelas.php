<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipeKelas extends Model
{
    use HasFactory;

    protected $fillable = ['nama'];

    public function materials()
    {
        return $this->hasMany(Material::class);
    }
}
