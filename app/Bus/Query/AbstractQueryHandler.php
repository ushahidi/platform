<?php

namespace Ushahidi\App\Bus\Query;

use InvalidArgumentException;

abstract class AbstractQueryHandler implements QueryHandler
{
    /**
     * @param Query $query
     * @return mixed
     * @throws InvalidArgumentException
     */
    protected abstract function isSupported(Query $query);
}
