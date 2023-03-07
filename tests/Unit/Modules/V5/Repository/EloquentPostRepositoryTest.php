<?php

namespace Tests\Unit\Modules\V5\Repository;

use Illuminate\Database\Eloquent\Builder;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Modules\V5\Repository\Post\EloquentPostRepository;
use Ushahidi\Tests\TestCase;
class EloquentPostRepositoryTest extends TestCase
{
    public function testFindingAPostById()
    {
        $post = factory(Post::class)->create();
        $builder = $this->createMock(Builder::class);
        $builder->method('find')->willReturn($post);

        $repository = new EloquentPostRepository($builder);
        $foundPost = $repository->fetchById($post->id);
        $this->assertInstanceOf(Post::class, $foundPost);
        $this->assertEquals($post->id, $foundPost->id);
    }

    public function testFindingAPostByIdThatDoesNotExist()
    {
        $builder = $this->createMock(Builder::class);
        $builder->method('find')->willReturn(null);

        $repository = new EloquentPostRepository($builder);
        $this->expectException(NotFoundException::class);
        $repository->fetchById(1);
    }
}
