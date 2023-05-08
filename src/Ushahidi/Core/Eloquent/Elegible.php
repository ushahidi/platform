<?php

namespace Ushahidi\Core\Eloquent;

interface Elegible
{
    /**
     * @param mixed ...$criteria
     *
     * @return $this
     */
    public function withCriteria(...$criteria): self;
}
