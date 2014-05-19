<?php

namespace spec\Ushahidi\Usecase\Tag;

use Ushahidi\Tool\Validator;
use Ushahidi\Usecase\Tag\CreateTagRepository;

use PhpSpec\ObjectBehavior;

class CreateSpec extends ObjectBehavior
{
	function let(CreateTagRepository $repo, Validator $valid)
	{
		$this->beConstructedWith($repo, $valid, $auth);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\Tag\Create');
	}

	function it_fails_with_invalid_input($req, $valid)
	{
		$input = [
			'tag'  => '',
			'slug' => '',
			'type' => '',
			];

		$valid->check($input)->willReturn(false);
		$valid->errors()->willReturn([]);
		$this->shouldThrow('Ushahidi\Exception\ValidatorException')->duringInteract($input);
	}

	function it_can_create_a_tag_with_valid_input($valid, $repo)
	{
		$input = [
			'tag'  => 'Tests',
			'slug' => 'tests',
			'type' => 'category',
			];

		$valid->check($input)->willReturn(true);

		// auth not needed right now, leaving it behind for later consideration
		// $auth->isAllowed('tags', 'create')->willReturn(true);

		$repo->createTag($input)->shouldBeCalled();
		$repo->getCreatedTagId()->shouldBeCalled();
		$repo->getCreatedTagTimestamp()->shouldBeCalled();

		$this->interact($input)->shouldReturnAnInstanceOf('Ushahidi\Entity\Tag');
	}
}
