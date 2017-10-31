<?php

namespace Tests;

abstract class TestCase extends \Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
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
        return $this->app['Illuminate\Contracts\Console\Kernel']->output();
    }
}
