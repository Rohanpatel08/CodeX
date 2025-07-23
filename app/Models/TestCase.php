<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCase extends Model
{
    use HasFactory;

    protected $table = 'question_test_cases';

    protected $fillable = [
        'question_id',
        'input',
        'expected_output',
        'is_hidden'
    ];

    protected $casts = [
        'is_hidden' => 'boolean'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
