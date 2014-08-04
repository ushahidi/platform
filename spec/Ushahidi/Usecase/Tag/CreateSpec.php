<?php

namespace spec\Ushahidi\Usecase\Tag;

use Ushahidi\Tool\Validator;
use Ushahidi\Entity\Tag;
use Ushahidi\Usecase\Tag\CreateTagRepository;
use Ushahidi\Usecase\Tag\TagData;

use PhpSpec\ObjectBehavior;

class CreateSpec extends ObjectBehavior
{
	function let(CreateTagRepository $repo, Validator $valid)
	{
		$this->beConstructedWith($repo, $valid);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\Tag\Create');
	}

	function it_fails_with_invalid_input($req, $valid, TagData $input)
	{
		$input->tag  = '';
		$input->slug = '';
		$input->type = '';

		$valid->check($input)->willReturn(false);
		$valid->errors()->willReturn([]);
		$this->shouldThrow('Ushahidi\Exception\ValidatorException')->duringInteract($input);
	}

	function it_can_create_a_tag_with_valid_input($valid, $repo, TagData $input)
	{
		$input->tag         = 'Tests';
		$input->slug        = 'tests';
		$input->description = 'Testing for tags';
		$input->type        = 'category';
		$input->color       = 'fff';
		$input->icon        = 'bell';
		$input->priority    = 99;
		$input->role        = ['user', 'admin'];

		$valid->check($input)->willReturn(true);

		// auth not needed right now, leaving it behind for later consideration
		// $auth->isAllowed('tags', 'create')->willReturn(true);

		$repo->createTag(
			$input->tag,
			$input->slug,
			$input->description,
			$input->type,
			$input->color,
			$input->icon,
			$input->priority,
			json_encode($input->role)
			)->shouldBeCalled();

		$repo->getCreatedTag()->willReturn(new Tag);

		$this->interact($input)->shouldReturnAnInstanceOf('Ushahidi\Entity\Tag');
	}
}
