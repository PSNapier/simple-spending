<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spend extends Model
{
    use HasFactory;

    protected $table = 'spend';

    protected $fillable = [
        'user_id',
        'name',
        'amount',
        'date',
    ];
}
