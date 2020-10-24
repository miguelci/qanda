<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ScoreService;
use Illuminate\Console\Command;

final class QAndAResetScore extends Command
{
    /**
     * @inheritdoc 
     */
    protected $signature = 'qanda:reset';

    /**
     * @inheritdoc 
     */
    protected $description = 'Resets all the score from the Q and A system.';

    public function handle(ScoreService $service): int
    {
        $this->warn('This will delete all the score achieved till now.');
        $answer = $this->confirm('Are you sure you want to continue?');

        if ($answer === true) {
            $service->deleteAllScores();
        }

        return 0;
    }

}
