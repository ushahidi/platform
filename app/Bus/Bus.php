<?php

declare(strict_types=1);


namespace App\Bus;

interface Bus
{
    public function register(string $action, string $handler): void;

    public function handle(Action $action);
}
