<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $table = 'income';

    protected $fillable = [
        'user_id',
        'name',
        'amount',
        'date',
        'mods',
    ];

    protected $casts = [
        'mods' => 'array',
    ];
} 