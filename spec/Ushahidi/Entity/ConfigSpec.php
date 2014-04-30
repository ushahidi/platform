<?php

namespace spec\Ushahidi\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConfigSpec extends ObjectBehavior
{
	function let()
	{
		$this->beConstructedWith(array(
			'fruit'  => 'banana',
			'yogurt' => 'vanilla',
			'pizza'  => 'pepperoni',
			),
			'testing'
			);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Entity\Config');
	}

	function it_has_a_group_key()
	{
		// This is actually controlled via the constant GROUP_KEY, but I am not
		// sure how to test for it.
		$this->{'@group'}->shouldBe('testing');
	}

	function it_can_set_the_group()
	{
		// This should also be using the constant...
		$this->setGroup('test_group')->shouldReturn($this);
		$this->{'@group'}->shouldBe('test_group');
	}

	function it_can_set_data_from_an_array()
	{
		// ArrayExchange trait
		$this->setData(array('pizza' => 'hawaiian'))->shouldReturn($this);
		$this->pizza->shouldBe('hawaiian');
	}

	function it_can_be_converted_to_an_array()
	{
		// ArrayExchange trait
		$this->asArray()->shouldHaveKey('pizza');
		$this->asArray()->shouldHaveKey('yogurt');
		$this->asArray()->shouldHaveKey('fruit');
		// But never contains the group key
		$this->asArray()->shouldNotHaveKey('@group');
	}

}
