<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use App\Bus\Command\CommandBus;
use App\Bus\Query\QueryBus;
use Ushahidi\Modules\V5\Http\Resources\TosCollection;
use Ushahidi\Modules\V5\Http\Resources\TosResource;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Models\Tos;
use Ushahidi\Modules\V5\Actions\Tos\Commands\CreateTosCommand;
use Ushahidi\Modules\V5\Requests\StoreTosRequest;
use Ushahidi\Modules\V5\Actions\Tos\Queries\FetchTosByIdQuery;
use Ushahidi\Modules\V5\Actions\Tos\Queries\FetchTosQuery;

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
    public function show(int $id, QueryBus $queryBus)
    {
        $tos = $queryBus->handle(new FetchTosByIdQuery($id));
        $this->authorizeForCurrentUser('show', $tos);
        return new TosResource($tos);
    }//end show()

   
    /**
     * Display the specified resource.
     *
     * @return TosCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, QueryBus $queryBus)
    {
        $this->authorizeForCurrentUser('index', Tos::class);
        $resourceCollection = new TosCollection(
            $queryBus->handle(
                new FetchTosQuery(
                    $request->query('limit', config('paging.default_limit')),
                    $request->query('page', 1),
                    $request->query('sortBy', config('paging.default_sort_by')),
                    $request->query('order', config('paging.default_order'))
                )
            )
        );
         return $resourceCollection;
    }//end index()

    
    /**
     * Create new Tos.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|CategoryResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreTosRequest $request, CommandBus $commandBus, QueryBus $queryBus)
    {
        $this->authorizeForCurrentUser('store', Tos::class);
        $command = new CreateTosCommand(
            $this->setInputDefaults(
                $this->getFields($request->input()),
                $this->getGenericUser()
            )
        );
         $commandBus->handle($command);
         return new TosResource(
             $queryBus->handle(new FetchTosByIdQuery($command->getId()))
         );
    }//end store()

    private function setInputDefaults($input, $user)
    {
        $input['user_id'] = $user->id;
        // Save the agreement date to the current time
        $input['agreement_date'] =  time();
        return $input;
    }
}//end class
