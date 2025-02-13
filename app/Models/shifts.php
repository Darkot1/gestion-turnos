<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class shifts extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'module_id',
        'user_id',
        'number',
        'status',
        'date',
    ];

    public function module()
    {
        return $this->belongsTo(modules::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
