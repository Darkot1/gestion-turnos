<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes;  // Añadido SoftDeletes

    protected $fillable = [
        'name', 'email', 'document', 'password', 'role',
    ];

    protected $dates = [
        'deleted_at'
    ];
}
