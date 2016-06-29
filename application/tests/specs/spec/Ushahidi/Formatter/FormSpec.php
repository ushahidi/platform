<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Ushahidi\Core\Entity\Form;
use Ushahidi\Core\Tool\Authorizer;

class Ushahidi_Formatter_FormSpec extends ObjectBehavior
{

	function let(Authorizer $auth)
	{
		$this->setAuth($auth);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi_Formatter_Form');
	}

	function it_should_format_color()
	{
		$this->__invoke(new Form(['color'=> '#aabbcc']))->shouldHaveKeyWithValue('color', '#aabbcc');
		$this->__invoke(new Form(['color'=> 'aabbcc']))->shouldHaveKeyWithValue('color', '#aabbcc');
	}
}
