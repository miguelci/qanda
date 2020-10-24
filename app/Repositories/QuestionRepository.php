<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Collection;

final class QuestionRepository implements QuestionRepositoryInterface
{
    public function findAll(): Collection
    {
        return Question::all();
    }

    public function find(int $id): ?Question
    {
        return Question::find($id);
    }

    public function create(string $question, string $answer): Question
    {
        return Question::create(
            [
            'question' => $question,
            'answer' => $answer,
            ]
        );
    }
}
