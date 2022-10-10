<?php

namespace Tests\Unit\Bus\Handler\Query;

use Ushahidi\Modules\V5\Actions\CountryCode\Query\FetchCountryCodeByIdQuery;
use Ushahidi\Modules\V5\Actions\CountryCode\Query\FetchCountryCodeQuery;
use Ushahidi\Modules\V5\Actions\CountryCode\QueryHandler\FetchCountryCodeByIdQueryHandler;
use PHPUnit\Framework\TestCase;
use Ushahidi\Modules\V5\Models\CountryCode;
use Ushahidi\Modules\V5\Repository\CountryCode\CountryCodeRepository;

class FetchCountryCodeByIdQueryHandlerTest extends TestCase
{
    public function testShouldReturnCountryCode(): void
    {
        // GIVEN
        $mockCountryCodeRepository = $this->createMock(CountryCodeRepository::class);
        $mockCountryCodeRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn(new CountryCode());
        $handler = new FetchCountryCodeByIdQueryHandler($mockCountryCodeRepository);

        // WHEN
        $result = $handler(new FetchCountryCodeByIdQuery(1));

        // THEN
        $this->assertInstanceOf(CountryCode::class, $result);
    }

    public function testShouldThrowExceptionWhenProvidedActionIsNotSupported(): void
    {
        // GIVEN
        $mockCountryCodeRepository = $this->createMock(CountryCodeRepository::class);
        $handler = new FetchCountryCodeByIdQueryHandler($mockCountryCodeRepository);

        // WHEN
        $this->expectException(\Exception::class);
        $handler(new FetchCountryCodeQuery(999, 1, null, null));
    }

    public function testShouldThrowExceptionWhenCountryCodeIsNotFound(): void
    {
        // GIVEN
        $mockCountryCodeRepository = $this->createMock(CountryCodeRepository::class);
        $mockCountryCodeRepository
            ->expects($this->once())
            ->method('findById')
            ->willThrowException(new \Exception('Country code not found'));
        $handler = new FetchCountryCodeByIdQueryHandler($mockCountryCodeRepository);

        // WHEN
        $this->expectException(\Exception::class);
        $handler(new FetchCountryCodeByIdQuery(1));
    }
}
