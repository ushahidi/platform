<?php

namespace Ushahidi\Tests;

use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        Hash::setRounds(4);

        return $app;
    }

    /**
     * Call artisan command and return code.
     *
     * @param string  $command
     * @param array   $parameters
     * @return int
     */
    public function artisanOutput()
    {
        return $this->app[Kernel::class]->output();
    }

        /**
     * Assert that a given where condition exists in the database.
     *
     * @param  string  $table
     * @param  array  $data
     * @param  string|null $onConnection
     * @return $this
     */
    protected function seeInDatabase($table, array $data, $onConnection = null)
    {
        $count = $this->app->make('db')->connection($onConnection)->table($table)->where($data)->count();

        $this->assertGreaterThan(0, $count, sprintf(
            'Unable to find row in database table [%s] that matched attributes [%s].',
            $table,
            json_encode($data)
        ));

        return $this;
    }
}
