<?php

namespace spec\Ushahidi\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FormAttributeSpec extends ObjectBehavior
{

	function let()
	{
		$this->beConstructedWith(array(
			'id'          => 1,
			'key'         => 'test_varchar',
			'label'       => 'Test varchar',
			'input'       => 'text',
			'type'        => 'varchar',
			'required'    => TRUE,
			'default'     => 'blah',
			'priority'    => 10,
			'options'     => ['option1', 'option2'],
			'cardinality' => 2
		));
	}

  function it_is_initializable()
  {
      $this->shouldHaveType('Ushahidi\Entity\FormAttribute');
  }

	function it_has_constructed_properties()
	{
		$this->id->shouldBe(1);
		$this->key->shouldBe('test_varchar');
		$this->label->shouldBe('Test varchar');
		$this->input->shouldBe('text');
		$this->type->shouldBe('varchar');
		$this->required->shouldBe(TRUE);
		$this->default->shouldBe('blah');
		$this->priority->shouldBe(10);
		$this->options->shouldBe(['option1', 'option2']);
		$this->cardinality->shouldBe(2);
	}
}
