<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use App\Bus\Query\QueryBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Ushahidi\Modules\V5\Actions\CountryCode\Queries\FetchCountryCodeByIdQuery;
use Ushahidi\Modules\V5\Actions\CountryCode\Queries\FetchCountryCodeQuery;
use Ushahidi\Modules\V5\Http\Resources\CountryCodeCollection;
use Ushahidi\Modules\V5\Http\Resources\CountryCodeResource;
use Ushahidi\Modules\V5\Models\CountryCode;

final class CountryCodeController extends V5Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->abortOnGate('view');

        /**
         * @var $result CountryCodeCollection
         */
        $result = $this->queryBus->handle(
            new FetchCountryCodeQuery(
                $request->query('limit'),
                $request->query('page'),
                $request->query('sortBy'),
                $request->query('dir')
            )
        );

        return $result->toResponse($request);
    }

    public function show(Request $request)
    {
        $this->abortOnGate('show');

        $countryCode = $this->queryBus->handle(
            new FetchCountryCodeByIdQuery($request->id)
        );

        return new CountryCodeResource($countryCode);
    }

    private function abortOnGate(string $action): void
    {
        if (!Gate::allows($action, CountryCode::class)) {
            abort(403, 'User is not allowed to view country codes');
        }
    }
}
