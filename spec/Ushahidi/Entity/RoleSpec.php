<?php

namespace spec\Ushahidi\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RoleSpec extends ObjectBehavior
{
	function let()
	{
		$this->beConstructedWith(array(
			'name' => 'test',
			'display_name' => 'Test Role',
			'description' => 'Role used for testing',
			));
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Entity\Role');
	}

	function it_has_a_name()
	{
		$this->name->shouldBe('test');
	}

	function it_has_a_display_name()
	{
		$this->display_name->shouldBe('Test Role');
	}

	function it_has_a_description()
	{
		$this->description->shouldBe('Role used for testing');
	}
}
