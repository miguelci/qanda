<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Question;
use App\Models\Score;
use Faker\Generator as Faker;

$factory->define(Score::class, function (Faker $faker) {
    return [
        'question_id' => factory(Question::class),
        'answer' => static function (array $score): string {
            return Question::find($score['question_id'])->answer;
        },
    ];
});
