<?php

namespace spec\Ushahidi\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MediaSpec extends ObjectBehavior
{
	function let()
	{
		$this->beConstructedWith(array(
			'id'       => 1,
			'user_id'  => 1,
			'caption'  => 'Hello, Test!',
			'created'  => strtotime('april 26, 2014'),
			'updated'  => strtotime('april 27, 2014'),
			'mime'     => 'image/png',
			'filename' => 'test.png',
			'width'    => 200,
			'height'   => 100,
			));
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Entity\Media');
	}

	function it_has_an_id()
	{
		$this->id->shouldBe(1);
	}

	function it_has_a_user_id()
	{
		$this->user_id->shouldBe(1);
	}

	function it_has_a_caption()
	{
		$this->caption->shouldBe('Hello, Test!');
	}

	function is_has_a_created_timestamp()
	{
		$this->created->shouldBe(strtotime('april 26, 2014'));
	}

	function is_has_a_updated_timestamp()
	{
		$this->updated->shouldBe(strtotime('april 27, 2014'));
	}

	function is_has_a_mime_type()
	{
		$this->mime->shouldBe('image/png');
	}

	function is_has_a_file_name()
	{
		$this->filename->shouldBe('test.png');
	}

	function is_has_a_width()
	{
		$this->width->shouldBe(200);
	}

	function is_has_a_height()
	{
		$this->height->shouldBe(200);
	}

	function it_can_set_data_from_an_array()
	{
		// ArrayExchange trait
		$this->setData(array('id' => 2))->shouldReturn($this);
		$this->id->shouldBe(2);
	}

	function it_can_be_converted_to_an_array()
	{
		// ArrayExchange trait
		$this->asArray()->shouldHaveKey('id');
	}


}
