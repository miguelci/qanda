<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Score;
use App\Repositories\ScoreRepositoryInterface;

final class ScoreService implements ScoreServiceInterface
{
    private ScoreRepositoryInterface $repository;

    public function __construct(ScoreRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function createQuestionScore(int $id, string $answer): Score
    {
        return $this->repository->create($id, $answer);
    }

    public function deleteAllScores(): void
    {
        $this->repository->deleteAll();
    }
}
