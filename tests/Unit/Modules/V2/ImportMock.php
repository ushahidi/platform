<?php

namespace Ushahidi\Tests\Unit\Modules\V2;

use Ushahidi\Modules\V2\Import;
use Mockery as M;

class ImportMock
{

    public static function forId(int $importId)
    {
        $m = M::mock(Import::class);
        $m->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn($importId);
        return $m;
    }

    public static function mockImportTimezone(Import $m, string $tz)
    {
        $m->shouldReceive('getImportTimezone')
            ->andReturn($tz);
    }
}
