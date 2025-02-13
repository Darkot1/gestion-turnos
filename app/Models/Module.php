<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'status',
    ];


    public function shifts()
    {
        return $this->hasMany(Shifts::class);
    }

    public function hasActiveShift()
    {
        return $this->shifts()
            ->whereIn('status', ['espera', 'en proceso'])
            ->exists();
    }

}
