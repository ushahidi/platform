<?php

namespace Tests\Unit\Modules\V5\Repository;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Ushahidi\Modules\V5\Entity\PostEntity;
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

        $db = $this->createMock(Connection::class);
        $db->method('table')->with('posts')->willReturn($builder);

        $repository = new EloquentPostRepository($db);
        $foundPost = $repository->fetchById($post->id);
        $this->assertInstanceOf(PostEntity::class, $foundPost);
        $this->assertEquals($post->id, $foundPost->getId());
    }

    public function testFindingAPostByIdThatDoesNotExist()
    {
        $builder = $this->createMock(Builder::class);
        $builder->method('find')->willReturn(null);

        $db = $this->createMock(Connection::class);
        $db->method('table')->with('posts')->willReturn($builder);

        $repository = new EloquentPostRepository($db);
        $foundPost = $repository->fetchById(1);
        $this->assertNull($foundPost);
    }
}
