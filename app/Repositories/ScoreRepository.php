<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Question;
use App\Models\Score;
use Illuminate\Database\Eloquent\Collection;

final class ScoreRepository implements ScoreRepositoryInterface
{
    public function findAll(): Collection
    {
        return Score::all();
    }

    public function find(int $id): ?Score
    {
        return Score::find($id);
    }

    public function create(int $questionId, string $answer): Score
    {
        return Score::create(
            [
            'question_id' => $questionId,
            'answer' => $answer,
            ]
        );
    }

    public function deleteAll(): void
    {
        Score::truncate();
    }
}
