<?php

namespace spec\UshahidiApi;

use Ushahidi\Usecase;
use Ushahidi\Data;
use Ushahidi\Tool\Parser;
use Ushahidi\Tool\Formatter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EndpointSpec extends ObjectBehavior
{
	function let(Parser $parser, Formatter $formatter, Usecase $usecase)
	{
		$this->beConstructedWith($parser, $formatter, $usecase);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('UshahidiApi\Endpoint');
	}

	function it_does_run_the_parser_usecase_formatter_sequence(Data $input, $parser, $formatter, $usecase)
	{
		$request = ['input'];
		$result  = ['entities'];
		$output  = ['formatted'];

		$parser->__invoke($request)->willReturn($input);

		$usecase->interact($input)->willReturn($result);

		$formatter->__invoke($result)->willReturn($output);

		$this->run($request)->shouldReturn($output);
	}
}
