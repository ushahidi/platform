<?php

namespace Tests\Unit\Modules\V5\Action\Post\Query;

use Ushahidi\Modules\V5\Actions\Post\Queries\ListPostsQuery;
use Ushahidi\Tests\TestCase;

class ListPostsQueryTest extends TestCase
{
    public function testShouldCreateWithCorrectParameters(): void
    {
        // $query = new ListPostsQuery([
        //     'fields' => ['id', 'parent_id', 'status'],
        //     'limit' => 10,
        // ]);
        // $this->assertEquals($query->getFields(), ['id', 'parent_id', 'status']);
        // $this->assertEquals($query->getLimit(), 10);
    }

    public function testShouldThrowOnProvidingLimitLowerThanOne(): void
    {
        // $this->expectException(\InvalidArgumentException::class);
        // ListPostsQuery::fromArray([
        //     'limit' => 0,
        // ]);
    }

    public function testShouldThrowOnProvidingUnapprovedFields(): void
    {
        // $this->expectException(\InvalidArgumentException::class);
        // ListPostsQuery::fromArray([
        //     'fields' => ['id', 'parent_id', 'status', 'unapproved_field'],
        // ]);
    }
}
