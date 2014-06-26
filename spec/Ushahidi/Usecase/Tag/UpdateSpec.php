<?php

namespace spec\Ushahidi\Usecase\Tag;

use Ushahidi\Tool\Validator;
use Ushahidi\Entity\Tag;
use Ushahidi\Usecase\Tag\UpdateTagRepository;
use Ushahidi\Usecase\Tag\TagData;

use PhpSpec\ObjectBehavior;

class UpdateSpec extends ObjectBehavior
{
	function let(UpdateTagRepository $repo, Validator $valid)
	{
		$this->beConstructedWith($repo, $valid);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\Tag\Update');
	}

	function it_can_update_a_tag_with_valid_input($valid, $repo, Tag $tag, TagData $input, TagData $update)
	{
		$raw_tag    = ['tag' => 'Before Update', 'icon' => 'bell'];
		$raw_input  = ['tag' => 'After Update', 'icon' => 'bell'];
		$raw_update = ['tag' => 'After Update'];

		$tag->asArray()->willReturn($raw_tag);
		$input->asArray()->willReturn($raw_input);
		$update->asArray()->willReturn($raw_update);

		// the update will be what is different in the input, as compared to the tag
		$input->getDifferent($raw_tag)->willReturn($update);

		// only the changed values will be validated
		$valid->check($update)->willReturn(true);

		// the repo will only receive changed values
		$repo->updateTag($tag->id, $raw_update)->shouldBeCalled();

		// the persisted changes will be applied to the tag
		$tag->setData($raw_update)->shouldBeCalled();

		// after being updated, the same tag will be returned
		$this->interact($tag, $input)->shouldReturn($tag);
	}
}

