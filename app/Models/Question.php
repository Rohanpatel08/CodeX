<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'input_format',
        'output_format',
        'constraints'
    ];

    public function testCases()
    {
        return $this->hasMany(TestCase::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
