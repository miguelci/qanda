<?php
declare(strict_types=1);

namespace Tests\Integration;

use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseDependentTestCase extends TestCase
{
    use RefreshDatabase;
    use InteractsWithDatabase;
}
