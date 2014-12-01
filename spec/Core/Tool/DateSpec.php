<?php

namespace spec\Ushahidi\Core\Tool;

use Ushahidi\Core\Entity\ConfigRepository;
use Ushahidi\Core\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DateSpec extends ObjectBehavior
{
	function let(ConfigRepository $repo)
	{
		// Normally this is handled by the application, but spec tests are run
		// outside of the application.
		date_default_timezone_set('UTC');

		$this->beConstructedWith($repo);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Core\Tool\Date');
	}

	function it_fetches_the_date_format_from_site_settings($repo, Entity $config)
	{
		$config->date_format = 'Y/m/d';
		$repo->get('site')->willReturn($config);
		$this->getDateFormat()->shouldReturn($config->date_format);
	}

	function it_converts_a_date_to_a_timestamp_with_defaults($repo, Entity $config)
	{
		$config->date_format = 'n/j/Y H:i'; // MM/DD/YYYY
		$repo->get('site')->willReturn($config);

		$this->getTimestampFromString('12/01/2014 00:00')->shouldReturn(1417392000);
	}

	function it_converts_a_european_date_to_a_timestamp()
	{
		$euro = 'j/n/Y H:i'; // DD/MM/YYYY
		$this->getTimestampFromString('01/12/2014 00:00', $euro)->shouldReturn(1417392000);
	}

	function it_adds_timestamps_to_results()
	{
		$results = [
			['date' => '13/12/2014 00:00'],
			['date' => '14/12/2014 02:00'],
		];

		$expected = [
			['date' => '13/12/2014 00:00', 'ts' => 1418428800],
			['date' => '14/12/2014 02:00', 'ts' => 1418522400],
		];

		$euro = 'j/n/Y H:i'; // DD/MM/YYYY

		$this->addTimestampToResults($results, 'date', 'ts', $euro)->shouldReturn($expected);
	}
}
