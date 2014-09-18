<?php

namespace spec\Ushahidi\Usecase\Post;

use Ushahidi\Tool\Validator;
use Ushahidi\Tool\Authorizer;
use Ushahidi\Entity\Post;
use Ushahidi\Exception\NotFoundException;
use Ushahidi\Usecase\Post\UpdatePostRepository;
use Ushahidi\Usecase\Post\ReadPostData;
use Ushahidi\Usecase\Post\UpdatePostData;

use PhpSpec\ObjectBehavior;

class UpdateSpec extends ObjectBehavior
{
	function let(UpdatePostRepository $repo, Validator $valid, Authorizer $auth)
	{
		$this->beConstructedWith($repo, $valid, $auth);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\Post\Update');
	}

	function it_can_update_a_post_with_valid_input($valid, $repo, $auth, Post $post, ReadPostData $read, UpdatePostData $input, UpdatePostData $update, Post $updated_post)
	{
		$raw_post   = ['title' => 'Before Update', 'content' => 'Some content'];
		$raw_input  = ['title' => 'After Update', 'content' => 'Some content'];
		$raw_update = ['title' => 'After Update'];

		$post->id = 1;

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

		// the repo will only receive changed values
		$repo->updatePost($post->id, $raw_update)->shouldBeCalled();
		$repo->updatePost($post->id, $raw_update)->willReturn($updated_post);

		// the persisted changes will be applied to the post
		// @todo use setData instead of returning a new object
		// then re-add this check
		// $post->setData($raw_update)->shouldBeCalled();

		// after being updated, the same post will be returned
		$this->interact($read, $input)->shouldReturn($updated_post);
	}

	function it_can_update_a_post_translation_with_valid_input($valid, $repo, $auth, Post $post, ReadPostData $read, UpdatePostData $input, UpdatePostData $update, Post $updated_post)
	{
		$raw_post   = ['title' => 'Before Update', 'content' => 'Some content'];
		$raw_input  = ['title' => 'After Update', 'content' => 'Some content'];
		$raw_update = ['title' => 'After Update'];

		$post->id = 2;
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
		$repo->updatePost($post->id, $raw_update)->shouldBeCalled();
		$repo->updatePost($post->id, $raw_update)->willReturn($updated_post);

		// the persisted changes will be applied to the post
		// @todo use setData instead of returning a new object
		// then re-add this check
		// $post->setData($raw_update)->shouldBeCalled();

		// after being updated, the same post will be returned
		$this->interact($read, $input)->shouldReturn($updated_post);
	}

	function it_fails_to_update_a_post_with_invalid_input($valid, $repo, $auth, Post $post, ReadPostData $read, UpdatePostData $input)
	{
		$post->id = 0;

		$read->id = 1;
		$read->parent_id = 0;

		$repo->getByIdAndParent($read->id, $read->parent_id)->willReturn($post);

		$this->shouldThrow('Ushahidi\Exception\NotFoundException')->duringInteract($read, $input);
	}
}
