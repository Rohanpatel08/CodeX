<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $table = 'submissions';

    protected $fillable = [
        'question_id',
        'user_id',
        'code',
        'language',
        // 'output',
        // 'status',
    ];
}
