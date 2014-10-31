<?php

namespace spec\Ushahidi\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConfigSpec extends ObjectBehavior
{
	function let()
	{
		$this->beConstructedWith(
			[
				'id'     => 'testing',
				'fruit'  => 'banana',
				'yogurt' => 'vanilla',
				'pizza'  => 'pepperoni',
			]
		);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Entity\Config');
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
	}
}
