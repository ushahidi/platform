<?php

namespace Ushahidi\Modules\V5\Actions\CountryCode\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\CountryCode\Queries\FetchCountryCodeByIdQuery;
use Ushahidi\Modules\V5\Models\CountryCode;
use Ushahidi\Modules\V5\Repository\CountryCode\CountryCodeRepository;

class FetchCountryCodeByIdQueryHandler extends AbstractQueryHandler
{
    private $countryCodeRepository;

    public function __construct(CountryCodeRepository $countryCodeRepository)
    {
        $this->countryCodeRepository = $countryCodeRepository;
    }

    protected function isSupported(Query $query)
    {
        if (!$query instanceof FetchCountryCodeByIdQuery) {
            throw new \Exception('Provided action is not supported');
        }
    }

    /**
     * @param FetchCountryCodeByIdQuery|Action $action
     * @throws \Exception
     */
    public function __invoke(Action $action): CountryCode
    {
        $this->isSupported($action);

        return $this->countryCodeRepository->findById(
            $action->getId()
        );
    }
}
