<?php

/**
 * Ushahidi Api Endpoint
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Api
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace UshahidiApi;

use Ushahidi\Tool\Parser;
use Ushahidi\Tool\Formatter;
use Ushahidi\Usecase;

class Endpoint
{
	public function __construct(
		Parser $parser,
		Formatter $formatter,
		Usecase $usecase
	) {
		$this->parser = $parser;
		$this->formatter = $formatter;
		$this->usecase = $usecase;
	}

	public function run(Array $request)
	{
		$input = $this->parser->__invoke($request);

		$result = $this->usecase->interact($input);

		$output = $this->formatter->__invoke($result);

		return $output;
	}

	public function __call($method, Array $args = null)
	{
		return call_user_func_array([$this->usecase, $method], $args);
	}
}
