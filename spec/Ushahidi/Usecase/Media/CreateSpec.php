<?php

namespace spec\Ushahidi\Usecase\Media;

use Ushahidi\Tool\Validator;
use Ushahidi\Entity\Media;
use Ushahidi\Usecase\Media\CreateMediaRepository;
use Ushahidi\Usecase\Media\MediaData;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CreateSpec extends ObjectBehavior
{
	function let(CreateMediaRepository $repo, Validator $valid)
	{
		$this->beConstructedWith($repo, $valid);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\Media\Create');
	}

	function it_fails_with_invalid_input($req, $valid, MediaData $input)
	{
		$input->file = null;

		$valid->check($input)->willReturn(false);
		$valid->errors()->willReturn([]);
		$this->shouldThrow('Ushahidi\Exception\ValidatorException')->duringInteract($input);
	}

	function it_can_create_a_media_with_valid_input($valid, $repo, MediaData $input)
	{
		$file = [
			'name'     => 'test.png',
			'type'     => 'image/png',
			'size'     => 1024,
			'tmp_name' => 't.png',
			'error'    => UPLOAD_ERR_OK,
			];

		$input->file = $file;

		$valid->check($input)->willReturn(true);

		// auth not needed right now, leaving it behind for later consideration
		// $auth->isAllowed('media', 'create')->willReturn(true);

		$repo->createMedia($file, Argument::cetera())->shouldBeCalled();
		$repo->getCreatedMedia()->willReturn(new Media);

		$this->interact($input)->shouldReturnAnInstanceOf('Ushahidi\Entity\Media');
	}

	function it_can_create_a_media_with_valid_input_and_optional_data($valid, $repo, MediaData $input)
	{
		$file = [
			'name'     => 'test2.png',
			'type'     => 'image/png',
			'size'     => 1024,
			'tmp_name' => 't2.png',
			'error'    => UPLOAD_ERR_OK,
			];

		$input->file = $file;
		$input->caption = 'hello, world!';
		$input->user_id = 1;

		$valid->check($input)->willReturn(true);

		// auth not needed right now, leaving it behind for later consideration
		// $auth->isAllowed('media', 'create')->willReturn(true);

		$repo->createMedia($input->file, $input->caption, $input->user_id)->shouldBeCalled();
		$repo->getCreatedMedia()->willReturn(new Media);

		$this->interact($input)->shouldReturnAnInstanceOf('Ushahidi\Entity\Media');
	}
}

