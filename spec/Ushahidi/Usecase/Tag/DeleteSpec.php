<?php

namespace spec\Ushahidi\Usecase\Tag;

use Ushahidi\Tool\Validator;
use Ushahidi\Entity\Tag;
use Ushahidi\Usecase\Tag\DeleteTagRepository;
use Ushahidi\Usecase\Tag\DeleteTagData;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DeleteSpec extends ObjectBehavior
{
	function let(DeleteTagRepository $repo, Validator $valid)
	{
		$this->beConstructedWith($repo, $valid);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\Tag\Delete');
	}

	function it_fails_with_invalid_input($req, $valid, DeleteTagData $input)
	{
		$input->id = null;

		$valid->check($input)->willReturn(false);
		$valid->errors()->willReturn([]);
		$this->shouldThrow('Ushahidi\Exception\ValidatorException')->duringInteract($input);
	}

	function it_can_delete_tag_with_valid_input($valid, $repo, DeleteTagData $input)
	{
		$input->id = 1;

		$valid->check($input)->willReturn(true);

		$repo->deleteTag($input->id)->willReturn(1);
		$repo->getDeletedTag()->willReturn(new Tag);

		$this->interact($input)->shouldReturnAnInstanceOf('Ushahidi\Entity\Tag');
	}
}

