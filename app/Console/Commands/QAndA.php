<?php

namespace App\Console\Commands;

use App\Services\QuestionService;
use App\Services\ScoreService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use function count;
use function in_array;
use function trim;

class QAndA extends Command
{
    public const ADD_QUESTION = 'Add question';
    public const PRACTISE = 'Practise';
    public const LEAVE = 'Leave';

    /**
     * @inheritdoc 
     */
    protected $signature = 'qanda:interactive';

    /**
     * @inheritdoc 
     */
    protected $description = 'Runs an interactive command line based Q And A system.';

    private bool $keepAsking = true;
    private QuestionService $questionService;
    private ScoreService $scoreService;


    public function __construct(QuestionService $questionService, ScoreService $scoreService)
    {
        parent::__construct();
        $this->questionService = $questionService;
        $this->scoreService = $scoreService;
    }

    public function handle(): int
    {
        $this->info('Welcome to the Q/A app!');
        $this->info('Write "b" or "back" at any step to go to the previous step!');
        $this->info('Write "q" or "quit" at any step to quit!');

        return $this->executeQAndA();
    }

    private function executeQAndA(): int
    {
        $choice = $this->choice(
            'What do you want to do?', [
            self::ADD_QUESTION,
            self::PRACTISE,
            self::LEAVE,
            ]
        );

        switch ($choice) {
        case self::ADD_QUESTION:
            $this->addQuestion();
            break;
        case self::PRACTISE:
            $this->practise();
            break;
        case self::LEAVE:
            $this->keepAsking = false;
            break;
        }

        if ($this->keepAsking === true) {
            $this->executeQAndA();
        }

        return 0;
    }


    private function addQuestion(): void
    {
        $question = $this->askForInput('What is the question?');
        if ($this->isBackOrQuitOption($question)) {
            return;
        }

        $answer = $this->askForInput('What is the correct answer for the question?');
        if ($this->isBackOrQuitOption($answer, [$this, 'addQuestion'])) {
            return;
        }
        $this->questionService->create($question, $answer);
    }

    private function practise(): void
    {
        $questionsAndScore = $this->questionService->getAllQuestionsAndScore();
        if ($questionsAndScore->isEmpty()) {
            $this->info('You still have to add some questions to practise');

            return;
        }

        if ($this->allQuestionsAnswered($questionsAndScore)) {
            return;
        }
        $this->showListOfQuestionsAndProgress($questionsAndScore, 'List of available questions:');

        $id = 0;
        $question = null;
        while ($id === 0) {
            $id = trim($this->ask('Choose an id to practise'));
            if ($this->isBackOrQuitOption($id)) {
                return;
            }

            $id = (int) $id;
            $question = $questionsAndScore->firstWhere('id', $id);
            if ($question === null) {
                $id = 0;
            }
        }

        $answer = $this->askForInput($question['question']);
        if ($this->isBackOrQuitOption($answer, [$this, 'practise'])) {
            return;
        }

        $this->scoreService->createQuestionScore($id, $answer);
        $this->practise();
    }

    private function showListOfQuestionsAndProgress(Collection $questionsAndScore, string $message = ''): void
    {
        $this->line($message);
        $header = ['Id', 'Question', 'Score'];
        $this->table($header, $questionsAndScore);
        $bar = $this->output->createProgressBar(count($questionsAndScore));
        $bar->advance(count($questionsAndScore->where('score', QuestionService::COMPLETE)));
        $this->output->newLine();
    }

    private function isBackOrQuitOption(string $answer, ?callable $callable = null): bool
    {
        if (in_array($answer, ['q', 'quit'], true)) {
            $this->keepAsking = false;
            return true;
        }

        $isBack = in_array($answer, ['b', 'back'], true);
        if ($isBack === true && $callable !== null) {
            $callable();
        }

        return $isBack;
    }

    private function allQuestionsAnswered(Collection $questionsAndScore): bool
    {
        if ($this->questionService->areAllQuestionsCorrectlyAnswered($questionsAndScore) === false) {
            return false;
        }
        $this->showListOfQuestionsAndProgress(
            $questionsAndScore,
            "Congratulations!! All answers are correct!\nFinal Progress"
        );
        $this->keepAsking = false;

        return true;
    }

    private function askForInput(string $ask): string
    {
        $input = '';
        while ($input === '') {
            $input = trim($this->ask($ask));
        }

        return $input;
    }
}
