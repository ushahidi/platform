<?php

namespace Ushahidi\Modules\V5\Actions\CountryCode\QueryHandler;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\CountryCode\Query\FetchCountryCodeQuery;
use Ushahidi\Modules\V5\Http\Resources\CountryCodeCollection;
use Ushahidi\Modules\V5\Repository\CountryCode\CountryCodeRepository;

class FetchCountryCodeQueryHandler extends AbstractQueryHandler
{
    private $countryCodeRepository;

    public function __construct(CountryCodeRepository $countryCodeRepository)
    {
        $this->countryCodeRepository = $countryCodeRepository;
    }

    /**
     * This check is not needed because the QueryBus will check if the action is a Query
     * But for typing’s sake we can add it here
     */
    protected function isSupported(Query $query)
    {
        if (!$query instanceof FetchCountryCodeQuery) {
            throw new \Exception('Provided action is not supported');
        }
    }

    /**
     * @param Action|FetchCountryCodeQuery $action
     * @throws \Exception
     */
    public function __invoke(Action $action): CountryCodeCollection
    {
        $skip = $action->getLimit() * ($action->getPage() - 1);

        return new CountryCodeCollection(
            collect(
                $this->countryCodeRepository->fetch(
                    $action->getLimit(),
                    $skip,
                    $action->getSortBy(),
                    $action->getDirection()
                )
            ),
            $this->countryCodeRepository->getCount()
        );
    }
}
