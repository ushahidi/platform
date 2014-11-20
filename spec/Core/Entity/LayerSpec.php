<?php

namespace spec\Ushahidi\Core\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LayerSpec extends ObjectBehavior
{
	public function let()
	{
		$this->beConstructedWith(array(
			'id' => 1,
			'media_id' => 7,
			'name' => 'Bars',
			'data_url' => 'http://example.com/bars.json',
			'type' => 'geojson',
			'active' => true,
			'visible_by_default' => true,
			'created' => strtotime('july 8, 2014'),
			'updated' => strtotime('july 9, 2014')
		));
	}

	public function it_is_initializable()
	{
			$this->shouldHaveType('Ushahidi\Core\Entity\Layer');
	}

	public function it_has_constructed_properties()
	{
		$this->id->shouldBe(1);
		$this->media_id->shouldBe(7);
		$this->name->shouldBe('Bars');
		$this->data_url->shouldBe('http://example.com/bars.json');
		$this->type->shouldBe('geojson');
		$this->active->shouldBe(true);
		$this->visible_by_default->shouldBe(true);
		$this->created->shouldBe(strtotime('july 8, 2014'));
		$this->updated->shouldBe(strtotime('july 9, 2014'));
	}
}
