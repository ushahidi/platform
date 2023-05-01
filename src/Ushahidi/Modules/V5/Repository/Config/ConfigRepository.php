<?php

namespace Ushahidi\Modules\V5\Repository\Config;

use Ushahidi\Modules\V5\Models\Config;
use Illuminate\Pagination\LengthAwarePaginator;

interface ConfigRepository
{
    /**
     * This method will fetch a all configs of group
     * @param string $group_name
     * @return Config
     */
    public function findByGroupName(string $group_name);
}
