<?php

namespace spec\Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Usecase\Post\UpdatePostRepository;
use Ushahidi\Core\Usecase\Post\ReadPostData;
use Ushahidi\Core\Usecase\Post\PostData;

use PhpSpec\ObjectBehavior;

class UpdatePostSpec extends ObjectBehavior
{
	function let(UpdatePostRepository $repo, Validator $valid, Authorizer $auth)
	{
		$repo->beADoubleOf('Ushahidi\Core\Entity\PostRepository');
		$this->beConstructedWith(compact('repo', 'valid', 'auth'));
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Core\Usecase\Post\UpdatePost');
	}

	function it_can_update_a_post_with_valid_input($valid, $repo, $auth, Post $post, ReadPostData $read, PostData $input, PostData $update, Post $updated_post)
	{
		$raw_post   = ['title' => 'Before Update', 'content' => 'Some content'];
		$raw_input  = ['title' => 'After Update', 'content' => 'Some content'];
		$raw_update = ['title' => 'After Update'];

		$post->getId()->willReturn(1);

		$repo->getByIdAndParent($read->id, $read->parent_id)->willReturn($post);

		$post->asArray()->willReturn($raw_post);
		$input->asArray()->willReturn($raw_input);
		$update->asArray()->willReturn($raw_update);

		// the update will be what is different in the input, as compared to the post
		$input->getDifferent($raw_post)->willReturn($update);

		// only the changed values will be validated
		$valid->check($update)->willReturn(true);

		// auth check
		$auth->isAllowed($post, 'update')->willReturn(true);
		$auth->isAllowed($post, 'change_user')->willReturn(true);
		$auth->isAllowed($post, 'read')->willReturn(true);

		// the repo will only receive changed values
		$repo->update($post->id, $update)->shouldBeCalled();
		$repo->update($post->id, $update)->willReturn($updated_post);

		// Loads updated posts and checks auth
		$repo->get($post->id)->willReturn($updated_post);
		$auth->isAllowed($updated_post, 'read')->willReturn(true);

		// after being updated, the same post will be returned
		$this->interact($read, $input)->shouldReturn($updated_post);
	}

	function it_can_update_a_post_translation_with_valid_input($valid, $repo, $auth, Post $post, ReadPostData $read, PostData $input, PostData $update, Post $updated_post)
	{
		$raw_post   = ['title' => 'Before Update', 'content' => 'Some content'];
		$raw_input  = ['title' => 'After Update', 'content' => 'Some content'];
		$raw_update = ['title' => 'After Update'];

		$post->getId()->willReturn(2);
		$read->locale = "fr_FR";
		$read->parent_id = 1;

		$repo->getByLocale($read->locale, $read->parent_id)->willReturn($post);

		$post->asArray()->willReturn($raw_post);
		$input->asArray()->willReturn($raw_input);
		$update->asArray()->willReturn($raw_update);

		// the update will be what is different in the input, as compared to the post
		$input->getDifferent($raw_post)->willReturn($update);

		// only the changed values will be validated
		$valid->check($update)->willReturn(true);

		// auth check
		$auth->isAllowed($post, 'update')->willReturn(true);
		$auth->isAllowed($post, 'change_user')->willReturn(true);

		// the repo will only receive changed values
		$repo->update($post->id, $update)->shouldBeCalled();

		// Loads updated posts and checks auth
		$repo->get($post->id)->willReturn($updated_post);
		$auth->isAllowed($updated_post, 'read')->willReturn(true);

		// after being updated, the same post will be returned
		$this->interact($read, $input)->shouldReturn($updated_post);
	}

	function it_fails_to_update_a_post_with_invalid_input($valid, $repo, $auth, Post $post, ReadPostData $read, PostData $input)
	{
		$post->getId()->willReturn(0);
		$post->getResource()->willReturn('posts');

		$read->id = 1;
		$read->parent_id = 0;

		$repo->getByIdAndParent($read->id, $read->parent_id)->willReturn($post);

		$this->shouldThrow('Ushahidi\Core\Exception\NotFoundException')->duringInteract($read, $input);
	}
}
