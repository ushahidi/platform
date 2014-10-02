<?php

namespace spec\Ushahidi\Usecase\Layer;

use Ushahidi\Tool\Authorizer;
use Ushahidi\Tool\Validator;
use Ushahidi\Entity\Layer;
use Ushahidi\Usecase\Layer\UpdateLayerRepository;
use Ushahidi\Usecase\Layer\LayerData;

use PhpSpec\ObjectBehavior;

class UpdateSpec extends ObjectBehavior
{
	function let(UpdateLayerRepository $repo, Validator $valid, Authorizer $auth)
	{
		$this->beConstructedWith($repo, $valid, $auth);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\Layer\Update');
	}

	function it_can_update_a_layer_with_valid_input($valid, $repo, $auth, Layer $layer, LayerData $input, LayerData $update)
	{
		$raw_layer  = ['name' => 'Before Update', 'data_url' => '/media/test.geojson'];
		$raw_input  = ['name' => 'After Update', 'data_url' => '/media/after.geojson'];
		$raw_update = ['name' => 'After Update'];

		$layer->id = 1;
		$layer->name = 'Before Update';
		$layer->data_url = '/media/test.geojson';
		$layer->type = 'geojson';

		$repo->get($input->id)->willReturn($layer);

		$layer->asArray()->willReturn($raw_layer);
		$input->asArray()->willReturn($raw_input);
		$update->asArray()->willReturn($raw_update);

		// the update will be what is different in the input, as compared to the layer
		$input->getDifferent($raw_layer)->willReturn($update);

		// only the changed values will be validated
		$valid->check($update)->willReturn(true);

		// authorization will be checked
		$auth->isAllowed($layer, 'update')->willReturn(true);

		// the repo will only receive changed values
		$repo->updateLayer($layer->id, $raw_update)->shouldBeCalled();

		// the persisted changes will be applied to the layer
		$layer->setData($raw_update)->shouldBeCalled();

		// after being updated, the same layer will be returned
		$this->interact($input)->shouldReturn($layer);
	}
}
