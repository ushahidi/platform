<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use App\Bus\Command\CommandBus;
use App\Bus\Query\QueryBus;
use Ushahidi\Modules\V5\Http\Resources\TosCollection;
use Ushahidi\Modules\V5\Http\Resources\TosResource;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Models\Tos;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Commands\Tos\CreateTosCommand;
use Ushahidi\Modules\V5\Queries\Tos\GetTosQuery;

class TosController extends V5Controller
{

    /**
     * Not all fields are things we want to allow on the body of requests
     * an author won't change after the fact so we limit that change
     * to avoid issues from the frontend.
     * @return string[]
     */
    protected function ignoreInput()
    {
        return ['user_id', 'agreement_date'];
    }
    
    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Request $request, int $id, QueryBus $queryBus)
    {
        $query = new GetTosQuery($request, $id);
        return new TosResource($queryBus->handle($query));
    }//end show()

   
    /**
     * Display the specified resource.
     *
     * @return TosCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, QueryBus $queryBus)
    {
        $query = new GetTosQuery($request);
        return new TosCollection($queryBus->handle($query));
    }//end index()

    
    /**
     * Create new Tos.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|CategoryResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request, CommandBus $commandBus)
    {
         $command = new CreateTosCommand($this->getFields($request->input()));
         $commandBus->handle($command);
         return new TosResource($command->getModel());
    }//end store()
}//end class
