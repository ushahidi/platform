<?php

namespace spec\Ushahidi\Usecase\Media;

use Ushahidi\Tool\Authorizer;
use Ushahidi\Tool\Validator;
use Ushahidi\Entity\Media;
use Ushahidi\Usecase\Media\CreateMediaRepository;
use Ushahidi\Usecase\Media\MediaData;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CreateSpec extends ObjectBehavior
{
	function let(CreateMediaRepository $repo, Validator $valid, Authorizer $auth)
	{
		$this->beConstructedWith($repo, $valid, $auth);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\Media\Create');
	}

	function it_fails_with_invalid_input($valid, MediaData $input)
	{
		$input->file = null;

		$valid->check($input)->willReturn(false);
		$valid->errors()->willReturn([]);
		$this->shouldThrow('Ushahidi\Exception\ValidatorException')->duringInteract($input);
	}

	function it_fails_when_not_allowed_to_create($valid, $auth, MediaData $input)
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

		$auth->isAllowed(new Media, 'post')->willReturn(false);

		// Exception message will include the user id
		$auth->getUserId()->shouldBeCalled();
		$this->shouldThrow('Ushahidi\Exception\AuthorizerException')->duringInteract($input);
	}

	function it_fails_when_not_allowed_to_read($valid, $auth, $repo, MediaData $input)
	{
		$file = [
			'name'     => 'test.png',
			'type'     => 'image/png',
			'size'     => 1024,
			'tmp_name' => 't.png',
			'error'    => UPLOAD_ERR_OK,
		];

		$media  = new Media;
		$userid = null;

		$input->file = $file;

		$valid->check($input)->willReturn(true);

		$auth->isAllowed(new Media, 'post')->willReturn(true);
		$auth->getUserId()->willReturn($userid);

		$repo->createMedia($input->file, $input->caption, $userid)->shouldBeCalled();
		$repo->getCreatedMedia()->willReturn($media);

		$auth->isAllowed($media, 'get')->willReturn(false);

		// Exception message will include the user id
		$auth->getUserId()->shouldBeCalled();
		$this->shouldThrow('Ushahidi\Exception\AuthorizerException')->duringInteract($input);
	}

	function it_can_create_a_media_with_valid_input($valid, $auth, $repo, MediaData $input)
	{
		$file = [
			'name'     => 'test.png',
			'type'     => 'image/png',
			'size'     => 1024,
			'tmp_name' => 't.png',
			'error'    => UPLOAD_ERR_OK,
		];

		$media  = new Media;
		$userid = null;

		$input->file = $file;

		$valid->check($input)->willReturn(true);

		$auth->isAllowed(new Media, 'post')->willReturn(true);
		$auth->getUserId()->willReturn($userid);

		$repo->createMedia($input->file, $input->caption, $userid)->shouldBeCalled();
		$repo->getCreatedMedia()->willReturn($media);

		$auth->isAllowed($media, 'get')->willReturn(true);

		$this->interact($input)->shouldReturnAnInstanceOf('Ushahidi\Entity\Media');
	}

	function it_can_create_a_media_with_valid_input_and_optional_data($valid, $auth, $repo, MediaData $input)
	{
		$file = [
			'name'     => 'test2.png',
			'type'     => 'image/png',
			'size'     => 1024,
			'tmp_name' => 't2.png',
			'error'    => UPLOAD_ERR_OK,
			];

		$media  = new Media;
		$userid = 1;

		$input->file = $file;
		$input->caption = 'hello, world!';

		$valid->check($input)->willReturn(true);


		$auth->isAllowed(new Media, 'post')->willReturn(true);
		$auth->getUserId()->willReturn($userid);

		$repo->createMedia($input->file, $input->caption, $userid)->shouldBeCalled();
		$repo->getCreatedMedia()->willReturn($media);

		$auth->isAllowed($media, 'get')->willReturn(true);

		$this->interact($input)->shouldReturnAnInstanceOf('Ushahidi\Entity\Media');
	}
}
