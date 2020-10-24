<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Score;
use App\Repositories\ScoreRepositoryInterface;
use App\Services\ScoreService;
use App\Services\ScoreServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ScoreServiceTest extends TestCase
{
    private ScoreServiceInterface $service;
    /**
     * @var ScoreRepositoryInterface&MockObject 
     */
    private ScoreRepositoryInterface $repository;

    /**
     * @before 
     */
    public function createDependencies(): void
    {
        $this->repository = $this->createMock(ScoreRepositoryInterface::class);
        $this->service = new ScoreService($this->repository);
    }

    /**
     * @test 
     */
    public function canCreate(): void
    {
        $id = 1;
        $answer = 'answer';

        $this->repository->expects(self::once())
            ->method('create')
            ->with($id, $answer)
            ->willReturn(new Score(['question_id' => $id, 'answer' => $answer]));

        $score = $this->service->createQuestionScore($id, $answer);

        self::assertSame($id, $score->question_id);
        self::assertSame($answer, $score->answer);
    }

    /**
     * @test 
     */
    public function canDeleteAllScores(): void
    {
        $this->repository->expects(self::once())
            ->method('deleteAll');
        $this->service->deleteAllScores();
    }
}
