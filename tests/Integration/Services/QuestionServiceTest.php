<?php
declare(strict_types=1);

namespace Tests\Integration\Services;

use App\Models\Question;
use App\Models\Score;
use App\Services\QuestionService;
use App\Services\QuestionServiceInterface;
use Tests\Integration\DatabaseDependentTestCase;
use function assert;
use function factory;

final class QuestionServiceTest extends DatabaseDependentTestCase
{
    private QuestionServiceInterface $service;

    /**
     * @before 
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(QuestionServiceInterface::class);
    }

    /**
     * @test 
     */
    public function canGetAllQuestionsAndCorrectScores(): void
    {
        $question = factory(Question::class)->create();
        assert($question instanceof Question);
        factory(Score::class)->create(
            [
            'question_id' => $question->id,
            'answer' => $question->answer
            ]
        );

        $allQuestionsAndScore = $this->service->getAllQuestionsAndScore();
        self::assertCount(1, $allQuestionsAndScore);
        self::assertSame(QuestionService::COMPLETE, $allQuestionsAndScore[0]['score']);

        $question = factory(Question::class)->create();
        assert($question instanceof Question);
        factory(Score::class)->create(
            [
            'question_id' => $question->id,
            'answer' => 'different answer'
            ]
        );
        $allQuestionsAndScore = $this->service->getAllQuestionsAndScore();
        self::assertCount(2, $allQuestionsAndScore);
        $wrongQuestion = $allQuestionsAndScore->firstWhere('id', $question->id);
        self::assertSame('', $wrongQuestion['score']);
    }

    /**
     * @test 
     */
    public function canConfirmIfAllAnswersAreCorrect(): void
    {
        $questions = factory(Question::class, 2)->create();
        foreach($questions as $question) {
            factory(Score::class)->create(
                [
                'question_id' => $question->id,
                'answer' => $question->answer
                ]
            );
        }
        self::assertTrue($this->service->areAllQuestionsCorrectlyAnswered());
    }

    /**
     * @test 
     */
    public function canConfirmThereAreStillAnswersToFillIn(): void
    {
        factory(Question::class, 2)->create();
        self::assertFalse($this->service->areAllQuestionsCorrectlyAnswered());
    }
}
