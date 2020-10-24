<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories;

use App\Models\Question;
use App\Repositories\ScoreRepositoryInterface;
use App\Models\Score;
use Tests\Integration\DatabaseDependentTestCase;
use function assert;
use function factory;

final class ScoreRepositoryTest extends DatabaseDependentTestCase
{
    private ScoreRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(ScoreRepositoryInterface::class);
    }

    /**
     * @test 
     */
    public function canFindAll(): void
    {
        factory(Score::class, 2)->create();
        self::assertCount(2, $this->repository->findAll());
    }

    /**
     * @test 
     */
    public function canFind(): void
    {
        $score = factory(Score::class)->create();
        assert($score instanceof Score);
        self::assertSame($score->answer, $this->repository->find($score->id)->answer);
    }

    /**
     * @test 
     */
    public function canCreate(): void
    {
        $score = $this->repository->create(factory(Question::class)->create()->id, 'An answer');

        self::assertCount(1, $this->repository->findAll());

        $foundScore = $this->repository->find($score->id);
        self::assertSame($score->id, $foundScore->id);
        self::assertSame($score->question->id, $foundScore->question->id);
        self::assertSame($score->answer, $foundScore->answer);
    }
}
