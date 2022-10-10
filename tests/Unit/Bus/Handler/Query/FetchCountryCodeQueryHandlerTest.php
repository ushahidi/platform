<?php

declare(strict_types=1);

namespace Tests\Unit\Bus\Handler\Query;

use Ushahidi\Modules\V5\Actions\CountryCode\Query\FetchCountryCodeQuery;
use Ushahidi\Modules\V5\Actions\CountryCode\QueryHandler\FetchCountryCodeQueryHandler;
use Ushahidi\Modules\V5\Http\Resources\CountryCodeCollection;
use Ushahidi\Modules\V5\Models\CountryCode;
use Ushahidi\Modules\V5\Repository\CountryCode\CountryCodeRepository;
use Ushahidi\Tests\TestCase;

class FetchCountryCodeQueryHandlerTest extends TestCase
{
    public function testShouldReturnCountryCodeCollection(): void
    {
        // GIVEN
        $mockCountryCodeRepository = $this->createMock(CountryCodeRepository::class);
        $mockCountryCodeRepository
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                new CountryCode(),
                new CountryCode(),
            ]);
        $handler = new FetchCountryCodeQueryHandler($mockCountryCodeRepository);

        // WHEN
        $result = $handler(new FetchCountryCodeQuery(1, 2));

        // THEN
        $this->assertInstanceOf(CountryCodeCollection::class, $result);
        $this->assertCount(2, $result);
    }
}
