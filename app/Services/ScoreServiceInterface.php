<?php

namespace App\Services;

use App\Models\Score;

interface ScoreServiceInterface
{
    public function createQuestionScore(int $id, string $answer): Score;

    public function deleteAllScores(): void;
}
