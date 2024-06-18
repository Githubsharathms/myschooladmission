<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signup extends Model
{
    use HasFactory;

    protected $table = 'signup';

    protected $fillable = [
        'mobile_number',
        'otp',
        'name',
    ];
}
