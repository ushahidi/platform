<?php

namespace Tests\Unit\Modules\V5\Action\Post\Query;

use Ushahidi\Modules\V5\Actions\Post\Queries\FindPostByIdQuery;
use Ushahidi\Tests\TestCase;

class FindPostByIdQueryTest extends TestCase
{
    public function testCanCreateWithAPositiveNumber()
    {
        $query = FindPostByIdQuery::of(1);

        $this->assertInstanceOf(FindPostByIdQuery::class, $query);
        $this->assertEquals($query->getId(), 1);
    }

    public function testCannotCreateWithANegativeNumber()
    {
        $this->expectException(\InvalidArgumentException::class);
        new FindPostByIdQuery(-1);
    }

    public function testCannotCreateWithZero()
    {
        $this->expectException(\InvalidArgumentException::class);
        new FindPostByIdQuery(0);
    }
}
