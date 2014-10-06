<?php

namespace spec\UshahidiApi;

use Ushahidi\Usecase;
use Ushahidi\Data;
use Ushahidi\Entity;
use Ushahidi\Tool\Authorizer;
use Ushahidi\Tool\Parser;
use Ushahidi\Tool\Formatter;

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
		$this->shouldHaveType('UshahidiApi\Endpoint');
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
