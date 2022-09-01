<?php

namespace Ushahidi\App\Bus\Query;

abstract class AbstractQueryHandler implements QueryHandler
{
    /**
     * @param Query $query
     * @throws \InvalidArgumentException
     * @return mixed
     */
    protected abstract function isSupported(Query $query);
}
