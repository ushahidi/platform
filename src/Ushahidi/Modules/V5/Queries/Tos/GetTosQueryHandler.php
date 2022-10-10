<?php

namespace Ushahidi\Modules\V5\Queries\Tos;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Common\Errors;
use Ushahidi\Modules\V5\Common\Authorize;
use Ushahidi\Modules\V5\Queries\Tos\GetTosQuery;
use Ushahidi\Modules\V5\Models\Tos;

class GetTosQueryHandler extends AbstractQueryHandler
{
    use Authorize;
    use Errors;


    /**
     * @param Action|GetTosQuery $action
     * @return array
     */
    public function __invoke(Action $action) //: array
    {
        $this->isSupported($action);
        return $this->run($action);
    }

    public function run(GetTosQuery $query) //: array
    {
        if ($query->isList()) {
            $this->authorizeForUser('index', Tos::class);
            return Tos::orderBy($query->orderBy(), $query->order())->paginate($query->perPage());
        } else {
            $tos = Tos::find($query->getId());
            $this->authorizeForUser('show', $tos);

            if (!$tos) {
                $this->errorNotFound("Tos", $query->getId());
            }
            return $tos;
        }
    }
    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === GetTosQuery::class,
            'Provided query is not supported'
        );
    }
}
