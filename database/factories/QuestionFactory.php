<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Question;
use Faker\Generator as Faker;

$factory->define(Question::class, static function (Faker $faker): array {
    return [
        'question' => $faker->sentence(12),
        'answer' => $faker->sentence(12),
    ];
});
