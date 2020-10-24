<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Support\Collection;

interface QuestionServiceInterface
{
    public function create(string $question, string $answer): Question;

    public function getAllQuestionsAndScore(): Collection;

    public function areAllQuestionsCorrectlyAnswered(?Collection $questionsAndScore = null): bool;
}
