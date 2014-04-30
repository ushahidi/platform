<?php

namespace spec\Ushahidi\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UserSpec extends ObjectBehavior
{
	function let()
	{
		$this->beConstructedWith(array(
			'id'       => 1,
			'email'    => 'test@ushahidi.com',
			'realname' => 'Test User',
			'username' => 'demo',
			'password' => sha1('secret'),
			'created'  => strtotime('april 27, 2014'),
			'updated'  => strtotime('april 27, 2014 3:00pm'),
			));
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Entity\User');
	}

	function it_has_an_id()
	{
		$this->id->shouldBe(1);
	}

	function it_has_an_email()
	{
		$this->email->shouldBe('test@ushahidi.com');
	}

	function it_has_a_realname()
	{
		$this->realname->shouldBe('Test User');
	}

	function it_has_a_username()
	{
		$this->username->shouldBe('demo');
	}

	function it_has_a_password()
	{
		// Real password hashing is more than sha1. :)
		$this->password->shouldBe(sha1('secret'));
	}

	function is_has_a_created_timestamp()
	{
		$this->created->shouldBe(strtotime('april 26, 2014'));
	}

	function is_has_a_updated_timestamp()
	{
		$this->updated->shouldBe(strtotime('april 27, 2014 3:00pm'));
	}

	function it_can_set_data_from_an_array()
	{
		// ArrayExchange trait
		$this->setData(array('username' => 'hello'))->shouldReturn($this);
		$this->username->shouldBe('hello');
	}

	function it_can_be_converted_to_an_array()
	{
		// ArrayExchange trait
		$this->asArray()->shouldHaveKey('id');
	}

}
