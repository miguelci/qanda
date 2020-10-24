<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Question;
use App\Repositories\QuestionRepositoryInterface;
use App\Services\QuestionService;
use App\Services\QuestionServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use function factory;

final class QuestionServiceTest extends TestCase
{
    private QuestionServiceInterface $service;
    /**
     * @var QuestionRepositoryInterface&MockObject 
     */
    private QuestionRepositoryInterface $repository;

    /**
     * @before 
     */
    public function createDependencies(): void
    {
        $this->repository = $this->createMock(QuestionRepositoryInterface::class);
        $this->service = new QuestionService($this->repository);
    }

    /**
     * @test 
     */
    public function createAnInstance(): void
    {
        $this->repository->expects(self::once())
            ->method('create')
            ->with('question', 'answer')
            ->willReturn(new Question(['question' => 'question', 'answer' => 'answer']));

        $question = $this->service->create('question', 'answer');
        self::assertSame('question', $question->question);
        self::assertSame('answer', $question->answer);
    }

    /**
     * @test 
     */
    public function getAllQuestionsAndScore(): void
    {
        $this->repository->expects(self::once())
            ->method('findAll')
            ->willReturn(new Collection([]));

        self::assertEmpty($this->service->getAllQuestionsAndScore());
    }
}
