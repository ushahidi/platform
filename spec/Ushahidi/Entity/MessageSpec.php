<?php

namespace spec\Ushahidi\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MessageSpec extends ObjectBehavior
{
	function let()
	{
		$this->beConstructedWith(array(
			'id'         => 1,
			'parent_id'  => 0,
			'contact_id' => 2,
			'post_id'    => 3,
			'title'      => 'Test Message',
			'message'    => 'Hello, this is a test',
			'created'    => strtotime('May 9, 2014'),
			'box'        => 'inbox',
			'direction'  => 'incoming',
			'status'     => 'received',
			));
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Entity\Message');
	}

	function it_has_an_id()
	{
		$this->id->shouldBe(1);
	}

	function it_has_a_parent_id()
	{
		$this->parent_id->shouldBe(0);
	}

	function it_has_a_post_id()
	{
		$this->post_id->shouldBe(3);
	}

	function it_has_a_title()
	{
		$this->title->shouldBe('Test Message');
	}

	function it_has_a_message()
	{
		$this->message->shouldBe('Hello, this is a test');
	}

	function it_has_a_created_timestamp()
	{
		$this->created->shouldBe(strtotime('May 9, 2014'));
	}

	function it_has_a_box()
	{
		$this->box->shouldBe('inbox');
	}

	function it_has_a_direction()
	{
		$this->direction->shouldBe('incoming');
	}

	function it_has_a_status()
	{
		$this->status->shouldBe('received');
	}
}
