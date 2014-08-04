<?php

namespace spec\Ushahidi\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TagSpec extends ObjectBehavior
{
	function let()
	{
		$this->beConstructedWith(array(
			'id'          => 1,
			'parent_id'   => 0,
			'tag'         => 'Spec Test',
			'slug'        => 'spec-test',
			'type'        => 'category',
			'color'       => '#911',
			'description' => 'Bright red spec tests',
			'priority'    => 10,
			'created'     => strtotime('may 9, 2014'),
			'role'        => 'user',
			));
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Entity\Tag');
	}

	function it_has_an_id()
	{
		$this->id->shouldBe(1);
	}
	
	function it_has_a_parent_id()
	{
		$this->parent_id->shouldBe(0);
	}

	function it_has_a_tag()
	{
		$this->tag->shouldBe('Spec Test');
	}

	function it_has_a_slug()
	{
		$this->slug->shouldBe('spec-test');
	}

	function it_has_a_type()
	{
		$this->type->shouldBe('category');
	}
	
	function it_has_a_color()
	{
		$this->color->shouldBe('#911');
	}

	function it_has_a_description()
	{
		$this->description->shouldBe('Bright red spec tests');
	}

	function it_has_a_priority()
	{
		$this->priority->shouldBe(10);
	}

	function it_has_a_created_timestamp()
	{
		$this->created->shouldBe(strtotime('may 9, 2014'));
	}
	
	function it_has_a_role()
	{
		$this->role->shouldBe('user');
	}

}
