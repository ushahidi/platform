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

    private function runAuthorizer($ability, $object)
    {
        $authorizer = service('authorizer.tos');
        // if there's no user the guards will kick them off already, but if there
        // is one we need to check the authorizer to ensure we don't let
        // users without admin perms create forms etc
        // this is an unfortunate problem with using an old version of lumen
        // that doesn't let me do guest user checks without adding more risk.
        $user = $authorizer->getUser();
        if ($user) {
            $this->authorize($ability, $object);
        }
        return $user;
    }

    private function setInputDefaults($input, $action, $user)
    {
        if ($action === 'store') {
            // Save the agreement date to the current time and the user ID
            $input['user_id'] = $user->id;
            $input['agreement_date'] =  time();
        }
        return $input;
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

    public function store1(Request $request, CommandBus $commandBus)
    {
         $input = $this->getFields($request->input());
        
        if (empty($input)) {
            return self::make500('POST body cannot be empty');
        }
         $user = $this->runAuthorizer('store', Tos::class);

         $input = $this->setInputDefaults($input, 'store', $user);

         $tos = new Tos();
        if (!$tos->validate($input)) {
            return self::make422($tos->errors);
        }
         DB::beginTransaction();
        try {
            $tos = tos::create($input);
            DB::commit();
            return new TosResource($tos);
        } catch (\Exception $e) {
            DB::rollback();
            return self::make500($e->getMessage());
        }
    }//end store()
}//end class
