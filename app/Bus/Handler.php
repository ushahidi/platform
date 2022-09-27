<?php

namespace App\Bus;

interface Handler
{
    public function __invoke(Action $action);
}
