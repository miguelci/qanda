<?php
declare(strict_types=1);

namespace Tests\Functional\Console\Commands;

use App\Console\Commands\QAndA;
use App\Models\Question;
use App\Models\Score;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use function factory;

final class QAndAResetScoreTest extends TestCase
{
    use InteractsWithDatabase;
    use RefreshDatabase;

    /**
     * @test 
     */
    public function willAskForConfirmationBeforeDelete(): void
    {
        $this->artisan('qanda:reset')
            ->expectsOutput('This will delete all the score achieved till now.')
            ->expectsConfirmation('Are you sure you want to continue?')
            ->assertExitCode(0);
    }

    /**
     * @test 
     */
    public function willDeleteAllScoresWhenConfirmed(): void
    {
        factory(Score::class, 2)->create();
        $this->assertDatabaseCount('scores', 2);

        $this->artisan('qanda:reset')
            ->expectsOutput('This will delete all the score achieved till now.')
            ->expectsConfirmation('Are you sure you want to continue?', 'yes')
            ->assertExitCode(0);

        $this->assertDatabaseCount('scores', 0);
    }
}
