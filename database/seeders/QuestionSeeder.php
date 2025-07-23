<?php

// database/seeders/QuestionSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;

class QuestionSeeder extends Seeder
{

    public function run()
    {
        // 1. Two Sum Problem
        $question1 = Question::create([
            'title' => 'Two Sum',
            'description' => 'Given an array of integers nums and an integer target, return indices of the two numbers such that they add up to target. You may assume that each input would have exactly one solution, and you may not use the same element twice.',
            'input_format' => 'Array of integers and target integer',
            'output_format' => 'Array of two indices',
            'constraints' => '2 <= nums.length <= 104, -109 <= nums[i] <= 109, -109 <= target <= 109'
        ]);

        $question1->testCases()->createMany([
            [
                'question_id' => $question1->id,
                'input' => '([2,7,11,15], 9)',
                'expected_output' => '[0,1]',
                'is_hidden' => false
            ],
            [
                'question_id' => $question1->id,
                'input' => '([3,2,4], 6)',
                'expected_output' => '[1,2]',
                'is_hidden' => false
            ],
            [
                'question_id' => $question1->id,
                'input' => '([3,3], 6)',
                'expected_output' => '[0,1]',
                'is_hidden' => true
            ]
        ]);

        // 2. Valid Palindrome
        $question2 = Question::create([
            'title' => 'Valid Palindrome',
            'description' => 'A phrase is a palindrome if, after converting all uppercase letters into lowercase letters and removing all non-alphanumeric characters, it reads the same forward and backward. Given a string s, return true if it is a palindrome, or false otherwise.',
            'input_format' => 'String s',
            'output_format' => 'Boolean (true/false)',
            'constraints' => '1 <= s.length <= 2 * 105, s consists only of printable ASCII characters.'
        ]);

        $question2->testCases()->createMany([
            [
                'question_id' => $question2->id,
                'input' => '("A man, a plan, a canal: Panama")',
                'expected_output' => 'true',
                'is_hidden' => false
            ],
            [
                'question_id' => $question2->id,
                'input' => '("race a car")',
                'expected_output' => 'false',
                'is_hidden' => false
            ],
            [
                'question_id' => $question2->id,
                'input' => '("Madam")',
                'expected_output' => 'true',
                'is_hidden' => true
            ]
        ]);

        // 3. Array Sum
        $question3 = Question::create([
            'title' => 'Array Sum',
            'description' => 'Given an array of integers, return the sum of all elements in the array.',
            'input_format' => 'Array of integers',
            'output_format' => 'Integer (sum of all elements)',
            'constraints' => '1 <= array.length <= 1000, -1000 <= array[i] <= 1000'
        ]);

        $question3->testCases()->createMany([
            [
                'question_id' => $question3->id,
                'input' => '([1, 2, 3, 4, 5])',
                'expected_output' => '15',
                'is_hidden' => false
            ],
            [
                'question_id' => $question3->id,
                'input' => '([])',
                'expected_output' => '0',
                'is_hidden' => false
            ],
            [
                'question_id' => $question3->id,
                'input' => '([-1, -2, -3])',
                'expected_output' => '-6',
                'is_hidden' => true
            ]
        ]);

        // 4. Fibonacci Number
        $question4 = Question::create([
            'title' => 'Fibonacci Number',
            'description' => 'The Fibonacci numbers, commonly denoted F(n) form a sequence, called the Fibonacci sequence, such that each number is the sum of the two preceding ones, starting from 0 and 1. Given n, calculate F(n).',
            'input_format' => 'Integer n',
            'output_format' => 'Integer F(n)',
            'constraints' => '0 <= n <= 30'
        ]);

        $question4->testCases()->createMany([
            [
                'question_id' => $question4->id,
                'input' => '(5)',
                'expected_output' => '5',
                'is_hidden' => false
            ],
            [
                'question_id' => $question4->id,
                'input' => '(8)',
                'expected_output' => '21',
                'is_hidden' => false
            ],
            [
                'question_id' => $question4->id,
                'input' => '(10)',
                'expected_output' => '55',
                'is_hidden' => true
            ]
        ]);

        // 5. Valid Parentheses
        $question5 = Question::create([
            'title' => 'Valid Parentheses',
            'description' => 'Given a string s containing just the characters "(", ")", "{", "}", "[" and "]", determine if the input string is valid. An input string is valid if: Open brackets must be closed by the same type of brackets, and Open brackets must be closed in the correct order.',
            'input_format' => 'String s',
            'output_format' => 'Boolean (true/false)',
            'constraints' => '1 <= s.length <= 104, s consists of parentheses only "()[]{}".'
        ]);

        $question5->testCases()->createMany([
            [
                'question_id' => $question5->id,
                'input' => '("()")',
                'expected_output' => 'true',
                'is_hidden' => false
            ],
            [
                'question_id' => $question5->id,
                'input' => '("()[]{}")',
                'expected_output' => 'true',
                'is_hidden' => false
            ],
            [
                'question_id' => $question5->id,
                'input' => '("(]")',
                'expected_output' => 'false',
                'is_hidden' => true
            ]
        ]);

        // 6. Maximum Subarray
        $question6 = Question::create([
            'title' => 'Maximum Subarray',
            'description' => 'Given an integer array nums, find the contiguous subarray (containing at least one number) which has the largest sum and return its sum.',
            'input_format' => 'Array of integers',
            'output_format' => 'Integer (maximum sum)',
            'constraints' => '1 <= nums.length <= 105, -104 <= nums[i] <= 104'
        ]);

        $question6->testCases()->createMany([
            [
                'question_id' => $question6->id,
                'input' => '([-2,1,-3,4,-1,2,1,-5,4])',
                'expected_output' => '6',
                'is_hidden' => false
            ],
            [
                'question_id' => $question6->id,
                'input' => '([1])',
                'expected_output' => '1',
                'is_hidden' => false
            ],
            [
                'question_id' => $question6->id,
                'input' => '([-1,-2,-3,-4])',
                'expected_output' => '-1',
                'is_hidden' => true
            ]
        ]);
    }
}
