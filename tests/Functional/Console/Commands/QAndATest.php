<?php
declare(strict_types=1);

namespace Tests\Functional\Console\Commands;

use App\Console\Commands\QAndA;
use App\Models\Question;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use function assert;
use function factory;

final class QAndATest extends TestCase
{
    use InteractsWithDatabase;
    use RefreshDatabase;

    /**
     * @test 
     */
    public function confirmInstructionsAndCanImmediatelyLeave(): void
    {
        $this->artisan('qanda:interactive')
            ->expectsOutput('Welcome to the Q/A app!')
            ->expectsOutput('Write "b" or "back" at any step to go to the previous step!')
            ->expectsOutput('Write "q" or "quit" at any step to quit!')
            ->expectsChoice(
                'What do you want to do?', QAndA::LEAVE, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->assertExitCode(0);
    }

    /**
     * @test 
     */
    public function canAddAQuestion(): void
    {
        $question = 'Which day is today?';
        $answer = 'Monday';
        $this->artisan('qanda:interactive')
            ->expectsChoice(
                'What do you want to do?', QAndA::ADD_QUESTION, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->expectsQuestion('What is the question?', $question)
            ->expectsQuestion('What is the correct answer for the question?', $answer)
            ->expectsChoice(
                'What do you want to do?', QAndA::LEAVE, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->assertExitCode(0);

        $this->assertDatabaseHas(
            'questions', [
            'question' => $question,
            'answer' => $answer,
            ]
        );
    }

    /**
     * @test 
     */
    public function canPractiseAQuestion(): void
    {
        $question = factory(Question::class)->create();
        assert($question instanceof Question);

        $this->artisan('qanda:interactive')
            ->expectsChoice(
                'What do you want to do?', QAndA::PRACTISE, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->expectsOutput('List of available questions:')
            ->expectsTable(
                ['Id', 'Question', 'Score'],
                [[$question->id, $question->question, '']]
            )
            ->expectsQuestion('Choose an id to practise', $question->id)
            ->expectsQuestion($question->question, $question->answer)
            ->expectsTable(
                ['Id', 'Question', 'Score'],
                [[$question->id, $question->question, 'Done']]
            )
            ->assertExitCode(0);

        self::assertDatabaseHas(
            'questions', [
            'id' => $question->id,
            'question' => $question->question,
            'answer' => $question->answer,
            ]
        );
        self::assertDatabaseHas(
            'scores', [
            'question_id' => $question->id,
            'answer' => $question->answer,
            ]
        );
    }

    /**
     * @test 
     */
    public function sendsMessageWhenNoQuestionsAdded(): void
    {
        $this->artisan('qanda:interactive')
            ->expectsChoice(
                'What do you want to do?', QAndA::PRACTISE, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->expectsOutput('You still have to add some questions to practise')
            ->expectsChoice(
                'What do you want to do?', QAndA::LEAVE, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->assertExitCode(0);
    }

    /**
     * @test
     * @dataProvider getBackOptions
     */
    public function canGoBackWhileCreatingAQuestion(string $answer): void
    {
        $this->artisan('qanda:interactive')
            ->expectsChoice(
                'What do you want to do?', QAndA::ADD_QUESTION, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->expectsQuestion('What is the question?', $answer)
            ->expectsChoice(
                'What do you want to do?', QAndA::LEAVE, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->assertExitCode(0);
    }

    /**
     * @test
     * @dataProvider getBackOptions
     */
    public function canGoBackWhileAddingAQuestion(string $answer): void
    {
        $this->artisan('qanda:interactive')
            ->expectsChoice(
                'What do you want to do?', QAndA::ADD_QUESTION, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->expectsQuestion('What is the question?', 'What is the question?')
            ->expectsQuestion('What is the correct answer for the question?', $answer)
            ->expectsQuestion('What is the question?', $answer)
            ->expectsChoice(
                'What do you want to do?', QAndA::LEAVE, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->assertExitCode(0);
    }

    /**
     * @test
     * @dataProvider getBackOptions
     */
    public function canGoBackWhileChoosingIdToPractiseAQuestion(string $answer): void
    {
        $question = factory(Question::class)->create();
        assert($question instanceof Question);

        $this->artisan('qanda:interactive')
            ->expectsChoice(
                'What do you want to do?', QAndA::PRACTISE, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->expectsTable(
                ['Id', 'Question', 'Score'],
                [[$question->id, $question->question, '']]
            )
            ->expectsQuestion('Choose an id to practise', $answer)
            ->expectsChoice(
                'What do you want to do?', QAndA::LEAVE, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->assertExitCode(0);
    }

    /**
     * @test
     * @dataProvider getBackOptions
     */
    public function canGoBackAfterChoosingIdToPractiseAQuestion(string $answer): void
    {
        $question = factory(Question::class)->create();
        assert($question instanceof Question);

        $this->artisan('qanda:interactive')
            ->expectsChoice(
                'What do you want to do?', QAndA::PRACTISE, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->expectsTable(
                ['Id', 'Question', 'Score'],
                [[$question->id, $question->question, '']]
            )
            ->expectsQuestion('Choose an id to practise', $answer)
            ->expectsChoice(
                'What do you want to do?', QAndA::LEAVE, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->assertExitCode(0);
    }

    /**
     * @test
     * @dataProvider getBackOptions
     */
    public function canGoBackWhileAnsweringPractiseAQuestion(string $answer): void
    {
        $question = factory(Question::class)->create();
        assert($question instanceof Question);

        $this->artisan('qanda:interactive')
            ->expectsChoice(
                'What do you want to do?', QAndA::PRACTISE, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->expectsTable(
                ['Id', 'Question', 'Score'],
                [[$question->id, $question->question, '']]
            )
            ->expectsQuestion('Choose an id to practise', $question->id)
            ->expectsQuestion($question->question, $answer)
            ->expectsTable(
                ['Id', 'Question', 'Score'],
                [[$question->id, $question->question, '']]
            )
            ->expectsQuestion('Choose an id to practise', $answer)
            ->expectsChoice(
                'What do you want to do?', QAndA::LEAVE, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->assertExitCode(0);
    }

    public function getBackOptions(): array
    {
        return [
            'answer b' => ['b'],
            'answer back' => ['back'],
        ];
    }

    /**
     * @test
     * @dataProvider getQuitOptions
     */
    public function canQuitWhileCreatingAQuestion(string $answer): void
    {
        $this->artisan('qanda:interactive')
            ->expectsChoice(
                'What do you want to do?', QAndA::ADD_QUESTION, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->expectsQuestion('What is the question?', $answer)
            ->assertExitCode(0);
    }

    /**
     * @test
     * @dataProvider getQuitOptions
     */
    public function canQuitWhileAddingAQuestion(string $answer): void
    {
        $this->artisan('qanda:interactive')
            ->expectsChoice(
                'What do you want to do?', QAndA::ADD_QUESTION, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->expectsQuestion('What is the question?', $answer)
            ->assertExitCode(0);
    }

    /**
     * @test
     * @dataProvider getQuitOptions
     */
    public function canQuitWhileChoosingIdToPractiseAQuestion(string $answer): void
    {
        $question = factory(Question::class)->create();
        assert($question instanceof Question);

        $this->artisan('qanda:interactive')
            ->expectsChoice(
                'What do you want to do?', QAndA::PRACTISE, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->expectsTable(
                ['Id', 'Question', 'Score'],
                [[$question->id, $question->question, '']]
            )
            ->expectsQuestion('Choose an id to practise', $answer)
            ->assertExitCode(0);
    }

    /**
     * @test
     * @dataProvider getQuitOptions
     */
    public function canQuitWhileAnsweringPractiseAQuestion(string $answer): void
    {
        $question = factory(Question::class)->create();
        assert($question instanceof Question);

        $this->artisan('qanda:interactive')
            ->expectsChoice(
                'What do you want to do?', QAndA::PRACTISE, [
                QAndA::ADD_QUESTION,
                QAndA::PRACTISE,
                QAndA::LEAVE,
                ]
            )
            ->expectsTable(
                ['Id', 'Question', 'Score'],
                [[$question->id, $question->question, '']]
            )
            ->expectsQuestion('Choose an id to practise', $question->id)
            ->expectsQuestion($question->question, $answer)
            ->assertExitCode(0);
    }

    public function getQuitOptions(): array
    {
        return [
            'answer q' => ['q'],
            'answer quit' => ['quit'],
        ];
    }
}
