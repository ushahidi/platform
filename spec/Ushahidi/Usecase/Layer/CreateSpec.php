<?php

namespace spec\Ushahidi\Usecase\Layer;

use Ushahidi\Tool\Authorizer;
use Ushahidi\Tool\Validator;
use Ushahidi\Entity\Layer;
use Ushahidi\Usecase\Layer\CreateLayerRepository;
use Ushahidi\Usecase\Layer\LayerData;

use PhpSpec\ObjectBehavior;

class CreateSpec extends ObjectBehavior
{
	function let(CreateLayerRepository $repo, Validator $valid, Authorizer $auth)
	{
		$this->beConstructedWith($repo, $valid, $auth);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\Layer\Create');
	}

	function it_fails_with_invalid_input($valid, $auth, LayerData $input)
	{
		$input->name                  = '';
		$input->data_url              = '';
		$input->type                  = '';
		$input->options               = [];

		$valid->check($input)->willReturn(false);
		$valid->errors()->willReturn([]);
		$this->shouldThrow('Ushahidi\Exception\ValidatorException')->duringInteract($input);
	}

	function it_fails_when_auth_not_allowed($valid, $auth, LayerData $input)
	{
		$input->name                  = 'Test';
		$input->data_url              = '/media/test.geojson';
		$input->type                  = 'geojson';
		$input->options               = [];
		$input->active                = true;
		$input->visible_by_default    = true;

		$raw_input = [
			'name'                  => 'Test',
			'data_url'              => '/media/test.geojson',
			'type'                  => 'geojson',
			'options'               => [],
			'active'                => true,
			'visible_by_default'    => true
		];

		$input->asArray()->willReturn($raw_input);

		$valid->check($input)->willReturn(true);

		$auth->isAllowed(new Layer, 'create')->willReturn(false);
		$auth->getUserId()->willReturn(1);
		$this->shouldThrow('Ushahidi\Exception\AuthorizerException')->duringInteract($input);
	}

	function it_can_create_a_layer_with_valid_input($valid, $repo, $auth, LayerData $input)
	{
		$input->name                  = 'Test';
		$input->data_url              = '/media/test.geojson';
		$input->type                  = 'geojson';
		$input->options               = [];
		$input->active                = true;
		$input->visible_by_default    = true;

		$raw_input = [
			'name'                  => 'Test',
			'data_url'              => '/media/test.geojson',
			'type'                  => 'geojson',
			'options'               => [],
			'active'                => true,
			'visible_by_default'    => true
		];

		$input->asArray()->willReturn($raw_input);

		$valid->check($input)->willReturn(true);

		$auth->isAllowed(new Layer, 'create')->willReturn(true);

		$repo->createLayer($raw_input)->willReturn(new Layer);

		$this->interact($input)->shouldReturnAnInstanceOf('Ushahidi\Entity\Layer');
	}
}
