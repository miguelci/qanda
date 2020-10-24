<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories;

use App\Models\Question;
use App\Repositories\QuestionRepository;
use App\Repositories\QuestionRepositoryInterface;
use Tests\Integration\DatabaseDependentTestCase;
use function assert;
use function factory;

final class QuestionRepositoryTest extends DatabaseDependentTestCase
{
    private QuestionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(QuestionRepositoryInterface::class);
    }

    /**
     * @test 
     */
    public function canFindAll(): void
    {
        factory(Question::class, 2)->create();
        self::assertCount(2, $this->repository->findAll());
    }

    /**
     * @test 
     */
    public function canFind(): void
    {
        $question = factory(Question::class)->create();
        assert($question instanceof Question);
        self::assertSame($question->question, $this->repository->find($question->id)->question);
    }

    /**
     * @test 
     */
    public function canCreate(): void
    {
        $question = $this->repository->create('A question', 'An answer');

        self::assertCount(1, $this->repository->findAll());

        $foundQuestion = $this->repository->find($question->id);
        self::assertSame($question->id, $foundQuestion->id);
        self::assertSame($question->question, $foundQuestion->question);
        self::assertSame($question->answer, $foundQuestion->answer);
    }
}
