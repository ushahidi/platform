<?php

namespace spec\Ushahidi\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ContactSpec extends ObjectBehavior
{
	function let()
	{
		$this->beConstructedWith(array(
			'id'            => 1,
			'user_id'       => 1,
			'data_provider' => 'email',
			'type'          => 'email',
			'contact'       => 'test@ushahidi.com',
			'created'       => strtotime('april 27, 2014'),
			));
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Entity\Contact');
	}

	function it_has_an_id()
	{
		$this->id->shouldBe(1);
	}

	function it_has_a_user_id()
	{
		$this->user_id->shouldBe(1);
	}

	function is_has_a_data_provider()
	{
		$this->data_provider->shouldBe('email');
	}

	function is_has_a_type()
	{
		$this->type->shouldBe('email');
	}

	function it_has_a_contact()
	{
		$this->contact->shouldBe('test@ushahidi.com');
	}

	function is_has_a_created_timestamp()
	{
		$this->created->shouldBe(strtotime('april 26, 2014'));
	}
}
