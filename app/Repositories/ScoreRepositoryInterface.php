<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Question;
use App\Models\Score;

interface ScoreRepositoryInterface extends RepositoryInterface
{
    public function create(int $questionId, string $answer): Score;

    public function deleteAll(): void;
}
