<?php

namespace App\Providers;

use App\Repositories\QuestionRepository;
use App\Repositories\QuestionRepositoryInterface;
use App\Repositories\ScoreRepository;
use App\Repositories\ScoreRepositoryInterface;
use App\Services\QuestionService;
use App\Services\QuestionServiceInterface;
use App\Services\ScoreService;
use App\Services\ScoreServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->alias(QuestionRepository::class, QuestionRepositoryInterface::class);
        $this->app->alias(QuestionService::class, QuestionServiceInterface::class);

        $this->app->alias(ScoreRepository::class, ScoreRepositoryInterface::class);
        $this->app->alias(ScoreService::class, ScoreServiceInterface::class);
    }
}
