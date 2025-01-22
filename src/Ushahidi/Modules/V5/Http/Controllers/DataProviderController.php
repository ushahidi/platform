<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\DataProvider\Queries\FetchDataProviderByIdQuery;
use Ushahidi\Modules\V5\Actions\DataProvider\Queries\FetchDataProviderQuery;
use Ushahidi\Modules\V5\Http\Resources\DataProvider\DataProviderResource;
use Ushahidi\Modules\V5\Http\Resources\DataProvider\DataProviderCollection;

class DataProviderController extends V5Controller
{


    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(string $id)
    {
        $provider = $this->queryBus->handle(new FetchDataProviderByIdQuery($id));
       // $this->authorize('show', $contact);
        return new DataProviderResource($provider);
    } //end show()



    /**
     * Display the specified resource.
     *
     * @return DataProviderCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
       // $this->authorize('index', DataProvider::class);
        $providers = $this->queryBus->handle(FetchDataProviderQuery::FromRequest($request));
        return new DataProviderCollection($providers);
    } //end index()
} //end class
