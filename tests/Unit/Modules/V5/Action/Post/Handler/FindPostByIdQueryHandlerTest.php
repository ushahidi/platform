<?php

namespace Tests\Unit\Modules\V5\Action\Post\Handler;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Post\Handlers\FindPostByIdQueryHandler;
use Ushahidi\Modules\V5\Actions\Post\Queries\FindPostByIdQuery;
use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Modules\V5\Repository\Post\PostRepository;
use Ushahidi\Tests\TestCase;

class FindPostByIdQueryHandlerTest extends TestCase
{
    public function testSuccessfullyFindingPost(): void
    {
        // Given
        $postModel = factory(Post::class)->create();
        $postRepository = $this->createMock(PostRepository::class);
        $postRepository->method('fetchById')->willReturn($postModel);

        // When
        $handler = new FindPostByIdQueryHandler($postRepository);
        $post = $handler(FindPostByIdQuery::of(1));

        // Then
        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals($post->id, $postModel->id);
    }

    public function testThrowingOnProvidingIncorrectQuery(): void
    {
        // Should
        $this->expectException(\InvalidArgumentException::class);

        // Given
        $postRepository = $this->createMock(PostRepository::class);

        // When
        $handler = new FindPostByIdQueryHandler($postRepository);
        $handler($this->createMock(Query::class));
    }
}
