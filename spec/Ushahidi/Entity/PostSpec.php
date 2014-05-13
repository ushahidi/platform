<?php

namespace spec\Ushahidi\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PostSpec extends ObjectBehavior
{
	function let()
	{
		$this->beConstructedWith(array(
			'id'        => 1,
			'parent_id' => 0,
			'form_id'   => 2,
			'user_id'   => 3,
			'type'      => 'report',
			'title'     => 'A Test Report',
			'slug'      => '2014-05-09-a-test-report',
			'content'   => "A test report by\nBob",
			'status'    => 'draft',
			'created'   => strtotime('may 9, 2014'),
			'updated'   => strtotime('may 10, 2014'),
			'locale'    => 'en-us',
			));
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Entity\Post');
	}

	function it_has_an_id()
	{
		$this->id->shouldBe(1);
	}

	function it_has_a_parent_id()
	{
		$this->parent_id->shouldBe(0);
	}

	function it_has_a_form_id()
	{
		$this->form_id->shouldBe(2);
	}

	function it_has_an_user_id()
	{
		$this->user_id->shouldBe(3);
	}

	function it_has_a_title()
	{
		$this->title->shouldBe('A Test Report');
	}

	function it_has_a_slug()
	{
		$this->slug->shouldBe('2014-05-09-a-test-report');
	}

	function it_has_content()
	{
		$this->content->shouldBe("A test report by\nBob");
	}

	function it_has_a_status()
	{
		$this->status->shouldBe('draft');
	}

	function it_has_a_created_timestamp()
	{
		$this->created->shouldBe(strtotime('may 9, 2014'));
	}

	function it_has_an_updated_timestamp()
	{
		$this->updated->shouldBe(strtotime('may 10, 2014'));
	}

	function it_has_a_locale()
	{
		$this->locale->shouldBe('en-us');
	}
}
