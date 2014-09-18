<?php

namespace spec\Ushahidi\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PostSpec extends ObjectBehavior
{
	function let()
	{
		$this->beConstructedWith(array(
			'id'              => 1,
			'parent_id'       => 0,
			'form_id'         => 2,
			'user_id'         => 3,
			'type'            => 'report',
			'title'           => 'A Test Report',
			'slug'            => '2014-05-09-a-test-report',
			'content'         => "A test report by\nBob",
			'author_email'    => "test@ushahidi.com",
			'author_realname' => "Test User",
			'status'          => 'draft',
			'created'         => strtotime('may 9, 2014'),
			'updated'         => strtotime('may 10, 2014'),
			'locale'          => 'en-us',
			));
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Entity\Post');
	}

	function it_has_constructed_properties()
	{
		$this->id->shouldBe(1);
		$this->parent_id->shouldBe(0);
		$this->form_id->shouldBe(2);
		$this->user_id->shouldBe(3);
		$this->title->shouldBe('A Test Report');
		$this->slug->shouldBe('2014-05-09-a-test-report');
		$this->content->shouldBe("A test report by\nBob");
		$this->author_email->shouldBe("test@ushahidi.com");
		$this->author_realname->shouldBe("Test User");
		$this->status->shouldBe('draft');
		$this->created->shouldBe(strtotime('may 9, 2014'));
		$this->updated->shouldBe(strtotime('may 10, 2014'));
		$this->locale->shouldBe('en-us');
	}
}
