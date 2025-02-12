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
        'number',
        'status',
    ];

    public function module()
    {
        return $this->belongsTo(modules::class);
    }
}
