<?php

namespace spec\Ushahidi\Api;

use Ushahidi\Core\Usecase;
use Ushahidi\Core\Data;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Tool\Parser;
use Ushahidi\Core\Tool\Formatter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EndpointSpec extends ObjectBehavior
{
	function let(
		Authorizer $auth,
		Formatter $formatter,
		Parser $parser,
		Usecase $usecase,
		Entity $resource
	) {
		$this->beConstructedWith(compact(
			'auth',
			'parser',
			'formatter',
			'usecase',
			'resource'
		));
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Api\Endpoint');
	}

	function it_does_run_the_parser_usecase_formatter_sequence(Data $input, $auth, $parser, $formatter, $usecase, $resource)
	{
		$request = ['input'];
		$result  = ['entities'];
		$output  = ['formatted'];

		$auth->isAllowed($resource, 'read')->willReturn(true);

		$parser->__invoke($request)->willReturn($input);

		$usecase->interact($input)->willReturn($result);

		$formatter->__invoke($result)->willReturn($output);

		$this->run($request)->shouldReturn($output);
	}
}
