<?php

namespace spec\Ushahidi\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PostValueSpec extends ObjectBehavior
{
	function let()
	{
		$this->beConstructedWith(array(
			'id'      => 1,
			'post_id' => 7,
			'form_attribute_id' => 2,
			'value'   => 'A test field value',
			'created' => strtotime('july 8, 2014'),
		));
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Entity\PostValue');
	}

	function it_has_constructed_properties()
	{
		$this->id->shouldBe(1);
		$this->post_id->shouldBe(7);
		$this->form_attribute_id->shouldBe(2);
		$this->value->shouldBe('A test field value');
		$this->created->shouldBe(strtotime('july 8, 2014'));
	}
}
