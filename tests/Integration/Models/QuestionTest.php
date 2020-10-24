<?php
declare(strict_types=1);

namespace Tests\Integration\Models;

use App\Models\Question;
use App\Models\Score;
use Tests\Integration\DatabaseDependentTestCase;
use function assert;
use function factory;

final class QuestionTest extends DatabaseDependentTestCase
{
    /**
     * @test 
     */
    public function canCreateAQuestion(): void
    {
        $question = factory(Question::class)->create();
        assert($question instanceof Question);

        $this->assertDatabaseHas(
            'questions', [
            'id' => $question->id,
            'question' => $question->question,
            'answer' => $question->answer,
            ]
        );
    }

    /**
     * @test 
     */
    public function questionScoreIsPositive(): void
    {
        $question = factory(Question::class)->create();
        assert($question instanceof Question);
        factory(Score::class)->create(
            [
            'question_id' => $question->id,
            'answer' => $question->answer,
            ]
        );
        factory(Score::class)->create(
            [
            'question_id' => $question->id,
            'answer' => 'wrong_answer',
            ]
        );

        self::assertTrue($question->has_correct_answer);
    }

    /**
     * @test 
     */
    public function questionScoreIsNegative(): void
    {
        $question = factory(Question::class)->create();
        assert($question instanceof Question);
        factory(Score::class)->create(
            [
            'question_id' => $question->id,
            'answer' => 'wrong answer',
            ]
        );

        self::assertFalse($question->has_correct_answer);
    }
}
