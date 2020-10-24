<?php

namespace App\Repositories;

use App\Models\Question;

interface QuestionRepositoryInterface extends RepositoryInterface
{
    public function create(string $question, string $answer): Question;

    public function find(int $id): ?Question;
}
