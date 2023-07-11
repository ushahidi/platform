<?php
namespace Ushahidi\Modules\V5\Http\Resources\Config;

use Illuminate\Http\Resources\Json\JsonResource as Resource;
use Ushahidi\Modules\V5\Http\Resources\RequestCachedResource;
use Ushahidi\Modules\V5\Http\Resources\Permissions\PermissionsCollection;
use Illuminate\Support\Collection;
use Ushahidi\Core\Entity\Config as ConfigEntity;


use App\Bus\Query\QueryBus;

class ConfigKeyResource extends Resource
{

    // use RequestCachedResource;

    public static $wrap = 'result';
}
