<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Question;
use App\Repositories\QuestionRepositoryInterface;
use Illuminate\Support\Collection;

final class QuestionService implements QuestionServiceInterface
{
    public const COMPLETE = 'Done';

    private QuestionRepositoryInterface $repository;

    public function __construct(QuestionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(string $question, string $answer): Question
    {
        return $this->repository->create($question, $answer);
    }

    public function getAllQuestionsAndScore(): Collection
    {
        return $this->repository->findAll()->map(
            static function (Question $question): array {
                return [
                'id' => $question->id,
                'question' => $question->question,
                'score' => $question->has_correct_answer ? self::COMPLETE : '',
                ];
            }
        );
    }

    public function areAllQuestionsCorrectlyAnswered(?Collection $questionsAndScore = null): bool
    {
        if ($questionsAndScore === null) {
            $questionsAndScore = $this->getAllQuestionsAndScore();
        }

        return $questionsAndScore->every(
            static function (array $question) {
                return $question['score'] === QuestionService::COMPLETE;
            }
        );
    }
}
