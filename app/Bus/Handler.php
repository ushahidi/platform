<?php

namespace Ushahidi\App\Bus;

interface Handler
{
    public function __invoke(Action $action);
}
