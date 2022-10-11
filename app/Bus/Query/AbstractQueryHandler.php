<?php

namespace App\Bus\Query;

use InvalidArgumentException;

abstract class AbstractQueryHandler implements QueryHandler
{
    /**
     * @param Query $query
     * @return mixed
     * @throws InvalidArgumentException
     */
    abstract protected function isSupported(Query $query);
}
