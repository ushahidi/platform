<?php

namespace Tests\Unit\Modules\V5\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Modules\V5\Repository\Post\EloquentPostRepository;
use Ushahidi\Tests\TestCase;

class EloquentPostRepositoryTest extends TestCase
{
    public function testFindingAPostById()
    {
        $post = Post::factory()->create();
        $builder = $this->createMock(Builder::class);
        $builder->method('find')->willReturn($post);

        $repository = new EloquentPostRepository($builder);
        $foundPost = $repository->findById($post->id);
        $this->assertInstanceOf(Post::class, $foundPost);
        $this->assertEquals($post->id, $foundPost->id);
    }

    public function testFindingAPostByIdThatDoesNotExist()
    {
        $builder = $this->createMock(Builder::class);
        $builder->method('find')->willReturn(null);

        $repository = new EloquentPostRepository($builder);
        $this->expectException(NotFoundException::class);
        $repository->findById(1);
    }

    public function testPaginationWithNoResults()
    {
        // $builder = $this->createMock(Builder::class);
        // $builder->method('paginate')->willReturn(new LengthAwarePaginator([], 0, 10));

        // $repository = new EloquentPostRepository($builder);
        // $posts = $repository->paginate(10, ['id']);
        // $this->assertInstanceOf(LengthAwarePaginator::class, $posts);
        // $this->assertEquals($posts->currentPage(), 1);
        // $this->assertEquals($posts->count(), 0);
        // $this->assertEquals($posts->perPage(), 10);
    }

    public function testPaginationWithResults()
    {
        // $post = Post::factory()->create();
        // $builder = $this->createMock(Builder::class);
        // $builder->method('paginate')->willReturn(new LengthAwarePaginator([$post], 1, 10));

        // $repository = new EloquentPostRepository($builder);
        // $posts = $repository->paginate(10, ['id']);
        // $this->assertInstanceOf(LengthAwarePaginator::class, $posts);
        // $this->assertEquals($posts->currentPage(), 1);
        // $this->assertEquals($posts->count(), 1);
        // $this->assertEquals($posts->perPage(), 10);
    }
}
