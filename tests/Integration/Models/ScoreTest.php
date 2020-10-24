<?php
declare(strict_types=1);

namespace Tests\Integration\Models;

use App\Models\Question;
use App\Models\Score;
use Tests\Integration\DatabaseDependentTestCase;
use function assert;
use function factory;

final class ScoreTest extends DatabaseDependentTestCase
{
    /**
     * @test 
     */
    public function canCreateAScore(): void
    {
        $score = factory(Score::class)->create();
        assert($score instanceof Score);

        $this->assertDatabaseHas(
            'scores', [
            'id' => $score->id,
            'answer' => $score->answer,
            ]
        );
    }

    /**
     * @test 
     */
    public function canCreateAScoreWithAQuestion(): void
    {
        $question = factory(Question::class)->create();
        assert($question instanceof Question);
        $score = factory(Score::class)->create(
            [
            'question_id' => $question->id,
            'answer' => $question->answer,
            ]
        );
        assert($score instanceof Score);

        $this->assertDatabaseHas(
            'scores', [
            'id' => $score->id,
            'question_id' => $question->id,
            'answer' => $score->answer,
            ]
        );
    }
}
