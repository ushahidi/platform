<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\HXL\Queries\FetchHXLQuery;
use Ushahidi\Modules\V5\Actions\HXL\Queries\FetchHXLLicensesQuery;
use Ushahidi\Modules\V5\Actions\HXL\Queries\FetchHXLOrganizationsQuery;
use Ushahidi\Modules\V5\Actions\HXL\Queries\FetchHXLTagsQuery;
use Ushahidi\Modules\V5\Actions\HXL\Queries\FetchHXLMetaDataQuery;
use Ushahidi\Modules\V5\Actions\HXL\Queries\FetchHXLMetadataByIdQuery;

use Ushahidi\Modules\V5\Actions\HXL\Commands\CreateHXLMetaDataCommand;

use Ushahidi\Modules\V5\Http\Resources\HXL\HXLLicenseCollection;
use Ushahidi\Modules\V5\Http\Resources\HXL\HXLMetadataCollection;
use Ushahidi\Modules\V5\Http\Resources\HXL\HXLOrganizationCollection;
use Ushahidi\Modules\V5\Http\Resources\HXL\HXLTagCollection;

use Ushahidi\Modules\V5\Requests\HXLMetadataRequest;
use Ushahidi\Modules\V5\Models\HXL;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Http\Resources\HXL\HXLMetadataResource;

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
    public function storeMetadata(HXLMetadataRequest $request)
    {
        $command = CreateHXLMetaDataCommand::fromRequest($request);
        //$new_hxl_metadata = new HXL\HXLMetaData($command->getHXLMetadataEntity()->asArray());
        //$this->authorize('store', $new_hxl_metadata);
        $hxl_metadata_id = $this->commandBus->handle($command);
        $hxl_metadata = $this->queryBus->handle(new FetchHXLMetadataByIdQuery($hxl_metadata_id));
        return new HXLMetadataResource($hxl_metadata);
    } //end store()
} //end class
