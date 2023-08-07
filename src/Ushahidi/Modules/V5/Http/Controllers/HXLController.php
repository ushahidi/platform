<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\HXL\Queries\FetchHXLQuery;
use Ushahidi\Modules\V5\Actions\HXL\Queries\FetchHXLLicensesQuery;
use Ushahidi\Modules\V5\Actions\HXL\Queries\FetchHXLOrganizationsQuery;
use Ushahidi\Modules\V5\Actions\HXL\Queries\FetchHXLTagsQuery;
use Ushahidi\Modules\V5\Actions\HXL\Queries\FetchHXLMetaDataQuery;
use Ushahidi\Modules\V5\Actions\HXL\Commands\CreateHXLMetaDataCommand;

use Ushahidi\Modules\V5\Http\Resources\HXL\HXLLicenseCollection;
use Ushahidi\Modules\V5\Http\Resources\HXL\HXLMetadataCollection;
use Ushahidi\Modules\V5\Http\Resources\HXL\HXLOrganizationCollection;
use Ushahidi\Modules\V5\Http\Resources\HXL\HXLTagCollection;

use Ushahidi\Modules\V5\Requests\HXLRequest;
use Ushahidi\Modules\V5\Models\HXL;
use Ushahidi\Core\Exception\NotFoundException;

class HXLController extends V5Controller
{




    /**
     * Display the specified resource.
     *
     * @return HXLCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $hxls = $this->queryBus->handle(FetchHXLQuery::FromRequest($request));
        return new HXLCollection($hxls);
    } //end index()

    public function indexLicenses(Request $request)
    {
        $hxls = $this->queryBus->handle(FetchHXLLicensesQuery::FromRequest($request));
        return new HXLLicenseCollection($hxls);
    } //end index()

    public function indexTags(Request $request)
    {
        $hxls = $this->queryBus->handle(FetchHXLTagsQuery::FromRequest($request));
        return new HXLTagCollection($hxls);
    } //end index()

    public function indexMetadata(Request $request)
    {
        $hxls = $this->queryBus->handle(FetchHXLMetaDataQuery::FromRequest($request));
        return new HXLMetadataCollection($hxls);
    } //end index()

    public function indexOrganizations(Request $request)
    {
        $hxls = $this->queryBus->handle(FetchHXLOrganizationsQuery::FromRequest($request));
        return new HXLOrganizationCollection($hxls);
    } //end index()



    /**
     * Create new HXL.
     *
     * @param HXLRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function storeMetadata(HXLRequest $request)
    {
        $command = CreateHXLMetaDataCommand::fromRequest($request);
        $new_hxl = new HXL($command->getHXLEntity()->asArray());
        $this->authorize('store', $new_hxl);
        return $this->show($this->commandBus->handle($command));
    } //end store()
} //end class
