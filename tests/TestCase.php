<?php

namespace Tests;

abstract class TestCase extends \Laravel\Lumen\Testing\TestCase
{
    // Validates mocks
    // May not be needed because \Laravel\Lumen\Testing\TestCase does some of this anyway
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }
}
